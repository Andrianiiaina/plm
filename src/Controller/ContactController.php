<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\ContactGroup;
use App\Form\ContactGroupType;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/contact')]
final class ContactController extends AbstractController
{
    #[Route(name: 'app_contact_index', methods: ['GET','POST'])]
    public function index(
        Request $request, 
        EntityManagerInterface $entityManager,
        PaginatorInterface $paginator): Response
    {
        //we can create contact_group in the same page.
        $contactGroup = new ContactGroup();
        $form_group = $this->createForm(ContactGroupType::class, $contactGroup);
        $form_group->handleRequest($request);

        if ($form_group->isSubmitted() && $form_group->isValid()) {
            $entityManager->persist($contactGroup);
            $entityManager->flush();
            $this->addFlash('success','Groupe enregistré!' );
            return $this->redirectToRoute('app_contact_index', [], Response::HTTP_SEE_OTHER);
        }

        $searchTerm = $request->query->get('q','');
        $pagination = $paginator->paginate(
            $entityManager->getRepository(Contact::class)->search($searchTerm), 
            $request->query->getInt('page', 1), 
            15 
        );
        return $this->render('contact/index.html.twig', [
            'contacts' => $pagination,
            'groups' => $entityManager->getRepository(ContactGroup::class)->findAll(),
            'form'=>$form_group,
            'searchTerm' => $searchTerm??""
        ]);
    }

    #[Route('/new', name: 'app_contact_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $contact = new Contact();
        $form_contact = $this->createForm(ContactType::class, $contact);
        $form_contact->handleRequest($request);

        if ($form_contact->isSubmitted() && $form_contact->isValid()) {
           
            $entityManager->persist($contact);
            $entityManager->flush();
            $this->addFlash('success','Contact enregistré!' );
            
            return $this->redirectToRoute('app_contact_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('contact/new.html.twig', [
            'contact' => $contact,
            'form' => $form_contact,
        ]);
    }

    #[Route('/show/{id}', name: 'app_contact_show', methods: ['GET'])]
    public function show(Contact $contact): Response
    {
        return $this->render('contact/show.html.twig', [
            'contact' => $contact,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_contact_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Contact $contact, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success','Contact modifié!' );
            return $this->redirectToRoute('app_contact_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('contact/edit.html.twig', [
            'contact' => $contact,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_contact_delete', methods: ['POST'])]
    public function delete(Request $request, Contact $contact, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if ($this->isCsrfTokenValid('delete'.$contact->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($contact);
            $entityManager->flush();
            $this->addFlash('success','Contact supprimé!' );
        }

        return $this->redirectToRoute('app_contact_index', [], Response::HTTP_SEE_OTHER);
    }
}
