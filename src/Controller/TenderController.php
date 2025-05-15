<?php

namespace App\Controller;

use App\Entity\Allotissement;
use App\Entity\Contact;
use App\Entity\Document;
use App\Entity\File;
use App\Entity\Tender;
use App\Entity\TenderDate;
use App\Event\UserAssignedToEntityEvent;
use App\Form\AllotissementType;
use App\Form\ContactType;
use App\Form\DocumentType;
use App\Form\FileType;
use App\Form\StatusType;
use App\Form\TenderDateType;
use App\Form\TenderType;
use App\Repository\TenderRepository;
use App\Service\ListService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/tender')]
final class TenderController extends AbstractController
{
    #[Route(name: 'app_tender_index', methods: ['GET'])]
    public function index(TenderRepository $tenderRepository, Request $request,PaginatorInterface $paginator): Response
    {

        $searchTerm = $request->query->get('q','');
        $tenders = $this->isGranted('ROLE_ADMIN') 
            ? $tenderRepository->search($searchTerm) 
            : $tenderRepository->searchTenderRespo($searchTerm, $this->getUser());

        $pagination = $paginator->paginate($tenders, $request->query->getInt('page', 1), 10);
      
       
        $statistiques=$this->isGranted('ROLE_ADMIN')?$tenderRepository->findAllStatistic():$tenderRepository->findRespoStatistic($this->getUser());
        return $this->render('tender/index.html.twig', [
            'tenders' => $pagination,
            'total_tenders'=>count($tenders),
            'searchTerm' => $searchTerm,
            'total_tender_by_status'=>$statistiques,
        ]);

    }

    #[Route('/new', name: 'app_tender_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, EventDispatcherInterface $dispatcher): Response
    {
        $tender = new Tender();
        $form = $this->createForm(TenderType::class, $tender);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tender);
                $entityManager->flush();
                $dispatcher->dispatch(new UserAssignedToEntityEvent($tender->getResponsable(),$tender->getId(),1));
                
                $this->addFlash('success','Tender enregistré ! ' );
            return $this->redirectToRoute('app_tender_edit_date', ['id'=>$tender->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tender/new.html.twig', [
            'form' => $form,
        ]);
    }


    #[Route('/edit_date/{id}', name: 'app_tender_edit_date', methods: ['GET', 'POST'])]
    public function edit_date(Request $request, Tender $tender, EntityManagerInterface $entityManager): Response
    {
        $tenderDate=$tender->getTenderDate()? $tender->getTenderDate() : new TenderDate();
        $form_date = $this->createForm(TenderDateType::class, $tenderDate);
        $form_date->handleRequest($request);

        if ($form_date->isSubmitted() && $form_date->isValid()) {
            $tenderDate->setTender($tender);
            $entityManager->persist($tenderDate);
            $entityManager->flush();
            $this->addFlash('success','Information sur les dates enregistrée' );
            if($tender->getContact()){
                return $this->redirectToRoute('app_tender_show', ['id'=>$tender->getId()], Response::HTTP_SEE_OTHER);
            }
            return $this->redirectToRoute('app_tender_edit_organisation_contact', ['id'=>$tender->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tender/components/_date_form.html.twig', [
            'tender' => $tender,
            'form' => $form_date,
        ]);
    }



    #[Route('/edit_organisation_contact/{id}', name: 'app_tender_edit_organisation_contact', methods: ['GET', 'POST'])]
    public function edit_organisation_contact(Request $request, Tender $tender, EntityManagerInterface $entityManager): Response
    {
        
        $contact =$tender->getContact()? $tender->getContact():new Contact();
        $form_contact = $this->createForm(ContactType::class, $contact);
        $form_contact->handleRequest($request);
        if ($form_contact->isSubmitted() && $form_contact->isValid()) {
            $entityManager->persist($contact);
            $entityManager->flush();
            $tender->setContact($contact);
            $entityManager->flush();
            $this->addFlash('success',"Information sur l'organisation enregistrée!" );
            return $this->redirectToRoute('app_tender_show', ['id'=>$tender->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tender/components/_contact_form.html.twig', [
            'tender' => $tender,
            'form' => $form_contact,
        ]);
    }

    #[Route('/edit_basic_info/{id}/', name: 'app_tender_edit_informations', methods: ['GET', 'POST'])]
    #[IsGranted('operation', 'tender', 'Page not found', 404)]
    public function edit_basic_informations(Request $request, Tender $tender, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TenderType::class, $tender);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success','Tender modifié ! ' );
            return $this->redirectToRoute('app_tender_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tender/edit.html.twig', [
            'tender' => $tender,
            'form' => $form,
        ]);
    }

    #[Route('/show/{id}', name: 'app_tender_show', methods: ['GET','POST'])]
    #[IsGranted('operation', 'tender', 'Page not found', 404)]
    public function show_tender(Tender $tender, Request $request, EntityManagerInterface $entityManager): Response
    {

        $form_status = $this->createForm(StatusType::class, $tender);
        $form_status->handleRequest($request);
        $tenderDate=$entityManager->getRepository(TenderDate::class)->findOneBy(['tender'=>$tender]);



        if ($form_status->isSubmitted() && $form_status->isValid()) {
            $entityManager->flush();
            $this->addFlash('success','status modifié ! ' );
            return $this->redirectToRoute('app_tender_show', ['id'=>$tender->getId()], Response::HTTP_SEE_OTHER);
        }

        $documents=$entityManager->getRepository(Document::class)->findTenderDocuments($tender);
        $grouped_documents_by_status = array_fill_keys(ListService::$document_status, []);


        foreach ($documents as $document) {
            $grouped_documents_by_status[$document->getStatus()][] = $document;
        }

        return $this->render('tender/show.html.twig', 
        [   'tender' => $tender,
            'tender_date'=>$tenderDate,
            'form_status'=>$form_status, 
            'form_document'=>$this->createForm(DocumentType::class),
            'groupedDocuments' => $grouped_documents_by_status,
            'form_file'=>$this->createForm(FileType::class),
            'form_allotissement'=>$this->createForm(AllotissementType::class),
            'files'=>$entityManager->getRepository(File::class)->findBy(['tender'=>$tender->getId()]),
            'allotissements'=>$entityManager->getRepository(Allotissement::class)->findBy(['tender'=>$tender->getId()]),
        ]);
    }




    #[Route('/{id}', name: 'app_tender_delete', methods: ['POST'])]   
    #[IsGranted('operation', 'tender', 'Page not found', 404)]
    public function delete(Request $request, Tender $tender, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tender->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($tender);
            $entityManager->flush();
            $this->addFlash('success','Tender supprimé ! ' );
        }

        return $this->redirectToRoute('app_tender_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/archive/{id}', name: 'app_tender_archive', methods: ['POST'])]
    public function archive_or_reset_tender(Request $request,Tender $tender,EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('archive'.$tender->getId(), $request->getPayload()->getString('_token'))) {
            $tender->setIsArchived(!$tender->isArchived());
            $entityManager->flush();
            $tender->isArchived()?$this->addFlash('success','Tender archivé ! '):$this->addFlash('success','Tender restauré ! ') ;
            
        }else{
            $this->addFlash('error','L\'Opération a échoué. Veuillez réessayer.');
        }
        return $this->redirectToRoute('app_tender_index', []);
    }

}
