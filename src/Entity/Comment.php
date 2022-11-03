<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 2555)]
    private ?string $text = null;

    #[ORM\ManyToOne(targetEntity: Media::class, inversedBy: 'comments')]
    private ?Media $media = null;

    #[ORM\ManyToOne(targetEntity: MakaUser::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?MakaUser $makaUser = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(?Media $media): self
    {
        $this->media = $media;

        return $this;
    }

    public function getMakaUser(): ?MakaUser
    {
        return $this->makaUser;
    }

    public function setMakaUser(?MakaUser $makaUser): self
    {
        $this->makaUser = $makaUser;

        return $this;
    }
}
