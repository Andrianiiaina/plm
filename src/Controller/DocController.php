<?php

namespace App\Controller;

use Firebase\JWT\JWT;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class DocController extends AbstractController
{      
    

    #[Route('/document/ediit/{filename}', name: 'document_edit')]
    public function edit(string $filename)
    {
        $documentUrl = $this->generateUrl('document_view', ['filename' => $filename], true);
        
        $callbackUrl = $this->generateUrl('document_callback', ['filename' => $filename], true);

        $documentUrl = $this->generateUrl('document_view', ['filename' => $filename], UrlGeneratorInterface::ABSOLUTE_URL);
       // $documentUrl = str_replace('https://', 'http://', $documentUrl);
        //$documentUrl = str_replace('localhost', 'host.docker.internal', $documentUrl);
       // Payload du JWT
       $payload = [
        'document' => [
            'fileType' => 'docx',
            'key' => $filename,
            'title' => $filename,
            'url' => $documentUrl,
        ],
        'editorConfig' => [
            'callbackUrl' => $callbackUrl,
            'mode'=>'view',
        ],
    ];
    $secretKey = $this->getParameter('onlyoffice_jwt_secret');
    // Générer le JWT
    $jwt = JWT::encode($payload, $secretKey, 'HS256');

        return $this->render('document/edit_document.html.twig', [
            'filename' => $filename,
            'documentUrl' => $documentUrl,
            'callbackUrl' => $callbackUrl,
            'token'=>$jwt,
            
        ]);
    }

    #[Route('/document/view/{filename}', name: 'document_view')]
    public function view(string $filename)
    {
        $filePath = $this->getParameter('targetDirectory').'/tender_documents/'.$filename;
       
        if (!file_exists($filePath)) {
            throw $this->createNotFoundException("Fichier introuvable.");
        }
        return $this->file($filePath, $filename, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    #[Route('/document/callback/{filename}', name: 'document_callback', methods: ['POST'])]
    public function callback(Request $request, string $filename): JsonResponse
    {
        return new JsonResponse(['success' => true]);
       
        $data = json_decode($request->getContent(), true);
 
        $filesystem = new Filesystem();
        $filePath = $this->getParameter('targetDirectory') . '/tender_documents/' . $filename;
       
        if (isset($data['status']) && $data['status'] == 2) { // État 2 = document modifié
            $downloadUri = $data['url'];
            if (!isset($data['url']) || empty($data['url'])) {
                return new JsonResponse(['success' => false, 'error' => 'URL de téléchargement absente'], 400);
            }
            $fileContents = file_get_contents($downloadUri);
            $filesystem->dumpFile($filePath, $fileContents);
        }

        return new JsonResponse(['success' => true]);
    }
}


