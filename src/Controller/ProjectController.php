<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Comment;
use App\Form\ProjectType;
use App\Form\CommentType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\DateTime;

#[Route('/project')]
class ProjectController extends AbstractController
{
    // 1. LLISTAT: Tothom que sigui usuari (ROLE_USER) pot veure la llista
    #[Route('/', name: 'app_project_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')] 
    public function index(ProjectRepository $projectRepository, Request $request): Response
    {
        $searchTerm = $request->query->get('q');
        $status = $request->query->get('status');
        
        // Rang inici
        $r1Param = $request->query->get('range1');
        $range1 = $r1Param ? new \DateTime($r1Param) : null;
        $r2Param = $request->query->get('range2');
        $range2 = $r2Param ? new \DateTime($r2Param) : null;

        // Rang final
        $r3Param = $request->query->get('range3');
        $range3 = $r3Param ? new \DateTime($r3Param) : null;
        $r4Param = $request->query->get('range4');
        $range4 = $r4Param ? new \DateTime($r4Param) : null;

        if ($range1 && $range2 && $range1 > $range2) {
            $this->addFlash('warning', 'La data inicial no pot ser posterior a la data final');
            $range1 = $range2 = null;
        }
        
        if ($range3 && $range4 && $range3 > $range4) {
            $this->addFlash('warning', 'La data final inicial no pot ser posterior a la data final');
            $range3 = $range4 = null;
        }

        $projects = $projectRepository->findByFilters($searchTerm, $status, $range1, $range2, $range3, $range4);

        return $this->render('project/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    // 2. CREAR: Només l'ADMIN pot crear
    #[Route('/new', name: 'app_project_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')] 
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('project/new.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    // 3. DETALL: Els usuaris normals poden veure el detall
    #[Route('/{id}', name: 'app_project_show', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function show(Project $project, Request $request, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setProject($project);
            $comment->setAuthor($this->getUser());
            
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app_project_show', ['id' => $project->getId()]);
        }

        return $this->render('project/show.html.twig', [
            'project' => $project,
            'comments' => $project->getComments(),
            'commentForm' => $form->createView(),
            'user' => $this->getUser(),
        ]);
    }

    // // 4. EDITAR: Només l'ADMIN pot editar
    // #[Route('/{id}/edit', name: 'app_project_edit', methods: ['GET', 'POST'])]
    // #[IsGranted('ROLE_ADMIN')]
    // public function edit(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    // {
    //     $form = $this->createForm(ProjectType::class, $project);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager->flush();

    //         return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->render('project/edit.html.twig', [
    //         'project' => $project,
    //         'form' => $form,
    //     ]);
    // }

    #[Route('/{id}/edit', name: 'app_project_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            // Si és una petició del modal, potser vols redirigir o tancar el modal
            return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
        }

        // AQUESTA ÉS LA PART CLAU:
        if ($request->isXmlHttpRequest()) {
            // Si és AJAX, renderitzem NOMÉS el contingut del modal
            return $this->render('project/_edit_content.html.twig', [
                'project' => $project,
                'form' => $form,
            ]);
        }

        // Si no és AJAX, renderitzem la pàgina completa com sempre
        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    // 5. ELIMINAR: Només l'ADMIN pot eliminar
    #[Route('/{id}', name: 'app_project_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Project $project, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$project->getId(), $request->request->get('_token'))) {
            $entityManager->remove($project);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_project_index', [], Response::HTTP_SEE_OTHER);
    }
}
