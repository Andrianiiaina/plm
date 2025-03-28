<?php

namespace App\Controller;

use App\Entity\Document;
use App\Entity\Tender;
use App\Event\UserAssignedToProjectEvent;
use App\Form\DocumentType;
use App\Service\FileUploaderService;
use App\Service\ListService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/document')]
final class DocumentController extends AbstractController
{
    #[Route(name: 'app_document_index', methods: ['GET'])]
    public function index(
    EntityManagerInterface $entityManager, 
    ): Response
    {
        $documents=$this->isGranted('ROLE_ADMIN')? $entityManager->getRepository(Document::class)->findBy([], ['createdAt' => 'DESC']):$entityManager->getRepository(Document::class)->findDocs($this->getUser());
        $grouped_documents_by_status = array_fill_keys(ListService::$document_status, []);

        foreach ($documents as $document) {
            $grouped_documents_by_status[$document->getStatus()][] = $document;
        }

        return $this->render('document/index.html.twig', [ 'groupedDocuments' => $grouped_documents_by_status]);
    }

    #[Route('/tender/show/{id}', name: 'app_document_tender', methods: ['GET','POST'])]
    public function document_tender(    Tender $tender,
    Request $request, 
    EntityManagerInterface $entityManager, 
    FileUploaderService $fileUploader,
    EventDispatcherInterface $dispatcher): Response
    
    {
        $document = new Document();
        $form = $this->createForm(DocumentType::class, $document,['user'=>$this->getUser()]);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if($form->isValid()==false){
                $this->addFlash('error','Un problème est survenu, réessayé!' );
            }else{
            $brochureDocument = $form['filepath']->getData();
                if ($brochureDocument) {
                    $newFilename = $fileUploader->upload($brochureDocument,"tender_documents");
                    try {
                        $document->setFilename($brochureDocument->getClientOriginalName());
                        $document->setFilepath($newFilename);
                        $document->setTender($tender);
                        $entityManager->persist($document);
                        $entityManager->flush(); 
                        $dispatcher->dispatch(new UserAssignedToProjectEvent($document->getResponsable(),$document->getId(),2));
                        
                        $this->addFlash('success','Document enregistré!' );
                    } catch (FileException $e) {
                        $this->addFlash('error', "Erreur! Le document n'a pas pu etre enregistré.");
                    
                    }
                }

                
            }
            
            return $this->redirectToRoute('app_document_tender', ['id'=>$tender->getId()], Response::HTTP_SEE_OTHER);
        }

        $documents=$entityManager->getRepository(Document::class)->findTenderDocuments($tender);
        $grouped_documents_by_status = array_fill_keys(ListService::$document_status, []);


        foreach ($documents as $document) {
            $grouped_documents_by_status[$document->getStatus()][] = $document;
        }

        return $this->render('document/documents.html.twig',
         [ 'groupedDocuments' => $grouped_documents_by_status,'tender'=>$tender,
         'form'=>$form]);
    }
    #[Route('/show/{id}', name: 'app_document_show', methods: ['GET'])]
    public function show(Document $document): Response
    {
        return $this->render('document/show.html.twig', [
            'document' => $document,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_document_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, Document $document, 
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $dispatcher,
        FileUploaderService $fileUploader,
        ): Response
    {
        $form = $this->createForm(DocumentType::class, $document,['is_edited'=>true,'user'=>$this->getUser()]);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if($form->isValid()==false){
                $this->addFlash('error','Un problème est survenu, réessayé!' );
            }else{
                $brochureDocument = $form['filepath']->getData();
                if ($brochureDocument) {
                    $newFilename = $fileUploader->upload($brochureDocument,"tender_documents");
                    try {
                        $document->setFilename($brochureDocument->getClientOriginalName());
                        $document->setFilepath($newFilename);
                    } catch (FileException $e) {
                        $this->addFlash('error', "Erreur lors du mis à jour du fichier.");
                    }
                }
                $entityManager->persist($document);
                $entityManager->flush(); 
                $dispatcher->dispatch(new UserAssignedToProjectEvent($document->getResponsable(),$document->getId(),2));
                        
                $this->addFlash('success','Document modifié!' );
            }
            return $this->redirectToRoute('app_document_tender', ['id'=>$document->getTender()->getId()], Response::HTTP_SEE_OTHER);
        }
 
        return $this->render('document/edit.html.twig', [
            'document' => $document,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_document_delete', methods: ['POST'])]
    public function delete(
    Request $request, 
    Document $document, 
    EntityManagerInterface $entityManager,
    FileUploaderService $fileUploader): Response
    {
        $tender_id=$document->getTender()->getId();
        if ($this->isCsrfTokenValid('delete'.$document->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($document);
            $fileUploader->removeFile('tender_documents',$document->getFilepath());
            $entityManager->flush();
            $this->addFlash('success','Document supprimé!' );
        }

        return $this->redirectToRoute('app_document_tender', ['id'=>$tender_id], Response::HTTP_SEE_OTHER);
    }



    #[Route('/update_status', name: 'update_status', methods: ['POST'])]
    public function updateOrder(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

       
        if (!$data) {
            return $this->json(['error' => 'Aucune donnée reçue'], 400);
        }

        $document=$em->getRepository(Document::class)->findOneBy(['id'=>$data['document_id']]);
        $document->setStatus($data['status_id']);
        $em->persist($document);
        $em->flush();
        
        return $this->json([
            'message' => 'JSON bien reçu',
            'data' => $data
        ]);
    }
}




