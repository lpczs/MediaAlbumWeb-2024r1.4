<?php

namespace Taopix\ControlCentre\Entity;

use Doctrine\ORM\Mapping as ORM;
use Taopix\ControlCentre\Repository\KeywordRepository;

#[ORM\Entity(repositoryClass: KeywordRepository::class), ORM\Table(name: "keywords", schema: "controlcentre")]
#[ORM\Index(columns: ["code"], name: "code")]
class Keyword
{
    #[ORM\Id, ORM\GeneratedValue(strategy: "AUTO"), ORM\Column(name: "id", nullable: false)]
    private int|null $id = null;

    #[ORM\Column(name: "datecreated", nullable: false)]
    private \DateTime|null $dateCreated = null;

    #[ORM\Column(name: "ref", nullable: false)]
    private int $ref = 0;

    #[ORM\Column(name: "code", length: 50, nullable: false)]
    private string $code = '';

    #[ORM\Column(name: "name", length: 2048, nullable: false)]
    private string $name = '';

    #[ORM\Column(name: "description", length: 1024, nullable: false)]
    private string $description = '';

    #[ORM\Column(name: "type", length: 10, nullable: false)]
    private string $type = '';

    #[ORM\Column(name: "maxlength", nullable: false)]
    private int $maxLength = 0;

    #[ORM\Column(name: "height", nullable: false)]
    private int $height = 0;

    #[ORM\Column(name: "width", nullable: false)]
    private int $width = 0;

    #[ORM\Column(name: "flags", length: 4096, nullable: false)]
    private string $flags = '';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Keyword
    {
        $this->id = $id;
        return $this;
    }

    public function getDateCreated(): ?\DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(?\DateTime $dateCreated): Keyword
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    public function getRef(): int
    {
        return $this->ref;
    }

    public function setRef(int $ref): Keyword
    {
        $this->ref = $ref;
        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): Keyword
    {
        $this->code = $code;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Keyword
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Keyword
    {
        $this->description = $description;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): Keyword
    {
        $this->type = $type;
        return $this;
    }

    public function getMaxLength(): int
    {
        return $this->maxLength;
    }

    public function setMaxLength(int $maxLength): Keyword
    {
        $this->maxLength = $maxLength;
        return $this;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function setHeight(int $height): Keyword
    {
        $this->height = $height;
        return $this;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function setWidth(int $width): Keyword
    {
        $this->width = $width;
        return $this;
    }

    public function getFlags(): string
    {
        return $this->flags;
    }

    public function setFlags(string $flags): Keyword
    {
        $this->flags = $flags;
        return $this;
    }

}
