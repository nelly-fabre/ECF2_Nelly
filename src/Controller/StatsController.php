<?php

namespace App\Controller;

use App\Repository\StudentRepository;
use App\Service\ActionLogger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class StatsController extends AbstractController
{
    #[Route('/stats', name: 'app_stats')]
    public function index(StudentRepository $studentRepository, ActionLogger $actionLogger): Response
    {
        $students = $studentRepository->findAll();
        $logs = $actionLogger->getLogs();

        usort($students, function ($a, $b) {
            return $b->getTotalAbsencesCount() - $a->getTotalAbsencesCount();
        });

        return $this->render('stats/index.html.twig', [
            'students' => $students,
            'logs' => $logs,
        ]);
    }
}
