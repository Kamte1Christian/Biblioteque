<?php
// src/Dto/CreateAbonnementInput.php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CreateAbonnementInput
{
    /**
     * @Assert\NotBlank()
     * @Assert\Type("int")
     */
    private ?int $userId = null;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("int")
     */
    private ?int $typeAbonnementId = null;


    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    public function getTypeAbonnementId(): ?int
    {
        return $this->typeAbonnementId;
    }

    public function setTypeAbonnementId(int $typeAbonnementId): self
    {
        $this->typeAbonnementId = $typeAbonnementId;
        return $this;
    }

}
