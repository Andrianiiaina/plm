<?php

namespace App\Controller;

use App\Entity\Document;
use App\Entity\Tender;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ArchiveController extends AbstractController
{
    #[Route('/archives', name: 'app_archive_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        return $this->render('archives/archived_document.html.twig', [
            'tenders' => $this->isGranted('ROLE_ADMIN')? 
            $entityManager->getRepository(Tender::class)->findBy(['isArchived'=>true]) :
            $entityManager->getRepository(Tender::class)->findRespoTenders($this->getUser(),true),
            'documents' => $entityManager->getRepository(Document::class)->findBy(['status'=>"4"], ['createdAt' => 'DESC']),
        ]);
    }
    
}
