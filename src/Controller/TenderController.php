<?php

namespace App\Controller;

use App\Entity\Document;
use App\Entity\History;
use App\Entity\Tender;
use App\Entity\TenderDate;
use App\Event\HistoryEvent;
use App\Event\UserAssignedToEntityEvent;
use App\Repository\TenderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/tender')]
final class TenderController extends AbstractController
{


    #[Route(name: 'app_tender_index', methods: ['GET'])]
    public function index(TenderRepository $tenderRepository, Request $request,PaginatorInterface $paginator): Response
    {
        $searchTerm = $request->query->get('q','');
        $tenders = $this->isGranted('ROLE_ADMIN') 
            ? $tenderRepository->search($searchTerm) 
            : $tenderRepository->searchTenderRespo($searchTerm, $this->getUser());

        $pagination = $paginator->paginate($tenders, $request->query->getInt('page', 1), 10);
      
       
        $statistiques=$this->isGranted('ROLE_ADMIN')?$tenderRepository->findAllStatistic():$tenderRepository->findRespoStatistic($this->getUser());
        return $this->render('tender/index.html.twig', [
            'tenders' => $pagination,
            'total_tenders'=>array_sum($statistiques),
            'searchTerm' => $searchTerm,
            'total_tender_by_status'=>$statistiques,
        ]);

    }

    #[Route('/new', name: 'app_tender_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, EventDispatcherInterface $dispatcher): Response
    {
        $tender = new Tender();
        $form = $this->createForm(\App\Form\TenderType::class, $tender);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tender->setResponsable($this->getUser());
            $entityManager->persist($tender);
            $tender_date=new TenderDate();
            $tender_date->setTender($tender);
            $entityManager->persist($tender_date);
                $entityManager->flush();
                $dispatcher->dispatch(new UserAssignedToEntityEvent($tender->getResponsable(),$tender->getId(),1));
                $dispatcher->dispatch(new HistoryEvent($this->getUser(),History::TENDER_TYPE,$tender->getId(),"create_tender"));
                $this->addFlash('success','Tender enregistré ! ' );
            return $this->redirectToRoute('app_tender_edit_date', ['id'=>$tender->getId()], Response::HTTP_SEE_OTHER);
        }


        return $this->render('tender/new.html.twig', [
            'form' => $form,
        ]);
    }


    #[Route('/edit_basic_info/{id}/', name: 'app_tender_edit_informations', methods: ['GET', 'POST'])]
    #[IsGranted('operation', 'tender', 'Page not found', 404)]
    public function edit_basic_informations(Request $request, Tender $tender, EntityManagerInterface $entityManager, EventDispatcherInterface $dispatcher): Response
    {
        $form = $this->createForm(\App\Form\TenderType::class, $tender);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success','Tender modifié ! ' );
            $dispatcher->dispatch(new HistoryEvent($this->getUser(),History::TENDER_TYPE,$tender->getId(),"edit_information_tender"));
            return $this->redirectToRoute('app_tender_index', [], Response::HTTP_SEE_OTHER);
        }


        return $this->render('tender/edit.html.twig', [
            'tender' => $tender,
            'form' => $form,
        ]);
    }

    #[Route('/show/{id}', name: 'app_tender_show', methods: ['GET','POST'])]
    #[IsGranted('operation', 'tender', 'Page not found', 404)]
    public function show_tender(Tender $tender, Request $request, EntityManagerInterface $entityManager, EventDispatcherInterface $dispatcher): Response
    {

        $form_status = $this->createForm(\App\Form\StatusType::class, $tender);
        $form_status->handleRequest($request);
        $tenderDate=$entityManager->getRepository(\App\Entity\TenderDate::class)->findOneBy(['tender'=>$tender]);



        if ($form_status->isSubmitted() && $form_status->isValid()) {
            $entityManager->flush();
            $dispatcher->dispatch(new HistoryEvent($this->getUser(),History::TENDER_TYPE,$tender->getId(),"edit_status_tender"));

            $this->addFlash('success','status modifié ! ' );
            return $this->redirectToRoute('app_tender_show', ['id'=>$tender->getId()], Response::HTTP_SEE_OTHER);
        }

        $documents=$entityManager->getRepository(Document::class)->findTenderDocuments($tender);
        $grouped_documents_by_status = array_fill_keys(\App\Service\ListService::$document_status, []);


        foreach ($documents as $document) {
            $grouped_documents_by_status[$document->getStatus()][] = $document;
        }



        return $this->render('tender/show.html.twig', 
        [   'tender' => $tender,
            'tender_date'=>$tenderDate,
            'form_status'=>$form_status, 
            'form_document'=>$this->createForm(\App\Form\DocumentType::class),
            'groupedDocuments' => $grouped_documents_by_status,
            'form_file'=>$this->createForm(\App\Form\FileType::class),
            'form_allotissement'=>$this->createForm(\App\Form\AllotissementType::class),
            'files'=>$entityManager->getRepository(\App\Entity\File::class)->findBy(['tender'=>$tender->getId()]),
            'allotissements'=>$entityManager->getRepository(\App\Entity\Allotissement::class)->findBy(['tender'=>$tender->getId()]),
        ]);
    }



    #[Route('/delete/{id}', name: 'app_tender_delete', methods: ['POST'])]   
    #[IsGranted('operation', 'tender', 'Page not found', 404)]
    public function delete(Request $request, Tender $tender, EntityManagerInterface $entityManager, EventDispatcherInterface $dispatcher): Response
    {   
        $id=$tender->getId();
        if ($this->isCsrfTokenValid('delete'.$id, $request->getPayload()->getString('_token'))) {
            $entityManager->remove($tender);
            $entityManager->flush();
            $dispatcher->dispatch(new HistoryEvent($this->getUser(),History::TENDER_TYPE,$id,"delete_tender"));

            $this->addFlash('success','Tender supprimé ! ' );
        }



        return $this->redirectToRoute('app_tender_index', [], Response::HTTP_SEE_OTHER);
    }

   

}
