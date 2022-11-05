<?php

namespace App\Controller;

use App\Entity\MakaUser;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('api/user')]
class UserController extends AbstractController
{
    private SerializerInterface $serializer;

    private EntityManagerInterface $em;

    private UserRepository $userRepository;

    public function __construct(SerializerInterface $serializer, EntityManagerInterface $em, UserRepository $userRepository)
    {
        $this->serializer = $serializer;
        $this->em = $em;
        $this->userRepository = $userRepository;
    }

    #[Route('/', name: 'maka_user_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $userList = $this->userRepository->findAll();
        $jsonUserList = $this->serializer->serialize($userList, 'json', ['groups' => 'list_maka_user']);

        return new JsonResponse($jsonUserList, Response::HTTP_OK, [], true);
    }

    #[Route('/new', name: 'maka_user_new', methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        $makaUser = $this->serializer->deserialize($request->getContent(), MakaUser::class, 'json');
        $makaUser->setCreatedAt(new \DateTime('now'));
        $this->em->persist($makaUser);
        $this->em->flush();

        $jsonMakaUser = $this->serializer->serialize($makaUser, 'json', ['groups' => 'show_maka_user']);

        return new JsonResponse($jsonMakaUser, Response::HTTP_CREATED, [], true);
    }

    #[Route('/{id}', name: 'maka_user_show', requirements: ["id" => "\d+"], methods: ['GET'])]
    public function show(MakaUser $user): JsonResponse
    {
        $jsonUser = $this->serializer->serialize($user, 'json', ['groups' => 'show_maka_user']);

        return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);

    }

    #[Route('/edit/{id}', name: 'maka_user_edit', methods: ['GET', 'POST'])]
    public function edit(MakaUser $currentUser, Request $request, UserRepository $userRepository): JsonResponse
    {
        $updateUser = $this->serializer->deserialize($request->getContent(),
            MakaUser::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $currentUser]);

        $this->em->persist($updateUser);
        $this->em->flush();

        $jsonUpdateUser = $this->serializer->serialize($updateUser, 'json', ['groups' => 'show_maka_user']);

        return new JsonResponse($jsonUpdateUser, Response::HTTP_OK, [], true);
    }

    #[Route('/delete/{id}', name: 'maka_user_delete', methods: ['GET'])]
    public function delete(int $id): JsonResponse
    {
        $user = $this->userRepository->find($id);

        // onvérifie si un utilisateur existe en BDD.
        if ($user) {
            $this->em->remove($user);
            $this->em->flush();

            $jsonResult = $this->serializer->serialize([
                    'delete'    => true,
                    'firstName' => $user->getFirstName(),
                    'lastName'  => $user->getLastName(),
                ]
                , 'json', ['groups' => 'show_maka_user']);

        } else {
            //On envoi un message d'erreur si l'utilisateur n'est pas retrouvé en BDD
            $jsonResult = $this->serializer->serialize([
                    'error' => 'Cet ID ne correspond à aucun utilisateur',
                ]
                , 'json', ['groups' => 'show_maka_user']);

        }

        return new JsonResponse($jsonResult, Response::HTTP_OK, [], true);
    }
}
