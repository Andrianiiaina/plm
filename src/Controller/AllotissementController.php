<?php

namespace App\Controller;

use App\Entity\Allotissement;
use App\Entity\Tender;
use App\Form\AllotissementType;
use App\Repository\AllotissementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/allotissement')]
final class AllotissementController extends AbstractController
{
    #[Route('/new/tender/{id}', name: 'app_allotissement_new', methods: ['POST'])]
    public function new(Tender $tender,Request $request, EntityManagerInterface $entityManager): Response
    {
        $allotissement = new Allotissement();
        $form = $this->createForm(AllotissementType::class, $allotissement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $allotissement->setTender($tender);
            $entityManager->persist($allotissement);
            $entityManager->flush();
            $this->addFlash('success','Allotissement enregistré! ' );

        }

        return $this->redirectToRoute('app_tender_show', ['id'=>$tender->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/show/{id}', name: 'app_allotissement_show', methods: ['GET'])]
    public function show(Allotissement $allotissement): Response
    {
        return $this->render('allotissement/show.html.twig', [
            'allotissement' => $allotissement,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_allotissement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Allotissement $allotissement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AllotissementType::class, $allotissement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success','Allotissement modifié ! ' );
            return $this->redirectToRoute('app_allotissement_show', ['id'=>$allotissement->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('allotissement/edit.html.twig', [
            'allotissement' => $allotissement,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_allotissement_delete', methods: ['POST'])]
    public function delete(Request $request, Allotissement $allotissement, EntityManagerInterface $entityManager): Response
    {
        $tender_id=$allotissement->getTender()->getId();
        if ($this->isCsrfTokenValid('delete'.$allotissement->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($allotissement);
            $entityManager->flush();
            
            $this->addFlash('success','Allotissement supprimé ! ' );
        }

        return $this->redirectToRoute('app_tender_show', ['id'=>$tender_id], Response::HTTP_SEE_OTHER);
    }
}
