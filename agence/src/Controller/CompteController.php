<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Reservation;
use App\Entity\Vehicule;
use App\Form\ReservationType;
use Symfony\Component\HttpFoundation\Request;

#[Route('/client')]
final class CompteController extends AbstractController
{

    

    #[Route('/vehicule/{id}', name: 'app_client_vehicule')]
public function client(Vehicule $vehicule, Request $request, EntityManagerInterface $manager): Response
{
    $reserver = new Reservation;

    $form = $this->createForm(ReservationType::class, $reserver);

    // Retirer les champs inutiles
    $form->remove('prix');
    $form->remove('vehicule');
    $form->remove('client');

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $dateDebut = $reserver->getDateDebut();
        $dateFin = $reserver->getDateFin();

        if ($dateFin < $dateDebut) {
            $this->addFlash('error', 'La date de fin ne peut pas être avant la date de début.');
            return $this->render('home/reserver.html.twig', [
                "vehicule" => $vehicule,
                "form" => $form
            ]);
        }

        $reserver->setPrix($request->get('prixReservation'));
        $reserver->setClient($this->getUser());
        $reserver->setVehicule($vehicule);

        $manager->persist($reserver);
        $manager->flush();

        $this->addFlash('success', 'Réservation effectuée avec succès !');

        return $this->redirectToRoute('app_home');  
    }

    return $this->render('home/reserver.html.twig', [
        "vehicule" => $vehicule,
        "form" => $form
    ]);
}
}
