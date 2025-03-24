<?php

namespace App\Controller;

use App\Entity\Document;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
final class DocController extends AbstractController
{

    #[Route('/show/edit/{id}', name: 'app_edit_document', methods: ['GET'])]
    public function edit_document(Document $document, UrlGeneratorInterface $urlGenerator): Response
    {
       
        return $this->render('document/edit_document.html.twig', [
            
        ]);
    }

}


