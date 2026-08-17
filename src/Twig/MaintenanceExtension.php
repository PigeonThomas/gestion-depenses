<?php

namespace App\Twig;

use App\Entity\User;
use App\Service\MaintenanceVehiculeService;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MaintenanceExtension extends AbstractExtension
{
    public function __construct(
        private readonly MaintenanceVehiculeService $maintenanceVehiculeService,
        private readonly Security $security,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('maintenance_alert_count', [$this, 'getMaintenanceAlertCount']),
        ];
    }

    public function getMaintenanceAlertCount(): int
    {
        $user = $this->security->getUser();

        if (!$user instanceof User || $user->getId() === null) {
            return 0;
        }

        return $this->maintenanceVehiculeService->countAlerts($user->getId());
    }
}