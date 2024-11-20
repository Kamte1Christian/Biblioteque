<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class ExemplaireInput
{
    #[Assert\NotBlank]
    public string $Book;

    #[Assert\NotBlank]
    #[Assert\Type("integer")]
    public int|string $code_bar;

    #[Assert\Type("string")]
    public ?string $emprunt = null;

    #[Assert\Type("boolean")]
    public bool $state = false;
}

