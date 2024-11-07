<?php

namespace App\Resolver;

use App\Repository\AbonnementRepository;
use App\Repository\AbonnementsRepository;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;
use DateTime;
use GraphQL\Type\Definition\Argument as DefinitionArgument;

class EmpruntsEnCoursQuery
{

     private AbonnementsRepository $abonnementRepository;

    public function __construct(AbonnementsRepository $abonnementRepository)
    {
        $this->abonnementRepository = $abonnementRepository;
    }

    public function __invoke()
    {

    }
    
    public function subscriptionsEndingInTwoDays(DefinitionArgument $args)
    {
        $today = new DateTime();
        $endInTwoDays = (new DateTime())->modify('+2 days');

        return $this->abonnementRepository->findSubscriptionsEndingIn($endInTwoDays);
    }
}
