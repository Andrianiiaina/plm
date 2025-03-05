<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\Tender;
use App\Form\FileType;
use App\Form\TenderType;
use App\Repository\TenderRepository;
use App\Security\Voter\TenderVoter;
use App\Service\FileUploaderService;
use App\Service\ListService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/tender')]
final class TenderController extends AbstractController
{
    #[Route(name: 'app_tender_index', methods: ['GET'])]
    public function index(TenderRepository $tenderRepository,ListService $listService): Response
    {
        
         if ($this->isGranted('ROLE_ADMIN')) {
            $tenders= $tenderRepository->findAll();
         }elseif($this->isGranted('ROLE_RESPO')){
            $tenders= $tenderRepository->findBy(["responsable_id"=>$this->getUser()]);
         }else{
            $tenders=[];
         }
        return $this->render('tender/index.html.twig', [
            'tenders' => $tenders
        ]);
    }

    #[Route('/new', name: 'app_tender_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tender = new Tender();
        $form = $this->createForm(TenderType::class, $tender);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($tender);
                $entityManager->flush();
                $this->addFlash('success','Projet enregistré!' );
            } catch (FileException $e) {
                $this->addFlash('error', "Erreur! Le projet n'a pas pu etre enregistré.");
            }
            return $this->redirectToRoute('app_tender_show', ['id'=>$tender->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tender/new.html.twig', [
            'tender' => $tender,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tender_show', methods: ['GET', 'POST'])]
    #[IsGranted('operation', 'tender', 'Page not found', 404)]
    public function show_tender_and_add_file(Tender $tender, EntityManagerInterface $entityManager, Request $request, 
    FileUploaderService $fileUploader): Response
    {
        $file = new File();
        $form = $this->createForm(FileType::class, $file);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file->setTenderId($tender);
            $brochureFile = $form['filepath']->getData();
            if ($brochureFile) {
                $newFilename = $fileUploader->upload($brochureFile,"tender_files");
                try {
                    $file->setFilename($brochureFile->getClientOriginalName());
                    $file->setFilepath($newFilename);
                    $entityManager->persist($file);
                    $entityManager->flush(); 
                    $this->addFlash('success','Fichier enregistré!' );

                } catch (FileException $e) {
                    $this->addFlash('error', "Erreur! Le fichier n'a pas pu etre enregistré.");
                   dd($e);
                }
            }
            return $this->redirectToRoute('app_tender_show', ['id'=>$tender->getId()], Response::HTTP_SEE_OTHER);
        }
        return $this->render('tender/show.html.twig', ['tender' => $tender, 'form' => $form]);
    }






    #[Route('/{id}/edit', name: 'app_tender_edit', methods: ['GET', 'POST'])]
    #[IsGranted('operation', 'tender', 'Page not found', 404)]
    public function edit(Request $request, Tender $tender, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TenderType::class, $tender);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->flush();
                 $this->addFlash('success','Projet modifié!' );
            } catch (\Throwable $th) {
                $this->addFlash('error',"Erreur! la modification du projet a échoué." );
            }

            return $this->redirectToRoute('app_tender_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tender/edit.html.twig', [
            'tender' => $tender,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_tender_delete', methods: ['POST'])]   
    #[IsGranted('operation', 'tender', 'Page not found', 404)]
  
    public function delete(Request $request, Tender $tender, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tender->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($tender);
            $entityManager->flush();
            $this->addFlash('success','Projet supprimé!' );
        }

        return $this->redirectToRoute('app_tender_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/file/{id}', name: 'app_file_delete', methods: ['POST'])]   
   
    public function delete_file(Request $request, File $file, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$file->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($file);
            $entityManager->flush();
            $this->addFlash('success','Fichier supprimé!' );
        }

        return $this->redirectToRoute('app_tender_index', [], Response::HTTP_SEE_OTHER);
    }
}
