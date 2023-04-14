<?php

namespace App\Entity;

use App\Repository\UploadTableRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UploadTableRepository::class)]
class UploadTable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $uploadTitle = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $uploadSinger = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ImagePath = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $AudioPath = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $genre = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUploadTitle(): ?string
    {
        return $this->uploadTitle;
    }

    public function setUploadTitle(string $uploadTitle): self
    {
        $this->uploadTitle = $uploadTitle;

        return $this;
    }

    public function getUploadSinger(): ?string
    {
        return $this->uploadSinger;
    }

    public function setUploadSinger(?string $uploadSinger): self
    {
        $this->uploadSinger = $uploadSinger;

        return $this;
    }

    public function getImagePath(): ?string
    {
        return $this->ImagePath;
    }

    public function setImagePath(?string $ImagePath): self
    {
        $this->ImagePath = $ImagePath;

        return $this;
    }

    public function getAudioPath(): ?string
    {
        return $this->AudioPath;
    }

    public function setAudioPath(?string $AudioPath): self
    {
        $this->AudioPath = $AudioPath;

        return $this;
    }

    public function getGenre(): array
    {
        return $this->genre;
    }

    public function setGenre(array $genre): self
    {
        $this->genre = $genre;

        return $this;
    }
}
