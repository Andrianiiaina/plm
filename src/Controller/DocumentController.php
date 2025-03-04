<?php

namespace App\Controller;

use App\Entity\Document;
use App\Form\DocumentType;
use App\Repository\DocumentRepository;
use App\Service\FileUploaderService;
use App\Service\ListService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use function PHPUnit\Framework\isEmpty;

#[Route('/document')]
final class DocumentController extends AbstractController
{
    #[Route(name: 'app_document_index', methods: ['GET','POST'])]
    public function index(Request $request, 
    EntityManagerInterface $entityManager, 
    FileUploaderService $fileUploader): Response
    {
        $document = new Document();
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $brochureDocument = $form['filepath']->getData();
            if ($brochureDocument) {
                $newFilename = $fileUploader->upload($brochureDocument,"documents");
                try {
                    $document->setFilename($brochureDocument->getClientOriginalName());
                    $document->setFilepath($newFilename);
                    $entityManager->persist($document);
                    $entityManager->flush(); 
                    $this->addFlash('success','Document enregistré!' );
                } catch (FileException $e) {
                    $this->addFlash('error', "Erreur! Le document n'a pas pu etre enregistré.");
                   
                }
            }
            return $this->redirectToRoute('app_document_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            $documents= $entityManager->getRepository(Document::class)->findAll();

         }elseif($this->isGranted('ROLE_RESPO')){
           $documents=$entityManager->getRepository(Document::class)->findDocs($this->getUser());
          
         }else{
            
            $documents=[];
         }

         $grouped_documents_by_status = [];
         foreach (ListService::$document_status as $status) {
                $grouped_documents_by_status[$status]=[];
        }
         foreach ($documents as $document) {
             $status = $document->getStatus(); 
             $grouped_documents_by_status[$status][] = $document;
         }
        return $this->render('document/index.html.twig', [
            'groupedDocuments' => $grouped_documents_by_status,
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
    public function edit(Request $request, Document $document, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
            try {
                $entityManager->flush();
                $this->addFlash('success', 'Document bien modifié!' );
            } catch (Exception $e) {
                $this->addFlash('error', "Erreur! la modification du document a échoué.");
            }
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
