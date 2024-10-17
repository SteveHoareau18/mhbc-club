<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\EventType;
class EventController extends AbstractController
{
    #[Route('/events', name: 'event_list')]
    public function list(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findAll();

        return $this->render('event/list.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/event/register/{id}', name: 'event_register', methods: ['GET', 'POST'])]
    public function register(Event $event, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($event->getLimitedSpace() <= 0) {
            $this->addFlash('error', 'Cet événement est complet.');
            return $this->redirectToRoute('event_list');
        }

        if ($request->isMethod('POST')) {
            $participants = $request->request->get('participants');

            if ($event->getLimitedSpace() >= $participants) {
                $event->setLimitedSpace($event->getLimitedSpace() - $participants);

                $entityManager->persist($event);
                $entityManager->flush();

                $this->addFlash('success', 'Inscription réussie !');
            } else {
                $this->addFlash('error', 'Pas assez de places disponibles.');
            }

            return $this->redirectToRoute('event_list');
        }

        return $this->render('event/register.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/event/add', name: 'add_event')]
    public function addEvent(EntityManagerInterface $entityManager): Response
    {
        $event = new Event();
        $event->setName('Tournoi de Handball');
        $event->setDescription('Un tournoi pour tester l\'ajout d\'événements.');
        $event->setDate(new \DateTime('2024-11-20'));
        $event->setPlace('Montpellier');
        $event->setType('Tournoi');
        $event->setLimitedSpace(50);

        $entityManager->persist($event);
        $entityManager->flush();

        return new Response('Événement ajouté !');
    }
    #[Route('/event/create', name: 'event_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new Event();

        $form = $this->createForm(EventType::class, $event);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('event_list');
        }

        return $this->render('event/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
