<?php
// src/Dto/CreateAbonnementInput.php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CreateEmpruntInput
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
    private ?int $exemplaireId = null;

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    public function getExemplaireId(): ?int
    {
        return $this->exemplaireId;
    }

    public function setExemplaireId(int $exemplaireId): self
    {
        $this->exemplaireId = $exemplaireId;
        return $this;
    }
}
