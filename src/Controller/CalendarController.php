<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Form\CalendarType;
use App\Repository\CalendarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/calendar')]
final class CalendarController extends AbstractController
{
    #[Route(path: '/calendar', name: 'app_calendar')]
    public function calendar(CalendarRepository $calendarRepository,Request $request, EntityManagerInterface $entityManager): Response
    {               
        $calendar = new Calendar();
        $form = $this->createForm(CalendarType::class, $calendar);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if($form->isValid()==false){
                $this->addFlash('error','Un problème est survenu, réessayé!' );
            }else{
                $entityManager->persist($calendar);
               
                $entityManager->flush(); 
               
                $this->addFlash('success','Evènement enregistré!' );
            }

      
            return $this->redirectToRoute('app_calendar', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('calendar/calendar.html.twig',
        [
            'calendars' => $calendarRepository->findUserCalendar($this->getUser()),
            'form'=>$form
        ]
    );
    }
    #[Route('/{id}', name: 'app_calendar_show', methods: ['GET'])]
    public function show(Calendar $calendar): Response
    {
        return $this->render('calendar/show.html.twig', [
            'calendar' => $calendar,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_calendar_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Calendar $calendar, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CalendarType::class, $calendar,['is_edited'=>true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Evènement bien modifié!' );
            
            return $this->redirectToRoute('app_calendar', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('calendar/edit.html.twig', [
            'calendar' => $calendar,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_calendar_delete', methods: ['POST'])]
    public function delete(Request $request, Calendar $calendar, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete_calendar'.$calendar->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($calendar);
            $entityManager->flush();
            $this->addFlash('success','Evènement supprimé!' );
        }
        

        return $this->redirectToRoute('app_calendar', [], Response::HTTP_SEE_OTHER);
    }
}
