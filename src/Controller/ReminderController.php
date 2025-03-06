<?php

namespace App\Controller;

use App\Entity\Reminder;
use App\Form\ReminderType;
use App\Repository\ReminderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/reminder')]
final class ReminderController extends AbstractController
{
    #[Route(name: 'app_reminder_index', methods: ['GET','POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reminder = new Reminder();
        $form = $this->createForm(ReminderType::class, $reminder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reminder->addUser($this->getUser());
            $entityManager->persist($reminder);
            $entityManager->flush();

            return $this->redirectToRoute('app_reminder_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('reminder/index.html.twig', [
            'reminders' => $entityManager->getRepository(Reminder::class)->findAll(),
            'form' => $form,
        ]);
    }

    #[Route('/new', name: 'app_reminder_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reminder = new Reminder();
        $form = $this->createForm(ReminderType::class, $reminder);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reminder);
            $entityManager->flush();

            return $this->redirectToRoute('app_reminder_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reminder/new.html.twig', [
            'reminder' => $reminder,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reminder_delete', methods: ['POST'])]
    public function delete(Request $request, Reminder $reminder, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reminder->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($reminder);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reminder_index', [], Response::HTTP_SEE_OTHER);
    }
}
