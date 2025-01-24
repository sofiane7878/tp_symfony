<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/reservation')]
final class ReservationController extends AbstractController
{
    #[Route(name: 'app_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository): Response
    {
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier que la date de fin est après la date de début
            if ($reservation->getDateDebut() >= $reservation->getDateFin()) {
                $form->get('date_fin')->addError(
                    new \Symfony\Component\Form\FormError('La date de fin doit être après la date de début.')
                );
            }

            if ($form->isValid()) {
                // Calculer le nombre de jours de la réservation
                $dateDebut = $reservation->getDateDebut();
                $dateFin = $reservation->getDateFin();
                $interval = $dateDebut->diff($dateFin);
                $nbJours = $interval->days;

                // Calculer le prix total
                $prixParJour = 50; // Par exemple
                $prixTotal = $nbJours * $prixParJour;

                // Appliquer la remise de 10 % si nécessaire
                if ($prixTotal >= 400) {
                    $prixTotal *= 0.9;
                }

                // Mettre à jour le prix de la réservation
                $reservation->setPrix($prixTotal);

                // Sauvegarder la réservation
                $entityManager->persist($reservation);
                $entityManager->flush();

                return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier que la date de fin est après la date de début
            if ($reservation->getDateDebut() >= $reservation->getDateFin()) {
                $form->get('date_fin')->addError(
                    new \Symfony\Component\Form\FormError('La date de fin doit être après la date de début.')
                );
            }

            if ($form->isValid()) {
                // Calculer le nombre de jours de la réservation
                $dateDebut = $reservation->getDateDebut();
                $dateFin = $reservation->getDateFin();
                $interval = $dateDebut->diff($dateFin);
                $nbJours = $interval->days;

                // Calculer le prix total
                $prixParJour = 50; // Par exemple
                $prixTotal = $nbJours * $prixParJour;

                // Appliquer la remise de 10 % si nécessaire
                if ($prixTotal >= 400) {
                    $prixTotal *= 0.9;
                }

                // Mettre à jour le prix de la réservation
                $reservation->setPrix($prixTotal);

                // Sauvegarder la réservation
                $entityManager->flush();

                return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reservation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
    }
}
