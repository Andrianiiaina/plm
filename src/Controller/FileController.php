<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\Tender;
use App\Form\FileType;
use App\Repository\FileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/file')]
final class FileController extends AbstractController
{
    #[Route('project/{id}',name: 'app_file_index', methods: ['POST'])]
    public function index(Tender $tender,FileRepository $fileRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $file = new File();
        $form = $this->createForm(FileType::class, $file);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file->setTender($tender);
            $file->setIsFinished(false);
            $entityManager->persist($file);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_tender_show', ['id'=>$tender->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/delete/{id}', name: 'app_file_delete', methods: ['POST'])]
    public function delete(Request $request, File $file, EntityManagerInterface $entityManager): Response
    {
        $tender_id=$file->getTender()->getId();
        if ($this->isCsrfTokenValid('delete'.$file->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($file);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tender_show', ['id'=>$tender_id], Response::HTTP_SEE_OTHER);
    }


    #[Route('/status/{id}', name: 'update_file_status', methods: ['POST'])]
    public function updateFileStatus(File $file, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
            $data = json_decode($request->getContent(), true);

            if (isset($data['status'])) {
                $file->setIsFinished($data['status']);
                $entityManager->flush();

                return new JsonResponse(['success' => true]);
            }

            return new JsonResponse(['success' => false], 400);
        }
    

}
