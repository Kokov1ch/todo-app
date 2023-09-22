<?php

namespace App\Controller;

use App\Entity\User;
use App\Previewer\UserPreviewer;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/users', name: 'users_')]
class UserController extends ApiController
{
    private UserRepository $userRepository;
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em,
                                UserRepository $userRepository)
    {
        $this->em = $em;
        $this->userRepository = $userRepository;
    }

    #[Route(name: 'get', methods: ['GET'])]
    public function getUsers(UserPreviewer $userPreviewer): JsonResponse
    {
        $users = $this->userRepository->findAll();
        $this->setSoftDeleteable($this->em, false);

        $userPreviews = array_map(
            fn(User $user): array => $userPreviewer->preview($user),
            $users
        );

        return $this->response($userPreviews);
    }

    #[Route(name: 'post', methods: ['POST'])]
    public function postUser(Request                     $request,
                             UserPasswordHasherInterface $passwordEncoder,
                             ValidatorInterface          $validator): JsonResponse
    {
        $request = $request->toArray();
        try {
            $this->setSoftDeleteable($this->em, false);
            $user = $this->userRepository->findOneBy(['login' => $request['login']]);
            if ($user) {
                if ($user->getDeletedAt()) {
                    $user->setDeletedAt(null);
                    $this->em->persist($user);
                    $this->em->flush();
                    $this->setSoftDeleteable($this->em);
                    return $this->respondWithSuccess("User added successfully");
                }

                return $this->respondValidationError('User with this login is already exist');
            }
            $user = new User();

            $user->setLogin($request['login']);
            $user->setPassword($request['password']);

            if (isset($request['fio'])) {
                $user->setFio($request['fio']);
            }
            if (isset($request['roles'])) {
                $user->setRoles([$request['roles']]);
            }
            if (isset($request['email'])) {
                $user->setEmail($request['email']);
            }

            if (count($validator->validate($user)) !== 0) return $this->respondValidationError();

                $user->setPassword(
                    $passwordEncoder->hashPassword(
                        $user,
                        $request['password']
                    )
                );

                $this->em->persist($user);
                $this->em->flush();

                return $this->respondWithSuccess("User added successfully");
        } catch (Exception) {
            return $this->respondValidationError();
        }
    }

    #[Route(name: 'delete', methods: ['DELETE'])]
    public function deleteUsers(Request $request): JsonResponse
    {
        // TODO: Сделать каскадное удаление тасков

        $request = $request->toArray();

        try {
            $userIds = $request['user_ids'];

            $this->em->beginTransaction();
            foreach ($userIds as $userId) {
                $user = $this->userRepository->find($userId);
                if (!$user) {
                    $this->em->rollback();
                    return $this->respondNotFound("User not found");
                }
                $this->em->remove($user);
            }
            $this->em->flush();
            $this->em->commit();

            return $this->respondWithSuccess("Users deleted successfully");
        } catch (Exception) {
            return $this->respondValidationError();
        }
    }

    #[Route('/{userId}', name: 'get_by_id', requirements: ['userId' => '\d+'], methods: ['GET'])]
    public function getUserById(UserPreviewer $userPreviewer, int $userId): JsonResponse
    {
        $user = $this->userRepository->find($userId);
        if (!$user) {
            return $this->respondNotFound("User not found");
        }

        $this->setSoftDeleteable($this->em, false);

        return $this->response($userPreviewer->preview($user));
    }

    #[Route('/{userId}', name: 'put_by_id', requirements: ['userId' => '\d+'], methods: ['PUT'])]
    public function updateUser(Request                     $request,
                               UserPasswordHasherInterface $passwordEncoder,
                               ValidatorInterface          $validator,
                               int                         $userId): JsonResponse
    {
        $user = $this->userRepository->find($userId);
        if (!$user) {
            return $this->respondNotFound("User not found");
        }

        $request = $request->toArray();

        try {
            if (isset($request['login'])) {
                $login = $request['login'];
                if (!$login) {
                    throw new Exception();
                }
                if ($this->userRepository->findOneBy(['login' => $request['login']])) {
                    return $this->respondValidationError('User with this login is already exist');
                }

                $user->setLogin($login);
            }
            if (isset($request['password'])) {
                $password = $request['password'];
                if (!$password) {
                    throw new Exception();
                }
                $user->setPassword($password);
            }
            if (isset($request['fio'])) {
                $user->setFio($request['fio']);
            }
            if (isset($request['roles'])) {
                $user->setRoles([$request['roles']]);
            }
            if (isset($request['email'])) {
                $user->setEmail($request['email']);
            }

            $validator->validate($user);

            if (isset($request['password'])) {
                $user->setPassword($passwordEncoder->hashPassword($user, $request['password']));
            }

            $this->em->flush();

            return $this->respondWithSuccess("User updated successfully");
        } catch (Exception) {
            return $this->respondValidationError();
        }
    }

    #[Route('/{userId}', name: 'delete_by_id', requirements: ['userId' => '\d+'], methods: ['DELETE'])]
    public function deleteUser(int $userId): JsonResponse
    {
        // TODO: Сделать каскадное удаление тасков

        $user = $this->userRepository->find($userId);
        if (!$user) {
            return $this->respondNotFound("User not found");
        }

        $this->em->remove($user);
        $this->em->flush();

        return $this->respondWithSuccess("User deleted successfully");
    }

}
