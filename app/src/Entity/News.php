<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
/**
 * @ORM\Entity(repositoryClass="App\Repository\NewsRepository")
 * @UniqueEntity("title")
 * @ORM\HasLifecycleCallbacks
 */
class News
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @Groups({"News_default"})
     * @var UuidInterface
     */
    private $id;

    /**
     * @Groups({"News_default"})
     * @ORM\Column(type="string", length=255)
     */
    private $coverText;

    /**
     * @Groups({"News_default"})
     * @ORM\Column(type="string", length=255)
     */
    private $coverImage;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @Groups({"News_default"})
     */
    private $text;

    /**
     * @ORM\Column(name="title", type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Groups({"News_default"})
     */
    private $title;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"News_default"})
     */
    private $created;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"News_default"})
     * @var DateTime|null
     */
    private $updated;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="news")
     * @Groups({"News_default"})
     */
    private $user;

    /**
     * @ORM\PrePersist
     *
     * @throws Exception;
     *
     */
    public function onPrePersist(): void
    {
        $this->id = Uuid::uuid4();
        $this->created = new DateTime('NOW');
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate(): void
    {
        $this->updated = new DateTime('NOW');
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }
    public function getCoverText(): ?string
    {
        return $this->coverText;
    }

    public function setCoverText(string $coverText): self
    {
        $this->coverText = $coverText;

        return $this;
    }

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage(string $coverImage): self
    {
        $this->coverImage = $coverImage;

        return $this;
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    //public function getCreated(): ?\DateTimeInterface
    public function getCreated(): ?string
    {

        return $this->created->format('d.m.Y');;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(\DateTimeInterface $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
