<?php

namespace App\Controller;

use App\Entity\Vehicule;
use App\Form\VehiculeType;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class VehiculeController extends AbstractController
{
    #[Route('/admin/vehicule', name: 'app_vehicule_index', methods: ['GET'])]
public function index(VehiculeRepository $vehiculeRepository): Response
{
    $vehiculesWithCounts = $vehiculeRepository->findAllWithReservationCount();

    
    $vehicules = [];
    foreach ($vehiculesWithCounts as $data) {
        /** @var Vehicule $vehicule */
        $vehicule = $data[0]; 
        $vehicule->reservationCount = $data['reservationCount']; 
    }

    return $this->render('vehicule/index.html.twig', [
        'vehicules' => $vehicules,
    ]);
}


    #[Route('/admin/vehicule/new', name: 'app_vehicule_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $vehicule = new Vehicule();
        $form = $this->createForm(VehiculeType::class, $vehicule);

        $form->remove('statut');

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $vehicule->setStatut(1);
            $vehicule->setImg("1.jpeg");
            $vehicule->setImmatricule($this->matricule($vehicule->getMarque()));

            $entityManager->persist($vehicule);
            $entityManager->flush();

            return $this->redirectToRoute('app_vehicule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vehicule/new.html.twig', [
            'vehicule' => $vehicule,
            'form' => $form,
        ]);
    }

    #[Route('vehicule/{id}', name: 'app_vehicule_show', methods: ['GET'])]
    public function show(int $id, VehiculeRepository $vehiculeRepository): Response
    {
        $data = $vehiculeRepository->findWithReservationCount($id);

        if (!$data) {
            throw $this->createNotFoundException('Véhicule introuvable.');
        }

        /** @var Vehicule $vehicule */
        $vehicule = $data[0]; 
        $reservationCount = $data['reservationCount']; 

        return $this->render('vehicule/show.html.twig', [
            'vehicule' => $vehicule,
            'reservationCount' => $reservationCount,
        ]);
    }


    #[Route('/admin/vehicule/{id}/edit', name: 'app_vehicule_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Vehicule $vehicule, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_vehicule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vehicule/edit.html.twig', [
            'vehicule' => $vehicule,
            'form' => $form,
        ]);
    }

    #[Route('/admin/vehicule/{id}', name: 'app_vehicule_delete', methods: ['POST'])]
    public function delete(Request $request, Vehicule $vehicule, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $vehicule->getId(), $request->get('_token'))) {
            $entityManager->remove($vehicule);
            $entityManager->flush();

            $this->addFlash('success', 'Le véhicule et ses commentaires associés ont été supprimés.');
        }

        return $this->redirectToRoute('app_vehicule_index', [], Response::HTTP_SEE_OTHER);
    }

    private function matricule($marque)
    {
        $str = substr($marque, 0, 2) . "-";

        for ($i = 0; $i < 4; $i++) {
            $str .= chr(rand(0, 25) + 65);
        }

        $str .= '-';

        for ($i = 0; $i < 3; $i++) {
            $str .= rand(0, 9);
        }

        return $str;
    }
}
