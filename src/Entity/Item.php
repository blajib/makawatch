<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\ItemRepository;
use App\State\ItemStateProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']],
)]
#[Post(processor: ItemStateProcessor::class)]
#[Patch(processor: ItemStateProcessor::class)]
#[Delete()]
#[Get()]
#[GetCollection()]
class Item
{
    public const LEVELS = [1,2,3,4,5,6,7];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read', 'write'])]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read', 'write'])]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['read', 'write'])]
    private ?int $level = null;

    #[ORM\Column]
    #[Groups(['read', 'write'])]
    private ?bool $verified = null;

    #[ORM\OneToMany(mappedBy: 'item', targetEntity: Media::class)]
    #[Groups(['read'])]
    private Collection $medias;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'items')]
    #[Groups(['read'])]
    private Collection $categories;

    #[ORM\ManyToOne(targetEntity: MakaUser::class,inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    #[Groups(['read'])]
    private ?MakaUser $makaUser = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ["default" => "CURRENT_TIMESTAMP"])]
    #[Groups(['read'])]
    private ?\DateTimeInterface $createdAt = null;

    public function __construct()
    {
        $this->medias = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(?int $level): self
    {
        $this->level = $level;

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

    public function getMedias(): Collection
    {
        return $this->medias;
    }

    public function addMedia(Media $media): self
    {
        if (!$this->medias->contains($media)) {
            $this->medias->add($media);
            $media->setItems($this);
        }

        return $this;
    }

    public function removeMedia(Media $media): self
    {
        if ($this->medias->removeElement($media)) {
            // set the owning side to null (unless already changed)
            if ($media->getItems() === $this) {
                $media->setItems(null);
            }
        }

        return $this;
    }

    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }


}
