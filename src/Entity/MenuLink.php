<?php

namespace App\Entity;

use App\Repository\MenuLinkRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\Column(nullable: true)]
    private ?array $cus_name = null;

    #[ORM\Column(nullable: true)]
    private ?array $cus_link = null;

    #[ORM\Column(nullable: true)]
    private ?bool $blank = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'menuLinks')]
    private ?self $parent = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
    private Collection $menuLinks;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'sous_menus')]
    private ?self $menuLink = null;

    #[ORM\OneToMany(mappedBy: 'menuLink', targetEntity: self::class)]
    private Collection $sous_menus;

    public function __construct()
    {
        $this->menuLinks = new ArrayCollection();
        $this->sous_menus = new ArrayCollection();
    }

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

    public function getCusName(): ?array
    {
        return $this->cus_name;
    }

    public function setCusName(?array $cus_name): static
    {
        $this->cus_name = $cus_name;

        return $this;
    }

    public function getCusLink(): ?array
    {
        return $this->cus_link;
    }

    public function setCusLink(?array $cus_link): static
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

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getMenuLinks(): Collection
    {
        return $this->menuLinks;
    }

    public function addMenuLink(self $menuLink): static
    {
        if (!$this->menuLinks->contains($menuLink)) {
            $this->menuLinks->add($menuLink);
            $menuLink->setParent($this);
        }

        return $this;
    }

    public function removeMenuLink(self $menuLink): static
    {
        if ($this->menuLinks->removeElement($menuLink)) {
            // set the owning side to null (unless already changed)
            if ($menuLink->getParent() === $this) {
                $menuLink->setParent(null);
            }
        }

        return $this;
    }

    public function getMenuLink(): ?self
    {
        return $this->menuLink;
    }

    public function setMenuLink(?self $menuLink): static
    {
        $this->menuLink = $menuLink;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getSousMenus(): Collection
    {
        return $this->sous_menus;
    }

    public function addSousMenu(self $sousMenu): static
    {
        if (!$this->sous_menus->contains($sousMenu)) {
            $this->sous_menus->add($sousMenu);
            $sousMenu->setMenuLink($this);
        }

        return $this;
    }

    public function removeSousMenu(self $sousMenu): static
    {
        if ($this->sous_menus->removeElement($sousMenu)) {
            // set the owning side to null (unless already changed)
            if ($sousMenu->getMenuLink() === $this) {
                $sousMenu->setMenuLink(null);
            }
        }

        return $this;
    }
}
