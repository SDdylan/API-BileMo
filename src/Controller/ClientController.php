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
     *          description="Numéro de la page de la liste des utilisateurs, on affiche 5 utilisateurs à partir de cet indicateur. (La première page est 1)",
     *          required=true,
     *          @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Liste des utilisateurs liés au client",
     *          @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User")),
     *      ),
     *     @OA\Response(response=404, description="La ressource n'existe pas"),
     *     @OA\Response(response=401, description="Jeton authentifié échoué / invalide")
     * )
     * @Route("/api/client/users/{page}", name="api_client_users", methods={"GET"})
     */
    public function clientUsers(int $page, UserRepository $userRepository, ClientRepository $clientRepository, SerializerInterface $serializer): JsonResponse
    {
        $page = $page - 1;
        if ($page < 0) {
            return $this->json([
                'status' => 500,
                'message' => "Page number must be >= 1."
            ], 500);
        }
        $hateoas = HateoasBuilder::create()->build();
        $client = $clientRepository->find($this->getUser()->getId());
        $paginator = $userRepository->getUserPaginator($client, $page);
        if (empty($paginator->getQuery())) {
            $nbPages = $userRepository->getNbPages();
            return $this->json([
                'status' => 404,
                'message' => "No resources found at this page, there is " . $nbPages . " page(s) at the moment."
            ], 404);
        }

        $json = $hateoas->serialize($paginator->getQuery(), 'json', SerializationContext::create()->setGroups(array('client:list')));

        return new JsonResponse($json, 200, [], true);
    }


    /**
     * @OA\Get(
     *     path="/api/client/user/{id}",
     *     operationId="App\Controller\ClientController::clientUsers",
     *     security={"bearer"},
     *     summary="Détail d'un Utilisateur.",
     *     tags={"Utilisateurs"},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="Identifiant de l'Utilisateur.",
     *          required=true,
     *          @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Détail de l'utilisateurs lié au client",
     *          @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User")),
     *      ),
     *     @OA\Response(response=404, description="La ressource n'existe pas"),
     *     @OA\Response(response=403, description="Vous n'êtes pas autorisé à accèder à cette ressource."),
     *     @OA\Response(response=401, description="Jeton authentifié échoué / invalide")
     * )
    * @Route("/api/client/user/{id}", name="api_client_user_detail", methods={"GET"})
    */
    public function clientUserDetail(int $id, UserRepository $userRepository, ClientRepository $clientRepository, SerializerInterface $serializer): JsonResponse
    {
        $hateoas = HateoasBuilder::create()->build();

        $user = $userRepository->find($id);
        //Si aucun utilisateur ne correspond à cet id :
        if ($user === null) {
            return $this->json([
                'status' => 400,
                'message' => "User does not exist"
            ], 400);
        } elseif ($user->getClient() !== $this->getUser()) {
            //Si l'utilisateur n'est pas lié au client connecté :
            return $this->json([
                'status' => 403,
                'message' => "Not authorized to see this resource."
            ], 403);
        }
        try {
            $json = $hateoas->serialize($user, 'json', SerializationContext::create()->setGroups(array('user:detail')));
            return new JsonResponse($json, 200, [], true);
        } catch (NotEncodableValueException $e) {
            return $this->json([
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            ], 400);
        }
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

            return $this->json([
                'status' => 201,
                'message' => "User created."
            ], 201);
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

                return $this->json([
                    'status' => 204,
                    'message' => "User deleted."
                ], 201);
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
