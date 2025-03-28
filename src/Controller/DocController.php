<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

final class DocController extends AbstractController
{      
    #[Route('/document-temporaire/{nomFichier}', name: 'document_temporaire')]
    public function documentTemporaire(string $nomFichier): Response
    {
        $cheminFichier = $this->getParameter('targetDirectory') . '/tender_documents/' . $nomFichier;
    
        if (!file_exists($cheminFichier)) {
            throw $this->createNotFoundException('Le fichier n\'a pas été trouvé.');
        }
    
        $response = new BinaryFileResponse($cheminFichier);

        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE, // Afficher dans le navigateur si possible
            $nomFichier
        );
    
        return $response;
    }
}


