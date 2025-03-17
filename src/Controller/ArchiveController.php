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
    #[Route('/archive', name: 'app_archive')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        return $this->render('archives/archived_document.html.twig', [
            'tenders' => $entityManager->getRepository(Tender::class)->findRespoArchivedTenders($this->getUser()),
            'documents' => $entityManager->getRepository(Document::class)->findBy(['status'=>"4"], ['createdAt' => 'DESC']),
        ]);
    }
}
