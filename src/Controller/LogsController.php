<?php

namespace App\Controller;

use App\Service\ActionLogger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class LogsController extends AbstractController
{
    #[Route('/logs', name: 'app_logs')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(ActionLogger $actionLogger): Response
    {

        $logs = $actionLogger->getLogs();


        return $this->render('logs/index.html.twig', [

            'logs' => $logs,
        ]);
    }
}
