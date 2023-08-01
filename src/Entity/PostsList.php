<?php

namespace App\Entity;

use App\Repository\PostsListRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostsListRepository::class)]
class PostsList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column]
    private array $post_name = [];

    #[ORM\Column]
    private array $post_content = [];

    #[ORM\Column]
    private array $post_meta_title = [];

    #[ORM\Column(nullable: true)]
    private ?array $post_meta_desc = null;

    #[ORM\Column(length: 255)]
    private ?string $post_url = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $post_thumb = null;

    #[ORM\Column(nullable: true)]
    private ?bool $online = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\ManyToOne(inversedBy: 'postsLists')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: MenuLink::class)]
    private Collection $menuLinks;

    public function __construct()
    {
        $this->menuLinks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPostName(): array
    {
        return $this->post_name;
    }

    public function setPostName(array $post_name): static
    {
        $this->post_name = $post_name;

        return $this;
    }

    public function getPostUrl(): ?string
    {
        return $this->post_url;
    }

    public function setPostUrl(string $post_url): static
    {
        $this->post_url = $post_url;

        return $this;
    }

    public function getPostThumb(): ?string
    {
        return $this->post_thumb;
    }

    public function setPostThumb(?string $post_thumb): static
    {
        $this->post_thumb = $post_thumb;

        return $this;
    }

    public function getPostContent(): array
    {
        return $this->post_content;
    }

    public function setPostContent(array $post_content): static
    {
        $this->post_content = $post_content;

        return $this;
    }

    public function getPostMetaTitle(): array
    {
        return $this->post_meta_title;
    }

    public function setPostMetaTitle(array $post_meta_title): static
    {
        $this->post_meta_title = $post_meta_title;

        return $this;
    }

    public function getPostMetaDesc(): ?array
    {
        return $this->post_meta_desc;
    }

    public function setPostMetaDesc(?array $post_meta_desc): static
    {
        $this->post_meta_desc = $post_meta_desc;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function isOnline(): ?bool
    {
        return $this->online;
    }

    public function setOnline(?bool $online): static
    {
        $this->online = $online;

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
            $menuLink->setPost($this);
        }

        return $this;
    }

    public function removeMenuLink(MenuLink $menuLink): static
    {
        if ($this->menuLinks->removeElement($menuLink)) {
            // set the owning side to null (unless already changed)
            if ($menuLink->getPost() === $this) {
                $menuLink->setPost(null);
            }
        }

        return $this;
    }
}
