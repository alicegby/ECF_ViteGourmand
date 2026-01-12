<?php 

namespace App\Service;

use App\Entity\Menu;
use App\Repository\HoraireRepository;
use DateTime;
use DateTimeInterface;

class MenuConditionChecker
{
    private HoraireRepository $horaireRepository; 

    public function __construct(HoraireRepository $horaireRepository)
    {
        $this->horaireRepository = $horaireRepository;
    }

    public function check(
        Menu $menu,
        DateTimeInterface $dateLivraison,
        DateTimeInterface $heureLivraison
    ): array {
        $errors = [];
        $now = new DateTime();

        // HORAIRES D’OUVERTURE
        $jourLivraison = strtolower($dateLivraison->format('l'));

        $jourMap = [
            'monday'    => 'Lundi',
            'tuesday'   => 'Mardi',
            'wednesday' => 'Mercredi',
            'thursday'  => 'Jeudi',
            'friday'    => 'Vendredi',
            'saturday'  => 'Samedi',
            'sunday'    => 'Dimanche',
        ];

        $jourFr = $jourMap[$jourLivraison] ?? null;

        if (!$jourFr) {
            $errors[] = "Jour de livraison invalide.";
        } else {
            $horaire = $this->horaireRepository->findOneBy([
                'jour' => $jourFr
            ]);

            if (!$horaire) {
                $errors[] = "Aucun horaire défini pour le $jourFr.";
            } else {
                $ouverture = DateTime::createFromFormat(
                    'H:i',
                    $horaire->getHeureOuverture()->format('H:i')
                );

                $fermeture = DateTime::createFromFormat(
                    'H:i',
                    $horaire->getHeureFermeture()->format('H:i')
                );

                if ($heureLivraison < $ouverture || $heureLivraison > $fermeture) {
                    $errors[] = sprintf(
                        "Le %s, les livraisons sont possibles uniquement entre %s et %s.",
                        strtolower($jourFr),
                        $ouverture->format('H:i'),
                        $fermeture->format('H:i')
                    );
                }
            }
        }

        // CONDITIONS DES MENUS
        foreach ($menu->getConditions() as $condition) {
            $libelle = strtolower($condition->getLibelle());

            // Délai minimum
            if (preg_match('/(\d+)\s*(jour|jours|h|heure|heures)/', $libelle, $m)) {
                $valeur = (int) $m[1];
                $unite = $m[2];

                $dateMini = clone $now;

                if (str_starts_with($unite, 'jour')) {
                    $dateMini->modify("+$valeur days");
                } else {
                    $dateMini->modify("+$valeur hours");
                }

                if ($dateLivraison < $dateMini) {
                    $errors[] = "Ce menu doit être commandé au moins {$valeur} {$unite} à l'avance.";
                }
            }

            // Période de disponibilité
            if (preg_match('/du (\d{2}\/\d{2}) au (\d{2}\/\d{2})/', $libelle, $m)) {
                $year = (int) (new DateTime())->format('Y');

                $debut = DateTime::createFromFormat('d/m/Y', $m[1] . '/' . $year);
                $fin   = DateTime::createFromFormat('d/m/Y', $m[2] . '/' . $year);

                if ($dateLivraison < $debut || $dateLivraison > $fin) {
                    $errors[] = "Ce menu n'est pas disponible à cette date.";
                }
            }
        }

        return array_unique($errors);
    }
}