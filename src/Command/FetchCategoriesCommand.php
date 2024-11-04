<?php

namespace App\Command;

use App\Entity\Categories;
use App\Service\OpenLibraryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\Response;

#[AsCommand(
    name: 'app:fetch-categories',
    description: 'Fetches book categories from the Open Library API and saves them to the database',
)]
class FetchCategoriesCommand extends Command
{
    private OpenLibraryService $openLibraryService;
    private EntityManagerInterface $entityManager;

    public function __construct(OpenLibraryService $openLibraryService, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->openLibraryService = $openLibraryService;
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        // Configuration de la commande sans argument ni option supplémentaire
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Fetching categories from Open Library API');

        try {
            $categoriesData = $this->openLibraryService->fetchCategories();

            foreach ($categoriesData as $categoryData) {
                // Vérifie si la catégorie existe déjà dans la base de données
                $existingCategory = $this->entityManager->getRepository(Categories::class)->findOneBy(['categorie' => $categoryData['name']]);

                if (!$existingCategory) {
                    $category = new Categories();
                    $category->setCategorie($categoryData['name']);
                    $this->entityManager->persist($category);
                }
            }

            $this->entityManager->flush();
            $io->success('Catégories récupérées et stockées avec succès.');

        } catch (\Exception $e) {
            $io->error('Erreur lors de la récupération des catégories : ' . $e->getMessage());
            return Command::FAILURE;
        }

        $io->success('Tâche terminée.');
        return Command::SUCCESS;
    }
}
