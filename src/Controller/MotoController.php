<?php

namespace App\Controller;

use App\Entity\Moto;
use App\Form\MotoType;
use App\Repository\MotoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/moto')]
final class MotoController extends AbstractController
{
    #[Route(name: 'app_moto_index', methods: ['GET'])]
    public function index(MotoRepository $motoRepository): Response
    {
        return $this->render('moto/index.html.twig', [
            'motos' => $motoRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_moto_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $moto = new Moto();
        $form = $this->createForm(MotoType::class, $moto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($moto);
            $entityManager->flush();

            return $this->redirectToRoute('app_moto_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('moto/new.html.twig', [
            'moto' => $moto,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_moto_show', methods: ['GET'])]
    public function show(Moto $moto): Response
    {
        return $this->render('moto/show.html.twig', [
            'moto' => $moto,
        ]);
    }

    // #[Route('/{id}/edit', name: 'app_moto_edit', methods: ['GET', 'POST'])]
    // public function edit(Request $request, Moto $moto, EntityManagerInterface $entityManager): Response
    // {
    //     $form = $this->createForm(MotoType::class, $moto);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager->flush();

    //         return $this->redirectToRoute('app_moto_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->render('moto/edit.html.twig', [
    //         'moto' => $moto,
    //         'form' => $form,
    //     ]);
    // }

    #[Route('/{id}/edit', name: 'app_moto_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Moto $moto, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MotoType::class, $moto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            // Si és una petició del modal, potser vols redirigir o tancar el modal
            return $this->redirectToRoute('app_moto_index', [], Response::HTTP_SEE_OTHER);
        }

        // AQUESTA ÉS LA PART CLAU:
        if ($request->isXmlHttpRequest()) {
            // Si és AJAX, renderitzem NOMÉS el contingut del modal
            return $this->render('moto/_edit_content.html.twig', [
                'moto' => $moto,
                'form' => $form,
            ]);
        }

        // Si no és AJAX, renderitzem la pàgina completa com sempre
        return $this->render('moto/edit.html.twig', [
            'moto' => $moto,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_moto_delete', methods: ['POST'])]
    public function delete(Request $request, Moto $moto, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$moto->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($moto);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_moto_index', [], Response::HTTP_SEE_OTHER);
    }
}
