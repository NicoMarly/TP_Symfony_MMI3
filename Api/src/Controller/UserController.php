<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user/create', name: 'create_user', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);

        $user = new User();

        $user->setEmail($data['email']);

        $password = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($password);

        $user->setRoles(['ROLE_USER']);

        $user->setName($data['name']);

        $user->setPhoneNumber($data['phone']);

        $em->persist($user);
        $em->flush();

        return $this->json(['message' => 'Utilisateur créé avec succès'], Response::HTTP_CREATED);
    }

    #[Route('user/read', name: 'read_user', methods: ['GET'])]
    public function readUserList(EntityManagerInterface $em): Response
    {
        $users = $em->getRepository(User::class)->findAll();

        if ($users) {
            $data = array_map(function ($user) {
                return [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'name' => $user->getName(),
                    'phone' => $user->getPhoneNumber()
                ];
            }, $users);

            return $this->json(['message' => 'Liste des utilisateurs :', 'data' => $data], Response::HTTP_OK);
        } else {
            return $this->json(['message' => 'Pas d\'utilisateurs trouvés'], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('user/{id}/read', name: 'read_user_by_id', methods: ['GET'])]
    public function readUserById(EntityManagerInterface $em, $id): Response
    {
        $user = $em->getRepository(User::class)->find($id);

        if ($user) {
            $data = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getName(),
                'phone' => $user->getPhoneNumber()
            ];

            return $this->json(['message' => 'Information de l\'utilisateur :', 'data' => [$data]], Response::HTTP_OK);
        } else {
            return $this->json(['message' => 'Pas d\'utilisateurs trouvés'], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('/user/{id}/update', name: 'update_user', methods: ['PUT'])]
    public function updateUserById(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em, $id): Response
    {
        $user = $em->getRepository(User::class)->find($id);

        if ($user) {
            $data = json_decode($request->getContent(), true);

            if ($data['email']) {
                $user->setEmail($data['email']);
            } else {
                return $this->json(['message' => 'Le champ email est requis.'], Response::HTTP_NOT_MODIFIED);
            }

            if ($data['password']) {
                $password = $passwordHasher->hashPassword($user, $data['password']);
                $user->setPassword($password);
            } else {
                return $this->json(['message' => 'Le champ password est requis.'], Response::HTTP_NOT_MODIFIED);
            }

            if ($data['name']) {
                $user->setName($data['name']);
            } else {
                return $this->json(['message' => 'Le champ name est requis.'], Response::HTTP_NOT_MODIFIED);
            }

            if ($data['phone']) {
                $user->setPhoneNumber($data['phone']);
            } else {
                return $this->json(['message' => 'Le champ phone est requis.'], Response::HTTP_NOT_MODIFIED);
            }

            $em->persist($user);
            $em->flush();

            return $this->json(['message' => 'Utilisateur mis à jour avec succès'], Response::HTTP_OK);
        } else {
            return $this->json(['message' => 'Pas d\'utilisateur trouvé'], Response::HTTP_NOT_FOUND);
        }
    }

    #[Route('user/{id}/delete', name: 'delete_user', methods: ['DELETE'])]
    public function DeleteUserById(EntityManagerInterface $em, $id): Response
    {
        $user = $em->getRepository(User::class)->find($id);

        if ($user) {
            $em->remove($user);
            $em->flush();

            return $this->json(['message' => 'Utilisateur supprimé avec succès'], Response::HTTP_OK);
        }

        return $this->json(['message' => 'Pas d\'utilisateur trouvé'], Response::HTTP_NOT_FOUND);
    }
}
