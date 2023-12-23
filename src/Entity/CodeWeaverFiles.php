<?php

namespace App\Entity;

use App\Repository\CodeWeaverFilesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CodeWeaverFilesRepository::class)]
class CodeWeaverFiles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $css_file = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $js_file = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCssFile(): ?string
    {
        return $this->css_file;
    }

    public function setCssFile(?string $css_file): static
    {
        $this->css_file = $css_file;

        return $this;
    }

    public function getJsFile(): ?string
    {
        return $this->js_file;
    }

    public function setJsFile(?string $js_file): static
    {
        $this->js_file = $js_file;

        return $this;
    }
}
