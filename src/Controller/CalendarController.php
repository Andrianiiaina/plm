<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Entity\Reminder;
use App\Form\CalendarType;
use App\Repository\CalendarRepository;
use DateTimeImmutable;
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
    public function calendar(): Response
    {
        
        return $this->render('calendar/calendar.html.twig');
    }

    #[Route('/new', name: 'app_calendar_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, CalendarRepository $calendarRepository): Response
    {
        $calendar = new Calendar();
        $form = $this->createForm(CalendarType::class, $calendar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($calendar);
              
                 //si on recoit reminder-> on enregistre
             if($form['reminder']->getData() != null){
                $reminder = new Reminder();
                $reminder->setTitle($calendar->getTitle());
                $reminder->addUser($this->getUser());
                $reminder->setDate($form['reminder']->getData());
                $entityManager->persist($reminder);
            }
            $entityManager->flush();
            $this->addFlash('success','Evènement enregistré!' );
            } catch (Exception $e) {
                $this->addFlash('error', "Erreur! L'évènement n'a pas pu etre enregistré.");
            }
            return $this->redirectToRoute('app_calendar', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('calendar/index.html.twig', [
            'calendar' => $calendar,
            'form' => $form,
            'calendars' => $calendarRepository->findBy([], ['beginAt' => 'ASC']),
        ]);
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
            try {
                $entityManager->flush();
                $this->addFlash('success', 'Evènement bien modifié!' );
            } catch (Exception $e) {
                $this->addFlash('error', "Erreur! la modification de l'évènement a échoué.");
            }
            return $this->redirectToRoute('app_calendar_new', [], Response::HTTP_SEE_OTHER);
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
        

        return $this->redirectToRoute('app_calendar_new', [], Response::HTTP_SEE_OTHER);
    }
}
