<?php

namespace App\Controller;
use App\Entity\ContactGroup;
use App\Form\ContactGroupType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/contact/group')]
final class ContactGroupController extends AbstractController
{

    #[Route('/{id}/edit', name: 'app_contact_group_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ContactGroup $contactGroup, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(ContactGroupType::class, $contactGroup);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success','Groupe modifié!' );
            return $this->redirectToRoute('app_contact_group_show', ['id'=>$contactGroup->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('contact_group/edit.html.twig', [
            'group' => $contactGroup,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_contact_group_delete', methods: ['POST'])]
    public function delete(Request $request, ContactGroup $contactGroup, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if ($this->isCsrfTokenValid('delete'.$contactGroup->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($contactGroup);
            $entityManager->flush();
            $this->addFlash('success','Groupe supprimé!' );
        }

        return $this->redirectToRoute('app_contact_index', [], Response::HTTP_SEE_OTHER);
    }
}
