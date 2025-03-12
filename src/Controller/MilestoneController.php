<?php

namespace App\Controller;

use App\Entity\Milestone;
use App\Entity\Project;
use App\Entity\TaskStatus;
use App\Form\MilestoneType;
use App\Repository\MilestoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('project/milestone')]
final class MilestoneController extends AbstractController
{

    #[Route('/new/{id}', name: 'app_milestone_new', methods: ['GET', 'POST'])]
    public function new(Project $project, EntityManagerInterface $entityManager, Request $request): Response
    {
        $milestone = new Milestone();
        $form = $this->createForm(MilestoneType::class, $milestone);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $milestone->setProject($project);
            $milestone->setStatus($entityManager->getRepository(TaskStatus::class)->findOneBy(['code'=>'0']));
            $entityManager->persist($milestone);
            $entityManager->flush();

            return $this->redirectToRoute('app_milestone_show', ['id'=>$milestone->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('milestone/new.html.twig', [
            'project_id' => $project->getId(),
            'form' => $form,
        ]);
    }

    #[Route('/show/{id}', name: 'app_milestone_show', methods: ['GET'])]
    public function show(Milestone $milestone): Response
    {
        return $this->render('milestone/show.html.twig', [
            'milestone' => $milestone,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_milestone_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Milestone $milestone, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MilestoneType::class, $milestone,['is_edit'=>true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_project_show', ['id'=>$milestone->getProject()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('milestone/edit.html.twig', [
            'milestone' => $milestone,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_milestone_delete', methods: ['POST'])]
    public function delete(Request $request, Milestone $milestone, EntityManagerInterface $entityManager): Response
    {
        $project=$milestone->getProject();
        if ($this->isCsrfTokenValid('delete'.$milestone->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($milestone);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_project_show', ['id'=>$project->getId()], Response::HTTP_SEE_OTHER);
    }
}
