<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Hateoas\HateoasBuilder;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ClientController extends AbstractController
{
    /**
     * @Route("/client", name="app_client")
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ClientController.php',
        ]);
    }

    /**
     * @Route("/api/client/users", name="api_client_users", methods={"GET"})
     */
    public function clientUsers(UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        $hateoas = HateoasBuilder::create()->build();
        $products = $userRepository->findBy(["client" => $this->getUser()->getId()]);

        $json = $hateoas->serialize($products, 'json', SerializationContext::create()->setGroups(array('client:list')));

        return new JsonResponse($json, 200, [], true);
    }

    /**
     * @Route("/api/client/user", name="api_client_create_users", methods={"POST"})
     */
    public function clientCreateUser(Request $request, ClientRepository $clientRepository, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $client = $clientRepository->find($this->getUser()->getId());
        $json = $request->getContent();
        try {
            $user = $serializer->deserialize($json, User::class, 'json');
            $user->setClient($client)
                ->setRoles(["ROLE_USER"]);

            $errors = $validator->validate($user);
            if (count($errors) > 0) {
                return $this->json($errors, 400);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->json($user, 201, [], ['groups' => 'user:create']);
        } catch (NotEncodableValueException $e) {
            return $this->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * @Route("/api/client/user/{idUser}", name="api_client_delete_users", methods={"DELETE"})
     */
    public function clientDeleteUser(int $idUser, Request $request, ClientRepository $clientRepository, UserRepository $userRepository, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $client = $clientRepository->find($this->getUser()->getId());
        $user = $userRepository->find($idUser);
        try {
            $entityManager->remove($user);
            $client->removeUser($user);
            $entityManager->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (Exception $exception) {
            return $this->json([
                'status' => $exception->getCode(),
                'message' => $exception->getMessage()
            ], $exception->getCode());
        }
    }
}
