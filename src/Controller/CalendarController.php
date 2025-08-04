<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Form\CalendarType;
use App\Repository\CalendarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/calendar')]
final class CalendarController extends AbstractController
{
    #[Route(path: '/calendar', name: 'app_calendar_index')]
    public function calendar(CalendarRepository $calendarRepository,PaginatorInterface $paginator,Request $request, EntityManagerInterface $entityManager): Response
    {               
        $calendar = new Calendar();
        $form = $this->createForm(CalendarType::class, $calendar,["user"=>$this->getUser()]);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if(!$form->isValid()){
                $this->addFlash('error','Erreur, Veuillez revérifier les informations.' );
            }else{
                $entityManager->persist($calendar);
                $entityManager->flush(); 
                $this->addFlash('success','Evènement enregistré ! ' );
            }
            return $this->redirectToRoute('app_calendar_index', [], Response::HTTP_SEE_OTHER);
        }
        $searchTerm=$request->query->get('q','');
    
        $pagination = $paginator->paginate(
            $this->isGranted('ROLE_ADMIN')?
            $calendarRepository->findAdminCalendar(100,$searchTerm):
            $calendarRepository->findUserCalendar($this->getUser(),100,$searchTerm),
            $request->query->getInt('page', 1), 
            20
        );

        return $this->render('calendar/calendar.html.twig',
        [
            'calendars' => $pagination,
            'form'=>$form,
            'searchTerm'=>$searchTerm??'',
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
            $this->addFlash('success', 'Evènement bien modifié ! ' );
            return $this->redirectToRoute('app_calendar_index', [], Response::HTTP_SEE_OTHER);
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
            $this->addFlash('success','Evènement supprimé ! ' );
        }
        
        return $this->redirectToRoute('app_calendar_index', [], Response::HTTP_SEE_OTHER);
    }
}
