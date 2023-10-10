<?php

namespace App\Controller;

use App\Entity\User;
use App\Previewer\UserPreviewer;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Gesdinet\JWTRefreshTokenBundle\Generator\RefreshTokenGenerator;
use Gesdinet\JWTRefreshTokenBundle\Generator\RefreshTokenGeneratorInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;


#[Route('/registration', name: 'api_registration')]
class SecurityController extends ApiController
{
    private EntityManagerInterface $em;
    private UserRepository $userRepository;

    public function __construct(EntityManagerInterface $em,  UserRepository $userRepository)
    {
        $this->em = $em;
        $this->userRepository = $userRepository;
    }

    /**
     *  Register a user
     */
    #[OA\Response(
        response: 200,
        description: "Successfully registered",
        content: new OA\JsonContent(properties:[
            new OA\Property(property: "token", type: "string"),
            new OA\Property(property: "refresh_token", type: "string")
        ] )
    )
    ]
    #[OA\Response(
        response: 422,
        description: "Unprocessable content",
    )
    ]
    #[Route(name: '', methods: ['POST'])]
    public function registration(Request                        $request,
                                 UserPasswordHasherInterface    $passwordEncoder,
                                 ValidatorInterface             $validator,
                                 JWTTokenManagerInterface       $jwtManager,
                                 RefreshTokenGeneratorInterface $refreshTokenGenerator,
                                 RefreshTokenManagerInterface   $refreshTokenManager): JsonResponse
    {
        $request = $request->toArray();
        try {
            $this->setSoftDeleteable($this->em, false);
            $user = $this->userRepository->findOneBy(['login' => $request['username']]);

            if ($user) return $this->respondValidationError('User with this login is already exist');

            $user = new User();

            $user->setLogin($request['username']);
            $user->setFio($request['fio']);
            $user->setEmail($request['email']);
            $user->setRoles(["ROLE_USER"]);

            $user->setPassword(
                $passwordEncoder->hashPassword(
                    $user,
                    $request['password']
                )
            );

            if (count($validator->validate($user)) !== 0) return $this->respondValidationError();

            $this->em->persist($user);
            $this->em->flush();

            $accessToken = $jwtManager->create($user);
            $refreshToken = $refreshTokenGenerator->createForUserWithTtl($user, 2592000);

            $refreshTokenManager->save($refreshToken);
            return $this->response(['token' => $accessToken, 'refresh_token' => $refreshToken->getRefreshToken()]);
        }
        catch (Exception){
            return $this->respondValidationError();
        }
    }

}
