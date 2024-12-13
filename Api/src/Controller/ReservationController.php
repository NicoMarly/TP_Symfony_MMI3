<?php
namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ReservationController extends AbstractController
{
    #[Route('/reservation/create', name: 'api_reservation_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);

        $day = new \DateTimeImmutable($data['date']);
        $now = new \DateTimeImmutable();
        $now->modify('+1 day');

        if ($day < $now) {
            return $this->json(['message' => 'Les réservations doivent être faites un jour en avance'], Response::HTTP_BAD_REQUEST);
        }

        list($start, $end) = explode('-', $data['timeslot']);
        $timeslotStart = new \DateTimeImmutable($data['date'] . ' ' . $start);
        $timeslotEnd = new \DateTimeImmutable($data['date'] . ' ' . $end);

        $existingReservation = $em->getRepository(Reservation::class)->findOneBy(['date' => $day, 'timeSlotStart' => $timeslotStart, 'timeSlotEnd' => $timeslotEnd]);

        if ($existingReservation) {
            return $this->json(['message' => 'Ce créneau horraire est déjà pris.'], Response::HTTP_BAD_REQUEST);
        }

        $reservation = new Reservation();
        $reservation->setDate($day);
        $reservation->setTimeslotStart($timeslotStart);
        $reservation->setTimeslotEnd($timeslotEnd);
        $reservation->setEventName($data['name']);

        $user = $em->getRepository(User::class)->find($data['user']);
        $reservation->setRelation($user);

        $em->persist($reservation);
        $em->flush();

        return $this->json(['message' => 'Réservation effectuée'], Response::HTTP_CREATED);
    }


    #[Route('/reservation/read', name: 'api_reservation_index', methods: ['GET'])]
    public function index(EntityManagerInterface $em): JsonResponse
    {
        $reservations = $em->getRepository(Reservation::class)->findAll();

        if ($reservations) {
            $data = array_map(function ($reservation) {
                return [
                    'id' => $reservation->getId(),
                    'date' => $reservation->getDate()->format('Y-m-d'),
                    'timeSlot' => $reservation->getTimeSlotStart()->format('H:i') . "-" . $reservation->getTimeSlotEnd()->format('H:i'),
                    'nameEvent' => $reservation->getEventName(),
                    'user' => $reservation->getRelation()->getName()
                ];
            }, $reservations);

            return $this->json(['message' => 'Liste des réservations :', 'data' => $data], Response::HTTP_OK);
        } else {
            return $this->json(['message' => 'Pas de réservations trouvées'], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/reservation/{id}/read', name: 'api_reservation_show', methods: ['GET'])]
    public function show(EntityManagerInterface $em, $id): JsonResponse
    {
        $reservation = $em->getRepository(Reservation::class)->find($id);

        if ($reservation) {
            $data = [
                'id' => $reservation->getId(),
                'date' => $reservation->getDate()->format('Y-m-d'),
                'timeSlot' => $reservation->getTimeSlotStart()->format('H:i') . "-" . $reservation->getTimeSlotEnd()->format('H:i'),
                'nameEvent' => $reservation->getEventName(),
                'user' => $reservation->getRelation()->getName()
            ];

            return $this->json(['message' => 'Information de la réservation :', 'data' => [$data]], Response::HTTP_OK);
        } else {
            return $this->json(['message' => 'Pas de réservations trouvées'], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/reservation/{id}/update', name: 'api_reservation_edit', methods: ['PUT'])]
    public function edit(Request $request, EntityManagerInterface $em, $id): JsonResponse
    {
        $reservation = $em->getRepository(Reservation::class)->find($id);
        if (!$reservation) {
            return new JsonResponse(['message' => 'Reservation not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        $day = new \DateTimeImmutable($data['date']);
        $now = new \DateTimeImmutable();
        $now->modify('+1 day');

        if ($day < $now) {
            return $this->json(['message' => 'Les réservations doivent être faites un jour en avance'], Response::HTTP_BAD_REQUEST);
        }

        list($start, $end) = explode('-', $data['timeslot']);
        $timeslotStart = new \DateTimeImmutable($data['date'] . ' ' . $start);
        $timeslotEnd = new \DateTimeImmutable($data['date'] . ' ' . $end);

        $existingReservation = $em->getRepository(Reservation::class)->findOneBy(['date' => $day, 'timeSlotStart' => $timeslotStart, 'timeSlotEnd' => $timeslotEnd]);

        if ($existingReservation) {
            return $this->json(['message' => 'Ce créneau horraire est déjà pris.'], Response::HTTP_BAD_REQUEST);
        }

        $reservation->setDate($day);
        $reservation->setTimeslotStart($timeslotStart);
        $reservation->setTimeslotEnd($timeslotEnd);
        $reservation->setEventName($data['name']);

        $user = $em->getRepository(User::class)->find($data['user']);
        $reservation->setRelation($user);

        $em->persist($reservation);
        $em->flush();

        return $this->json(['message' => 'Réservation mise à jour'], Response::HTTP_CREATED);
    }

    #[Route('/reservation/{id}/delete', name: 'api_reservation_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $em, $id): JsonResponse
    {
        $reservation = $em->getRepository(Reservation::class)->find($id);

        if ($reservation) {
            $em->remove($reservation);
            $em->flush();

            return $this->json(['message' => 'Réservation supprimée avec succès'], Response::HTTP_OK);
        }

        return $this->json(['message' => 'Pas de réservation trouvée'], Response::HTTP_NOT_FOUND);
    }
}
