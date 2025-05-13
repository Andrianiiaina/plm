<?php

namespace App\Controller;

use App\Entity\Document;
use App\Entity\Tender;
use App\Event\UserAssignedToEntityEvent;
use App\Form\DocumentType;
use App\Repository\DocumentRepository;
use App\Service\FileUploaderService;
use App\Service\ListService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
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
    public function index(DocumentRepository $documentRepository,Request $request,PaginatorInterface $paginator): Response
    {
        $searchTerm = $request->query->get('q','');
       
        $pagination = $paginator->paginate(
            $documentRepository->search($searchTerm,$this->getUser()),
             $request->query->getInt('page', 1),
              15);
            $searchTerm="";
            
        return $this->render('document/index.html.twig', [ 
            'documents'=> $pagination,
            'searchTerm'=>$searchTerm??'',
        ]);
    }

    

    #[Route('/tender/show/{id}', name: 'app_tender_documents', methods: ['GET','POST'])]
    public function tender_documents(Tender $tender,
    Request $request, 
    EntityManagerInterface $entityManager, 
    FileUploaderService $fileUploader,
    EventDispatcherInterface $dispatcher): Response
    
    {
        $document = new Document();
        $form = $this->createForm(DocumentType::class, $document,['user'=>$this->getUser()]);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if(!$form->isValid()){
                $this->addFlash('error', 'Erreur. Veuillez revérifier les informations.');
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
                        $dispatcher->dispatch(new UserAssignedToEntityEvent($document->getResponsable(),$document->getId(),2));
                        
                        $this->addFlash('success','Document enregistré ! ' );
                    } catch (FileException $e) {
                        $this->addFlash('error', "Erreur! Veuillez revérifier les informations.");
                    
                    }
                }

                
            }
        }
            return $this->redirectToRoute('app_tender_show', ['id'=>$tender->getId()], Response::HTTP_SEE_OTHER);
        

       
    }


    #[Route('/show/{id}', name: 'app_document_show', methods: ['GET'])]
    public function show(Document $document): Response
    {   $ext=mime_content_type("uploads/tender_documents/".$document->getFilepath());

        return $this->render('document/show.html.twig', [
            'document' => $document,
            'is_pdf'=>($ext=="application/pdf")?true:false,
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
            if(!$form->isValid()){
                $this->addFlash('error', 'Un problème est survenu, veuillez réessayer.');
            }else{
                $brochureDocument = $form['filepath']->getData();
                if ($brochureDocument) {
                    $newFilename = $fileUploader->upload($brochureDocument,"tender_documents");
                    try {
                        $document->setFilename($brochureDocument->getClientOriginalName());
                        $document->setFilepath($newFilename);
                    } catch (FileException $e) {
                        $this->addFlash('error', "Erreur lors de la modification du document, veuillez réessayer.");
                    }
                }
                $entityManager->persist($document);
                $entityManager->flush(); 
                $dispatcher->dispatch(new UserAssignedToEntityEvent($document->getResponsable(),$document->getId(),2));
                        
                $this->addFlash('success','Document modifié ! ' );
            }
            return $this->redirectToRoute('app_tender_show', ['id'=>$document->getTender()->getId()], Response::HTTP_SEE_OTHER);
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
            $fileUploader->removeFileFromStorage('tender_documents',$document->getFilepath());
            $entityManager->flush();
            $this->addFlash('success','Document supprimé ! ' );
        }

        return $this->redirectToRoute('app_tender_show', ['id'=>$tender_id], Response::HTTP_SEE_OTHER);
    }



    #[Route('/api_update_status', name: 'api_update_status', methods: ['POST'])]
    public function updateStatusInSortableFunc(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

       
        if (!$data) {
            return $this->json(['error' => 'Aucune donnée reçue'], 400);
        }

        $document=$em->getRepository(Document::class)->findOneBy(['id'=>$data['document_id']]);
        $document->setStatus($data['status_id']);
        $em->flush();
        
        return $this->json([
            'message' => 'JSON bien reçu',
            'data' => $data
        ]);
    }
    #[Route('/archive/{id}', name: 'app_document_archive', methods: ['POST'])]
    public function archive_or_reset_document(Request $request,Document $document,EntityManagerInterface $entityManager): Response
    {
        $tender_id=$document->getTender()->getId();
        if ($this->isCsrfTokenValid('archive'.$document->getId(), $request->getPayload()->getString('_token'))) {
            $document->setIsArchived(!$document->isArchived());
            $entityManager->flush();
            $document->isArchived()?$this->addFlash('success','Document archivé ! '):$this->addFlash('success','Document restauré ! ') ;
            
        }else{
            $this->addFlash('error','L\'Opération a échoué. Veuillez réessayer.');
        }
        return $this->redirectToRoute('app_tender_show', ['id'=>$tender_id]);
    }
}




