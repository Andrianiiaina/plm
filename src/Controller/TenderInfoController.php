<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Reminder;
use App\Entity\Tender;
use App\Entity\TenderDate;
use App\Event\HistoryEvent;
use App\Form\TenderContactType;
use App\Service\ListService;
use App\Service\ReminderService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tender/info')]
final class TenderInfoController extends AbstractController
{
    
    #[Route('/edit_date/{id}', name: 'app_tender_edit_date', methods: ['GET', 'POST'])]
    public function edit_date(Request $request, Tender $tender, EntityManagerInterface $entityManager, EventDispatcherInterface $dispatcher): Response
    {
        $tenderDate=$tender->getTenderDate()? $tender->getTenderDate() : new TenderDate();
        $form_date = $this->createForm(\App\Form\TenderDateType::class, $tenderDate);
        $form_date->handleRequest($request);
        if ($form_date->isSubmitted() && $form_date->isValid()) {
            $tenderDate->setTender($tender);
            $entityManager->persist($tenderDate);
            $entityManager->flush();
            $this->addFlash('success','Information sur les dates enregistrée' );
            $dispatcher->dispatch(new HistoryEvent($this->getUser(),0,$tender->getId(),"edit_date_tender"));

            if($tender->getContact()){
                return $this->redirectToRoute('app_tender_show', ['id'=>$tender->getId()], Response::HTTP_SEE_OTHER);
            }
            return $this->redirectToRoute('app_tender_edit_organisation', ['id'=>$tender->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tender/components/_date_form.html.twig', [
            'tender' => $tender,
            'form' => $form_date,
            'form_reminder'=>$this->createForm(\App\Form\ReminderType::class),
        ]);
    }

    #[Route('/edit_organisation/{id}', name: 'app_tender_edit_organisation', methods: ['GET', 'POST'])]
    public function edit_organisation(Request $request, Tender $tender, EntityManagerInterface $entityManager, EventDispatcherInterface $dispatcher): Response
    {

        $contact =$tender->getContact()? $tender->getContact():new Contact();
        $form_contact = $this->createForm(\App\Form\ContactType::class, $contact);
        $select_contact_form = $this->createForm(TenderContactType::class);

        $form_contact->handleRequest($request);
        $select_contact_form->handleRequest($request);

        if($form_contact->isSubmitted() && $form_contact->isValid()) {
            $entityManager->persist($contact);
            $entityManager->flush();
            $tender->setContact($contact);
        }elseif($select_contact_form->isSubmitted() && $select_contact_form->isValid()) {
            $contact=$select_contact_form->get('contact')->getData();
            $tender->setContact($contact);
        }else{
            return $this->render('tender/components/_contact_form.html.twig', [
                'tender' => $tender,
                'form' => $form_contact,
                'select_contact_form'=>$select_contact_form,
            ]);
        }
        
        $entityManager->flush();
        $this->addFlash('success',"Information sur l'organisation enregistrée!" );
        $dispatcher->dispatch(new HistoryEvent($this->getUser(),0,$tender->getId(),"edit_organisation_tender"));
        return $this->redirectToRoute('app_tender_show', ['id'=>$tender->getId()], Response::HTTP_SEE_OTHER);
    }
      
    #[Route('/archive/{id}', name: 'app_tender_archive', methods: ['POST'])]
    public function archive_or_reset_tender(Request $request,Tender $tender,EntityManagerInterface $entityManager, EventDispatcherInterface $dispatcher): Response
    {
        if ($this->isCsrfTokenValid('archive'.$tender->getId(), $request->getPayload()->getString('_token'))) {
            $tender->setIsArchived(!$tender->isArchived());
            $entityManager->flush();

            if($tender->isArchived()){
                $this->addFlash('success','Tender archivé ! ');
                $dispatcher->dispatch(new HistoryEvent($this->getUser(),0,$tender->getId(),"archive_tender"));
            }else{
                $this->addFlash('success','Tender restauré ! ') ;
                $dispatcher->dispatch(new HistoryEvent($this->getUser(),0,$tender->getId(),"reset_tender"));
            }
        }else{
            $this->addFlash('error','L\'Opération a échoué. Veuillez réessayer.');
        }
        return $this->redirectToRoute('app_tender_index', []);
    }

    #[Route('/reminder/delete/{tender_id}/{reminder_id}', name: 'app_reminder_delete', methods: ['POST'])]   
    public function delete(Request $request, $reminder_id,$tender_id, EntityManagerInterface $entityManager): Response
    {  
        if ($this->isCsrfTokenValid('delete_reminder'.$reminder_id, $request->getPayload()->getString('_token'))) {
            $reminder=$entityManager->getRepository(Reminder::class)->find($reminder_id);
            
            $entityManager->remove($reminder);
            $entityManager->flush();

            $this->addFlash('success','Rappel supprimé! ' );
        }
        return $this->redirectToRoute('app_tender_edit_date', ['id'=>$tender_id], Response::HTTP_SEE_OTHER);
    }

    #[Route('/add/reminder/{id}', name: 'app_add_reminder', methods: ['POST'])]
    public function add_reminder(ReminderService $reminder_service,Request $request, TenderDate $tender_date, EntityManagerInterface $entityManager): Response
    {
        $reminder = new Reminder();
        $form_reminder = $this->createForm(\App\Form\ReminderType::class,$reminder);
        $form_reminder->handleRequest($request);
        if ($form_reminder->isSubmitted() && $form_reminder->isValid()) {
            if ($reminder_service->reminderExists($tender_date, $reminder)) {
                $this->addFlash('error', 'Le rappel existe déjà.');
            } else {
                $is_created=$reminder_service->createReminder($tender_date,$reminder);
                $is_created?$this->addFlash('success',"Rappel enregistré"):$this->addFlash('error',"La date pour ce type de rappel est vide. Enregistrez d'abord la date.");
            }
        }

        return $this->redirectToRoute('app_tender_edit_date', ['id'=>$tender_date->getTender()->getId()], Response::HTTP_SEE_OTHER);
    }

    
}
