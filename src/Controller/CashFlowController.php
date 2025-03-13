<?php

namespace App\Controller;

use App\Entity\CashFlow;
use App\Entity\Project;
use App\Form\CashFlowType;
use App\Repository\CashFlowRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cash/flow')]
final class CashFlowController extends AbstractController
{
    #[Route('/lists/project/{id}',name: 'app_cash_flow_index', methods: ['GET'])]
    public function index($id, CashFlowRepository $cashFlowRepository): Response
    {
        return $this->render('cash_flow/index.html.twig', [
            'project_id' => $id,
            'cashflows'=>$cashFlowRepository->findBy(['project'=>$id]),
        ]);
    }

    #[Route('/new/project/{id}', name: 'app_cash_flow_new', methods: ['GET', 'POST'])]
    public function new($id,Request $request, EntityManagerInterface $entityManager): Response
    {
        $cashFlow = new CashFlow();
        $form = $this->createForm(CashFlowType::class, $cashFlow);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cashFlow->setExpense(0);
            $cashFlow->setProject($entityManager->getRepository(Project::class)->findOneBy(['id'=>$id]));
            $entityManager->persist($cashFlow);
            $entityManager->flush();

            return $this->redirectToRoute('app_cash_flow_index', ['id'=>$id], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cash_flow/new.html.twig', [
            'project_id' => $id,
            'form' => $form,
        ]);
    }

    #[Route('/show/{id}', name: 'app_cash_flow_show', methods: ['GET'])]
    public function show(CashFlow $cashFlow): Response
    {
        return $this->render('cash_flow/show.html.twig', [
            'cash_flow' => $cashFlow,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_cash_flow_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CashFlow $cashFlow, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CashFlowType::class, $cashFlow);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_cash_flow_show', ['id'=>$cashFlow->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cash_flow/edit.html.twig', [
            'cash_flow' => $cashFlow,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cash_flow_delete', methods: ['POST'])]
    public function delete(Request $request, CashFlow $cashFlow, EntityManagerInterface $entityManager): Response
    {
        $project=$cashFlow->getProject();
        if ($this->isCsrfTokenValid('delete'.$cashFlow->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($cashFlow);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cash_flow_index',  ['id'=>$project->getId()], Response::HTTP_SEE_OTHER);
    }
}
