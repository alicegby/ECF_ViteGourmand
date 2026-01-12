<?php

namespace App\Twig;

use App\Repository\HoraireRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private HoraireRepository $horaireRepository;

    public function __construct(HoraireRepository $horaireRepository)
    {
        $this->horaireRepository = $horaireRepository;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_openings_hours', [$this, 'getOpeningsHours']),
        ];
    }

    public function getOpeningsHours(): array
    {
        $horaireEntities = $this->horaireRepository->findAll();
        $openings_hours = [];

        foreach ($horaireEntities as $h) {
            $days = [$h->getJour()]; 
            $openTime = $h->getHeureOuverture()?->format('H:i') ?? '00:00';
            $closeTime = $h->getHeureFermeture()?->format('H:i') ?? '00:00';

            $openings_hours[] = [
                $openTime . '-' . $closeTime,
                $days
            ];
        }

        return $openings_hours;
    }
}