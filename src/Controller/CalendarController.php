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
        $form = $this->createForm(CalendarType::class, $calendar,["is_admin"=>true,"user"=>$this->getUser()]);
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

        $searchTerm = $request->query->get('q','');
        $calendars = $calendarRepository->getCalendars($this->getUser(),$this->isGranted('ROLE_ADMIN'),$searchTerm);

        return $this->render('calendar/calendar.html.twig',
        [
            'calendars' => $paginator->paginate($calendars, $request->query->getInt('page', 1), 20),
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
