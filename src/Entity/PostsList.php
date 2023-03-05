<?php

namespace App\Entity;

use App\Repository\PostsListRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostsListRepository::class)]
class PostsList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $post_name;

    #[ORM\Column(type: 'string', length: 255)]
    private $post_url;

    #[ORM\Column(type: 'string', length: 255)]
    private $post_id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $post_meta_title;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $post_meta_desc;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $post_meta_title_en = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $post_meta_desc_en = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $post_name_en = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPostName(): ?string
    {
        return $this->post_name;
    }

    public function setPostName(string $post_name): self
    {
        $this->post_name = $post_name;

        return $this;
    }

    public function getPostUrl(): ?string
    {
        return $this->post_url;
    }

    public function setPostUrl(string $post_url): self
    {
        $this->post_url = $post_url;

        return $this;
    }

    public function getPostId(): ?string
    {
        return $this->post_id;
    }

    public function setPostId(string $post_id): self
    {
        $this->post_id = $post_id;

        return $this;
    }

    public function getPostMetaTitle(): ?string
    {
        return $this->post_meta_title;
    }

    public function setPostMetaTitle(string $post_meta_title): self
    {
        $this->post_meta_title = $post_meta_title;

        return $this;
    }

    public function getPostMetaDesc(): ?string
    {
        return $this->post_meta_desc;
    }

    public function setPostMetaDesc(?string $post_meta_desc): self
    {
        $this->post_meta_desc = $post_meta_desc;

        return $this;
    }

    public function getPostMetaTitleEn(): ?string
    {
        return $this->post_meta_title_en;
    }

    public function setPostMetaTitleEn(?string $post_meta_title_en): self
    {
        $this->post_meta_title_en = $post_meta_title_en;

        return $this;
    }

    public function getPostMetaDescEn(): ?string
    {
        return $this->post_meta_desc_en;
    }

    public function setPostMetaDescEn(?string $post_meta_desc_en): self
    {
        $this->post_meta_desc_en = $post_meta_desc_en;

        return $this;
    }

    public function getPostNameEn(): ?string
    {
        return $this->post_name_en;
    }

    public function setPostNameEn(?string $post_name_en): self
    {
        $this->post_name_en = $post_name_en;

        return $this;
    }
}
