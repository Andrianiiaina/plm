<?php

namespace App\Controller;

use App\Entity\File;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/project/file')]
final class FileController extends AbstractController
{
  
    #[Route('/{id}', name: 'app_file_delete', methods: ['POST'])]
    public function delete(Request $request, File $file, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$file->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($file);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
    }

}

