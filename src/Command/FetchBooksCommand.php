<?php

namespace App\Command;

use App\Entity\Categories;
use App\Entity\Livres;
use App\Service\OpenLibraryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'FetchBooksCommand',
    description: 'Add a short description for your command',
)]
class FetchBooksCommand extends Command
{
// //    private OpenLibraryService $openLibraryService;
//     private EntityManagerInterface $entityManager;

//     public function __construct(OpenLibraryService $openLibraryService, EntityManagerInterface $entityManager)
//     {
//         parent::__construct();
//         $this->openLibraryService = $openLibraryService;
//         $this->entityManager = $entityManager;
//     }

//     protected function configure(): void
//     {
//         $this
//             ->addArgument('category', InputArgument::REQUIRED, 'Category name to fetch books for');
//     }


//    protected function execute(InputInterface $input, OutputInterface $output): int
//     {
//         $io = new SymfonyStyle($input, $output);
//         $category = $input->getArgument('category');

//         $io->title(sprintf('Fetching books for category: %s', $category));

//         try {
//             $booksData = $this->openLibraryService->fetchBooksByCategory($category);

//             foreach ($booksData as $bookData) {
//                 // Vérifie si le livre existe déjà
//                 $existingLivre = $this->entityManager->getRepository(Livres::class)->findOneBy(['titre' => $bookData['title']]);

//                 if (!$existingLivre) {
//                     $livre = new Livres();
//                     $livre->setTitre($bookData['title']);
//                     $livre->setEditionKey($bookData['edition_key'] ?? null);

//                     // Associer la catégorie au livre
//                     $categoryEntity = $this->entityManager->getRepository(Categories::class)->findOneBy(['nom' => $category]);
//                     if ($categoryEntity) {
//                         $livre->setCategorie($categoryEntity);
//                         $this->entityManager->persist($livre);
//                     }
//                 }
//             }

//             $this->entityManager->flush();
//             $io->success('Books fetched and stored successfully.');

//         } catch (\Exception $e) {
//             $io->error('Could not fetch books: ' . $e->getMessage());
//             return Command::FAILURE;
//         }

//         return Command::SUCCESS;
//     }
}
