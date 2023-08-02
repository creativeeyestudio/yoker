<?php

namespace App\Entity;

use App\Repository\PagesListRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PagesListRepository::class)]
class PagesList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column]
    private array $page_name = [];

    #[ORM\Column(type: 'string', length: 255)]
    private $page_url;

    #[ORM\Column(type: 'string', length: 255)]
    private $page_id;

    #[ORM\Column]
    private ?bool $blocked_page = null;

    #[ORM\Column]
    private ?bool $status = null;

    #[ORM\Column(nullable: true)]
    private array $page_content = [];

    #[ORM\Column(nullable: true)]
    private array $page_meta_title = [];

    #[ORM\Column]
    private array $page_meta_desc = [];

    #[ORM\OneToMany(mappedBy: 'page', targetEntity: MenuLink::class)]
    private Collection $menuLinks;

    public function __construct()
    {
        $this->menuLinks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPageName(): ?array
    {
        return $this->page_name;
    }

    public function setPageName(?array $page_name): self
    {
        $this->page_name = $page_name;

        return $this;
    }

    public function getPageUrl(): ?string
    {
        return $this->page_url;
    }

    public function setPageUrl(string $page_url): self
    {
        $this->page_url = $page_url;

        return $this;
    }

    public function getPageId(): ?string
    {
        return $this->page_id;
    }

    public function setPageId(string $page_id): self
    {
        $this->page_id = $page_id;

        return $this;
    }

    public function isBlockedPage(): ?bool
    {
        return $this->blocked_page;
    }

    public function setBlockedPage(bool $blocked_page): self
    {
        $this->blocked_page = $blocked_page;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getPageContent(): array
    {
        return $this->page_content;
    }

    public function setPageContent(?array $page_content): static
    {
        $this->page_content = $page_content;

        return $this;
    }

    public function getPageMetaTitle(): array
    {
        return $this->page_meta_title;
    }

    public function setPageMetaTitle(?array $page_meta_title): static
    {
        $this->page_meta_title = $page_meta_title;

        return $this;
    }

    public function getPageMetaDesc(): array
    {
        return $this->page_meta_desc;
    }

    public function setPageMetaDesc(array $page_meta_desc): static
    {
        $this->page_meta_desc = $page_meta_desc;

        return $this;
    }

    /**
     * @return Collection<int, MenuLink>
     */
    public function getMenuLinks(): Collection
    {
        return $this->menuLinks;
    }

    public function addMenuLink(MenuLink $menuLink): static
    {
        if (!$this->menuLinks->contains($menuLink)) {
            $this->menuLinks->add($menuLink);
            $menuLink->setPage($this);
        }

        return $this;
    }

    public function removeMenuLink(MenuLink $menuLink): static
    {
        if ($this->menuLinks->removeElement($menuLink)) {
            // set the owning side to null (unless already changed)
            if ($menuLink->getPage() === $this) {
                $menuLink->setPage(null);
            }
        }

        return $this;
    }
}
