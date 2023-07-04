<?php

namespace App\Entity;

use App\Repository\EmailsListRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmailsListRepository::class)]
class EmailsList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $email_name = null;

    #[ORM\Column(length: 255)]
    private ?string $email_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmailName(): ?string
    {
        return $this->email_name;
    }

    public function setEmailName(string $email_name): self
    {
        $this->email_name = $email_name;

        return $this;
    }

    public function getEmailId(): ?string
    {
        return $this->email_id;
    }

    public function setEmailId(string $email_id): self
    {
        $this->email_id = $email_id;

        return $this;
    }
}
