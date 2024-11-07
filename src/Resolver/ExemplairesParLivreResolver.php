<?php
// src/GraphQL/Resolver/BookResolver.php
namespace App\GraphQL\Resolver;

use App\Repository\ExemplaireRepository;
use App\Repository\ExemplairesRepository;
use App\Repository\LivreRepository;
use App\Repository\LivresRepository;
use GraphQL\Type\Definition\Argument as DefinitionArgument;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Resolver\ResolverMap;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class ExemplairesParLivreResolver
{
    private ExemplairesRepository $exemplaireRepository;
    private LivresRepository $livreRepository;

    public function __construct(
        ExemplairesRepository $exemplaireRepository,
        LivresRepository $livreRepository
    ) {
        $this->exemplaireRepository = $exemplaireRepository;
        $this->livreRepository = $livreRepository;
    }

    public function countExemplairesForBook(DefinitionArgument $args): int
    {
        $bookId = $args['bookId'];
        return $this->exemplaireRepository->count(['livre' => $bookId]);
    }

    public function countExemplaires($book, array $context, ResolveInfo $info): int
    {
        return $this->exemplaireRepository->count(['livre' => $book->getId()]);
    }
}
