<?php

namespace App\Resolver;

use ApiPlatform\GraphQl\Resolver\MutationResolverInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginMutation implements MutationResolverInterface
{
    private UserProviderInterface $userProvider;
    private UserPasswordHasherInterface $passwordEncoder;
    private JWTTokenManagerInterface $jwtManager;

    public function __construct(
        UserProviderInterface $userProvider,
        UserPasswordHasherInterface $passwordEncoder,
        JWTTokenManagerInterface $jwtManager
    ) {
        $this->userProvider = $userProvider;
        $this->passwordEncoder = $passwordEncoder;
        $this->jwtManager = $jwtManager;
    }

    public function __invoke(array $args): array
    {
        $email = $args['email'];
        $password = $args['password'];

        // Load the user by email
        $user = $this->userProvider->loadUserByIdentifier($email);

        if (!$user || !$this->passwordEncoder->isPasswordValid($user, $password)) {
            throw new AuthenticationException('Invalid credentials.');
        }

        // Generate a JWT token
        $token = $this->jwtManager->create($user);

        return ['token' => $token];
    }
}
