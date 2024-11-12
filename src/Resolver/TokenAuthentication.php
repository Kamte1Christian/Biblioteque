<?php

namespace App\Resolver;

use ApiPlatform\GraphQl\Resolver\MutationResolverInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface as EncoderJWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class TokenAuthentication implements MutationResolverInterface
{
    private EntityManagerInterface $entityManager;
    private EncoderJWTEncoderInterface $jwtEncoder;

    public function __construct(
        EntityManagerInterface $entityManager,
        EncoderJWTEncoderInterface $jwtEncoder
    ) {
        $this->entityManager = $entityManager;
        $this->jwtEncoder = $jwtEncoder;
    }

    // Update method signature to match the interface
    public function __invoke(?object $item, array $context): ?object
    {
        $token = $context['args']['token'] ?? null;

        if (!$token) {
            throw new AuthenticationException('Token is required.');
        }

        try {
            // Decode the token to get user data
            $data = $this->jwtEncoder->decode($token);
        } catch (\Exception $e) {
            throw new AuthenticationException('Invalid token.');
        }

        // Retrieve user based on the token data (typically the user's ID or username)
        $userId = $data['id'] ?? null;

        if (!$userId) {
            throw new AuthenticationException('Token data is invalid.');
        }

        $user = $this->entityManager->getRepository(User::class)->find($userId);

        if (!$user) {
            throw new AuthenticationException('User not found.');
        }

        return $user;
    }
}
