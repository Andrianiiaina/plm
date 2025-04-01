<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Tender;
use App\Event\UserAssignedToEntityEvent;
use App\Form\ContactType;
use App\Form\TenderDateType;
use App\Form\TenderType;
use App\Repository\TenderRepository;
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

        return $this->render('tender/index.html.twig', [
            'tenders' => $pagination,
            'searchTerm' => $searchTerm
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
                $this->addFlash('success','Tender enregistré!' );
            return $this->redirectToRoute('app_tender_edit_date', ['id'=>$tender->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tender/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/edit_date/{id}', name: 'app_tender_edit_date', methods: ['GET', 'POST'])]
    public function edit_date(Request $request, Tender $tender, EntityManagerInterface $entityManager): Response
    {
        $form_date = $this->createForm(TenderDateType::class, $tender);
        $form_date->handleRequest($request);

        if ($form_date->isSubmitted() && $form_date->isValid()) {
            $entityManager->flush();
            $this->addFlash('success','Opération réussie' );
            if($tender->getContact()){
                return $this->redirectToRoute('app_tender_show', ['id'=>$tender->getId()], Response::HTTP_SEE_OTHER);
            }
            return $this->redirectToRoute('app_tender_edit_organisation_contact', ['id'=>$tender->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tender/_date_form.html.twig', [
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

        return $this->render('tender/_contact_form.html.twig', [
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
            $this->addFlash('success','Informations enregistrée!' );
            return $this->redirectToRoute('app_tender_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tender/edit.html.twig', [
            'tender' => $tender,
            'form' => $form,
        ]);
    }

    #[Route('/show/{id}', name: 'app_tender_show', methods: ['GET','POST'])]
    #[IsGranted('operation', 'tender', 'Page not found', 404)]
    public function show_tender(Tender $tender): Response
    {
        return $this->render('tender/show.html.twig', ['tender' => $tender]);
    }

    #[Route('/{id}', name: 'app_tender_delete', methods: ['POST'])]   
    #[IsGranted('operation', 'tender', 'Page not found', 404)]
    public function delete(Request $request, Tender $tender, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tender->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($tender);
            $entityManager->flush();
            $this->addFlash('success','Tender supprimé!' );
        }

        return $this->redirectToRoute('app_tender_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/archive/{id}', name: 'app_tender_archive', methods: ['POST'])]
    public function archive_or_reset_tender(Request $request,Tender $tender,EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('archive'.$tender->getId(), $request->getPayload()->getString('_token'))) {
            $tender->setIsArchived(!$tender->isArchived());
            $entityManager->flush();
            $tender->isArchived()?$this->addFlash('success','Tender archivé!'):$this->addFlash('success','Tender restauré!') ;
            
        }else{
            $this->addFlash('error','Opération échouée');
        }
        return $this->redirectToRoute('app_tender_index', []);
    }
}
