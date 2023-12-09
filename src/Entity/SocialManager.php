<?php

namespace App\Entity;

use App\Repository\SocialManagerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SocialManagerRepository::class)]
class SocialManager
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $facebook = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $twitter = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $instagram = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $youtube = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pinterest = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $slideshare = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $xing = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $angelList = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $glassdoor = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $behance = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $meetup = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $reddit = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $quora = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $whatsapp = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFacebook(): ?string
    {
        return $this->facebook;
    }

    public function setFacebook(?string $facebook): static
    {
        $this->facebook = $facebook;

        return $this;
    }

    public function getTwitter(): ?string
    {
        return $this->twitter;
    }

    public function setTwitter(?string $twitter): static
    {
        $this->twitter = $twitter;

        return $this;
    }

    public function getInstagram(): ?string
    {
        return $this->instagram;
    }

    public function setInstagram(?string $instagram): static
    {
        $this->instagram = $instagram;

        return $this;
    }

    public function getYoutube(): ?string
    {
        return $this->youtube;
    }

    public function setYoutube(?string $youtube): static
    {
        $this->youtube = $youtube;

        return $this;
    }

    public function getPinterest(): ?string
    {
        return $this->pinterest;
    }

    public function setPinterest(?string $pinterest): static
    {
        $this->pinterest = $pinterest;

        return $this;
    }

    public function getSlideshare(): ?string
    {
        return $this->slideshare;
    }

    public function setSlideshare(?string $slideshare): static
    {
        $this->slideshare = $slideshare;

        return $this;
    }

    public function getXing(): ?string
    {
        return $this->xing;
    }

    public function setXing(?string $xing): static
    {
        $this->xing = $xing;

        return $this;
    }

    public function getAngelList(): ?string
    {
        return $this->angelList;
    }

    public function setAngelList(?string $angelList): static
    {
        $this->angelList = $angelList;

        return $this;
    }

    public function getGlassdoor(): ?string
    {
        return $this->glassdoor;
    }

    public function setGlassdoor(?string $glassdoor): static
    {
        $this->glassdoor = $glassdoor;

        return $this;
    }

    public function getBehance(): ?string
    {
        return $this->behance;
    }

    public function setBehance(?string $behance): static
    {
        $this->behance = $behance;

        return $this;
    }

    public function getMeetup(): ?string
    {
        return $this->meetup;
    }

    public function setMeetup(?string $meetup): static
    {
        $this->meetup = $meetup;

        return $this;
    }

    public function getReddit(): ?string
    {
        return $this->reddit;
    }

    public function setReddit(?string $reddit): static
    {
        $this->reddit = $reddit;

        return $this;
    }

    public function getQuora(): ?string
    {
        return $this->quora;
    }

    public function setQuora(?string $quora): static
    {
        $this->quora = $quora;

        return $this;
    }

    public function getWhatsapp(): ?string
    {
        return $this->whatsapp;
    }

    public function setWhatsapp(?string $whatsapp): static
    {
        $this->whatsapp = $whatsapp;

        return $this;
    }
}
