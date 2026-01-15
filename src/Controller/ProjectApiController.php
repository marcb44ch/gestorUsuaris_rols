<?php

namespace App\Controller;

use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/projectApi')]
class ProjectApiController extends AbstractController
{
    #[Route('/', name: 'app_projectApi_index', methods: ['GET'])]
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

        return $this->render('projectApi/index.html.twig', [
            'projects' => $projects,
        ]);
    }
}
