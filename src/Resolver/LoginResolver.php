<?php
namespace App\Resolver;

use ApiPlatform\GraphQl\Resolver\MutationResolverInterface;
use App\Entity\User;
use App\Dto\LoginResponse;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class LoginResolver implements MutationResolverInterface
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;
    private JWTTokenManagerInterface $jwtManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface $jwtManager
    ) {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->jwtManager = $jwtManager;
    }

    public function __invoke(?object $item, array $context): ?LoginResponse
    {
        $args = $context['args'] ?? [];

        if (empty($args['email']) || empty($args['password'])) {
            throw new \InvalidArgumentException("Email and password are required.");
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $args['email']]);
        if (!$user || !$this->passwordHasher->isPasswordValid($user, $args['password'])) {
            throw new AuthenticationException('Invalid credentials.');
        }

        // Generate the JWT token
        $token = $this->jwtManager->create($user);

        // Return a LoginResponse object
        return new LoginResponse($token, $user);
    }
}
