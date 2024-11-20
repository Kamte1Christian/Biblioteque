<?php

namespace App\Resolver;

use App\Entity\Emprunt;
use App\Entity\Exemplaire;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use ApiPlatform\GraphQl\Resolver\MutationResolverInterface;
use App\Entity\User;

class ReturnExemplaireResolver implements MutationResolverInterface
{
    private EntityManagerInterface $entityManager;
    private Security $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function __invoke(?object $item, array $context): ?object
    {

        // Retrieve the exemplaire IDs from the GraphQL arguments
        $args = $context['args']['input'] ?? [];
        $userid = (int) $args['Userid'] ?? [];
        $codes = (array) $args['codebars'] ?? [];
        if (!is_array($codes) || empty($codes)) {
            throw new \Exception('Invalid or missing exemplaire code bar.');
        }

           $user = $this->entityManager->getRepository(User::class)->findOneBy(['id'=>$userid]);
          if (!$user) {
            throw new \Exception('User with id '.$userid.' does not exist');
        }
        // Fetch the user's active Emprunt
        $emprunt = $this->entityManager->getRepository(Emprunt::class)
            ->findOneBy(['user' => $user, 'isBacked' => false]);

        if (!$emprunt) {
            throw new \Exception('You have no active Emprunt.');
        }

        // Validate that the exemplaires belong to the user and the active Emprunt
        $exemplairesToReturn = [];
        foreach ($codes as $code) {
            $exemplaire = $this->entityManager->getRepository(Exemplaire::class)->findOneBy(['code_bar'=>$code]);

            if (!$exemplaire || $exemplaire->getEmprunt() !== $emprunt) {
                throw new \Exception("Exemplaire with code bar {$code} does not belong to your active Emprunt.");
            }

            $exemplairesToReturn[] = $exemplaire;
        }

        // Update the status of the Exemplaires and Emprunt
        foreach ($exemplairesToReturn as $exemplaire) {
            $emprunt->removeExemplaire($exemplaire); // Remove the exemplaire from the Emprunt
            $exemplaire->setEmprunt(null); // Mark the exemplaire as available
            $this->entityManager->persist($exemplaire);
        }

        // If all exemplaires are returned, mark the Emprunt as completed
        if ($emprunt->getExemplaire()->isEmpty()) {
            $emprunt->setBacked(true);
            $emprunt->setEffectiveBackAt(new \DateTimeImmutable());
        }

        // Persist changes
        $this->entityManager->persist($emprunt);
        $this->entityManager->flush();

        return $emprunt;
    }
}
