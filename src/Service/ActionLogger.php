<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

class ActionLogger
{
    public function __construct(
        #[Autowire('%kernel.project_dir%')] private string $projectDir
    ) {}

    public function log(string $action, string $entity, int $entityId, string $adminName): void
    {
        $logFile = $this->projectDir . '/var/log/admin_actions.json';

        if (file_exists($logFile)) {
            $logs = json_decode(file_get_contents($logFile), true);
        } else {
            $logs = [];
        }

        $logs[] = [
            'date' => (new \DateTime())->format('d-m-Y H:i:s'),
            'action' => $action,
            'entity' => $entity,
            'entityId' => $entityId,
            'admin' => $adminName,
        ];

        file_put_contents(
            $logFile,
            json_encode($logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    public function getLogs(): array
    {
        $logFile = $this->projectDir . '/var/log/admin_actions.json';

        if (file_exists($logFile)) {
            $logs = json_decode(file_get_contents($logFile), true);
        } else {
            $logs = [];
        }
        return array_reverse($logs);
    }
}
