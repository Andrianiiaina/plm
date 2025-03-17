<?php

namespace App\Controller;

use App\Entity\Document;
use App\Event\UserAssignedToProjectEvent;
use App\Form\DocumentType;
use App\Service\FileUploaderService;
use App\Service\ListService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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



    #[Route('/new', name: 'app_document_new', methods: ['GET','POST'])]
    public function new(
    Request $request, 
    EntityManagerInterface $entityManager, 
    FileUploaderService $fileUploader,
    EventDispatcherInterface $dispatcher): Response
    {
        $document = new Document();
        $form = $this->createForm(DocumentType::class, $document,['user'=>$this->getUser()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $brochureDocument = $form['filepath']->getData();
            if ($brochureDocument) {
                $newFilename = $fileUploader->upload($brochureDocument,"tender_documents");
                try {
                    $document->setFilename($brochureDocument->getClientOriginalName());
                    $document->setFilepath($newFilename);
                    $entityManager->persist($document);
                    $entityManager->flush(); 
                    $dispatcher->dispatch(new UserAssignedToProjectEvent($document->getResponsable(),$document->getId(),2));
                    
                    $this->addFlash('success','Document enregistré!' );
                } catch (FileException $e) {
                    $this->addFlash('error', "Erreur! Le document n'a pas pu etre enregistré.");
                   
                }
            }
            return $this->redirectToRoute('app_document_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('document/new.html.twig', [
            'form'=>$form,
        ]);
    }

    #[Route('/{id}', name: 'app_document_show', methods: ['GET'])]
    public function show(Document $document): Response
    {
        return $this->render('document/show.html.twig', [
            'document' => $document,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_document_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, Document $document, 
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $dispatcher
        ): Response
    {
        $form = $this->createForm(DocumentType::class, $document,['is_edited'=>true,'user'=>$this->getUser()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $dispatcher->dispatch(new UserAssignedToProjectEvent($document->getResponsable(),$document->getId(),2));
            
            $this->addFlash('success', 'Document bien modifié!' );  
            return $this->redirectToRoute('app_document_index', [], Response::HTTP_SEE_OTHER);
        }


        return $this->render('document/edit.html.twig', [
            'document' => $document,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_document_delete', methods: ['POST'])]
    public function delete(Request $request, Document $document, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$document->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($document);
            $entityManager->flush();
            $this->addFlash('success','Document supprimé!' );
        }

        return $this->redirectToRoute('app_document_index', [], Response::HTTP_SEE_OTHER);
    }



}
