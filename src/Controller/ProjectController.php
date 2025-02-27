<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\Project;
use App\Form\FileType;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use App\Security\Voter\ProjectVoter;
use App\Service\FileUploaderService;
use App\Service\ListService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/project')]
final class ProjectController extends AbstractController
{
    #[Route(name: 'app_project_index', methods: ['GET'])]
    public function index(ProjectRepository $projectRepository,ListService $listService): Response
    {
        
         if ($this->isGranted('ROLE_ADMIN')) {
            $projects= $projectRepository->findAll();
         }elseif($this->isGranted('ROLE_RESPO')){
            $projects= $projectRepository->findBy(["responsable_id"=>$this->getUser()]);
         }else{
            $projects=[];
         }
        return $this->render('project/index.html.twig', [
            'projects' => $projects
        ]);
    }

    #[Route('/new', name: 'app_project_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($project);
            $entityManager->flush();
            return $this->redirectToRoute('app_project_show', ['id'=>$project->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('project/new.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_project_show', methods: ['GET', 'POST'])]
    #[IsGranted('operation', 'project', 'Page not found', 404)]
    public function show_project_and_add_file(Project $project, EntityManagerInterface $entityManager, Request $request, 
    FileUploaderService $fileUploader): Response
    {
        $file = new File();
        $form = $this->createForm(FileType::class, $file);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file->setProjectId($project);
            $brochureFile = $form['filepath']->getData();
            if ($brochureFile) {
                $newFilename = $fileUploader->upload($brochureFile,"project_tas");
                try {
                    $file->setFilename($brochureFile->getClientOriginalName());
                    $file->setFilepath($newFilename);
                    $entityManager->persist($file);
                    $entityManager->flush(); 
                } catch (FileException $e) {
                    $this->addFlash('error', 'Error!');
                   dd($e);
                }
            }
            return $this->redirectToRoute('app_project_show', ['id'=>$project->getId()], Response::HTTP_SEE_OTHER);
        }
        return $this->render('project/show.html.twig', ['project' => $project, 'form' => $form]);
    }






    #[Route('/{id}/edit', name: 'app_project_edit', methods: ['GET', 'POST'])]
    #[IsGranted('operation', 'project', 'Page not found', 404)]
    public function edit(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_project_delete', methods: ['POST'])]   
    #[IsGranted('operation', 'project', 'Page not found', 404)]
  
    public function delete(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($project);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/file/{id}', name: 'app_file_delete', methods: ['POST'])]   
    #[IsGranted('operation', 'project', 'Page not found', 404)]
    public function delete_file(Request $request, File $file, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$file->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($file);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
    }
}
