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
use OpenApi\Annotations as OA;

class ClientController extends AbstractController
{
    /**
     * @OA\Get(
     *     path="/api/client/users/{page}",
     *     operationId="App\Controller\ClientController::clientUsers",
     *     security={"bearer"},
     *     summary="Récupération de la liste des utilisateurs liés au Client connecté",
     *     tags={"Utilisateurs"},
     *     @OA\Parameter(
     *          name="page",
     *          in="path",
     *          description="Numéro de la page de la liste des utilisateurs, on affiche 5 utilisateurs à partir de cet indicateur. (La première page est 0)",
     *          required=true,
     *          @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Liste des utilisateurs lié au client",
     *          @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User")),
     *      ),
     *     @OA\Response(response=404, description="La ressource n'existe pas"),
     *     @OA\Response(response=401, description="Jeton authentifié échoué / invalide")
     * )
     * @Route("/api/client/users/{page}", name="api_client_users", methods={"GET"})
     */
    public function clientUsers(int $page, UserRepository $userRepository, ClientRepository $clientRepository, SerializerInterface $serializer): JsonResponse
    {
        $hateoas = HateoasBuilder::create()->build();
        $client = $clientRepository->find($this->getUser()->getId());
        $paginator = $userRepository->getUserPaginator($client, $page);

        $json = $hateoas->serialize($paginator->getQuery(), 'json', SerializationContext::create()->setGroups(array('client:list')));

        return new JsonResponse($json, 200, [], true);
    }

    /**
     * @OA\Post(
     *     path="/api/client/users",
     *     operationId="App\Controller\ClientController::clientCreateUser",
     *     security={"bearer"},
     *     summary="Ajout d'un utilisateur lié au Client connecté",
     *     tags={"Utilisateurs"},
     *     @OA\Parameter(
     *          name="body",
     *          in="body",
     *          description="Utilisateur devant être ajouté à la base de donnée.",
     *          example={
     *              "email": "test.dylan@gmail.com",
     *               "lastname": "Sardi",
     *               "firstname": "Dylan",
     *               "password": "123456789"
     *           },
     *          required=true,
     *     ),
     *     @OA\Response(
     *          response="201",
     *          description="Utilisateur créé",
     *          @OA\JsonContent(type="string"),
     *      ),
     *     @OA\Response(response=400, description="Erreur de syntaxe"),
     *     @OA\Response(response=401, description="Jeton authentifié échoué / invalide")
     * )
     * @Route("/api/client/users", name="api_client_create_users", methods={"POST"})
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
     * @OA\Delete(
     *     path="/api/client/users/{id}",
     *     operationId="App\Controller\ClientController::clientDeleteUser",
     *     security={"bearer"},
     *     summary="Suppression d'un utilisateur existant lié au Client connecté",
     *     tags={"Utilisateurs"},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          @OA\Schema(type="integer"),
     *          required=true,
     *     ),
     *     @OA\Response(
     *          response="204",
     *          description="Utilisateur supprimé",
     *      ),
     *     @OA\Response(response=404, description="La ressource n'existe pas."),
     *     @OA\Response(response=401, description="Jeton authentifié échoué / invalide.")
     * )
     * @Route("/api/client/users/{idUser}", name="api_client_delete_users", methods={"DELETE"})
     */
    public function clientDeleteUser(int $idUser, Request $request, ClientRepository $clientRepository, UserRepository $userRepository, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        $client = $clientRepository->find($this->getUser()->getId());

        $user = $userRepository->find($idUser);
        if ($user === null) {
            return $this->json([
                'status' => 400,
                'message' => "User does not exist"
            ], 400);
        } else {
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

    /**
     * @OA\Post(
     *     path="/api/login_check",
     *     summary="Récupération d'un Token de connexion",
     *     tags={"Login"},
     *     @OA\Parameter(
     *          name="body",
     *          in="body",
     *          description="Identifiants du Client.",
     *          example={
     *              "email": "test.dylan@gmail.com",
     *              "password": "123456789"
     *           },
     *          required=true,
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Token créé",
     *          @OA\JsonContent(type="string"),
     *      ),
     *     @OA\Response(response=400, description="Erreur de syntaxe"),
     *     @OA\Response(response=401, description="Identifiants invalides")
     * )
     * @Route(path="/api/login_check", name="api_login")
     */
    public function apiLogin()
    {
    }
}
