<?php

namespace App\Entity;

use App\Repository\MenuLinkRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MenuLinkRepository::class)]
class MenuLink
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $order_link = null;

    #[ORM\ManyToOne(inversedBy: 'menuLinks')]
    private ?Menu $menu = null;

    #[ORM\ManyToOne(inversedBy: 'menuLinks')]
    private ?PagesList $page = null;

    #[ORM\ManyToOne(inversedBy: 'menuLinks')]
    private ?PostsList $post = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cus_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cus_link = null;

    #[ORM\Column(nullable: true)]
    private ?bool $blank = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderLink(): ?int
    {
        return $this->order_link;
    }

    public function setOrderLink(int $order_link): static
    {
        $this->order_link = $order_link;

        return $this;
    }

    public function getMenu(): ?Menu
    {
        return $this->menu;
    }

    public function setMenu(?Menu $menu): static
    {
        $this->menu = $menu;

        return $this;
    }

    public function getPage(): ?PagesList
    {
        return $this->page;
    }

    public function setPage(?PagesList $page): static
    {
        $this->page = $page;

        return $this;
    }

    public function getPost(): ?PostsList
    {
        return $this->post;
    }

    public function setPost(?PostsList $post): static
    {
        $this->post = $post;

        return $this;
    }

    public function getCusName(): ?string
    {
        return $this->cus_name;
    }

    public function setCusName(?string $cus_name): static
    {
        $this->cus_name = $cus_name;

        return $this;
    }

    public function getCusLink(): ?string
    {
        return $this->cus_link;
    }

    public function setCusLink(?string $cus_link): static
    {
        $this->cus_link = $cus_link;

        return $this;
    }

    public function isBlank(): ?bool
    {
        return $this->blank;
    }

    public function setBlank(?bool $blank): static
    {
        $this->blank = $blank;

        return $this;
    }
}
