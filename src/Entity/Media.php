<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\MediaRepository;
use App\State\MakaUserProcessor;
use App\State\MediaStateProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']],
)]
#[Post(processor: MediaStateProcessor::class)]
#[Patch(processor: MediaStateProcessor::class)]
#[Delete()]
#[Get()]
#[GetCollection()]
class Media
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('read')]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read','write'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['read','write'])]
    private ?bool $verified = null;

    #[ORM\ManyToOne(targetEntity: Item::class,inversedBy: 'medias')]
    private ?Item $item = null;

    #[ORM\OneToMany(mappedBy: 'media', targetEntity: Comment::class)]
    private Collection $comments ;

    #[ORM\ManyToOne(targetEntity: MakaUser::class,inversedBy: 'medias')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    #[Groups(['read'])]
    private ?MakaUser $makaUser = null;

    #[ORM\ManyToOne(targetEntity: Type::class,inversedBy: 'medias')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    #[Groups(['read'])]
    private ?Type $type = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ["default" => "CURRENT_TIMESTAMP"])]
    #[Groups(['read'])]
    private ?\DateTimeInterface $createdAt = null;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function isVerified(): ?bool
    {
        return $this->verified;
    }

    public function setVerified(bool $verified): self
    {
        $this->verified = $verified;

        return $this;
    }

    public function getItems(): ?Item
    {
        return $this->item;
    }

    public function setItems(?Item $item): self
    {
        $this->item = $item;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setMedia($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getMedia() === $this) {
                $comment->setMedia(null);
            }
        }

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

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function setItem(?Item $item): void
    {
        $this->item = $item;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

}
