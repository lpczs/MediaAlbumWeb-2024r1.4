<?php

namespace Taopix\ControlCentre\Entity;

use Doctrine\ORM\Mapping as ORM;
use Taopix\ControlCentre\Repository\KeywordGroupRepository;

#[ORM\Entity(repositoryClass: KeywordGroupRepository::class), ORM\Table(name: "keywordgroup", schema: "controlcentre")]
class KeywordGroup
{
    #[ORM\Id, ORM\GeneratedValue(strategy: "AUTO"), ORM\Column(name: "id", nullable: false)]
    private int|null $id = null;

    #[ORM\Column(name: "datecreated", nullable: false)]
    private \DateTime|null $dateCreated = null;

    #[ORM\Column(name: "keywordgroupheaderid", nullable: false)]
    private int $keywordGroupHeaderId = 0;

    #[ORM\Column(name: "keywordcode", nullable: false)]
    private string $keywordCode = '';

    #[ORM\Column(name: "sortorder", nullable: false)]
    private int $sortOrder = 0;

    #[ORM\Column(name: "defaultvalue", nullable: false)]
    private string $defaultValue = '';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): KeywordGroup
    {
        $this->id = $id;
        return $this;
    }

    public function getDateCreated(): ?\DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(?\DateTime $dateCreated): KeywordGroup
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    public function getKeywordGroupHeaderId(): int
    {
        return $this->keywordGroupHeaderId;
    }

    public function setKeywordGroupHeaderId(int $keywordGroupHeaderId): KeywordGroup
    {
        $this->keywordGroupHeaderId = $keywordGroupHeaderId;
        return $this;
    }

    public function getKeywordCode(): string
    {
        return $this->keywordCode;
    }

    public function setKeywordCode(string $keywordCode): KeywordGroup
    {
        $this->keywordCode = $keywordCode;
        return $this;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(int $sortOrder): KeywordGroup
    {
        $this->sortOrder = $sortOrder;
        return $this;
    }

    public function getDefaultValue(): string
    {
        return $this->defaultValue;
    }

    public function setDefaultValue(string $defaultValue): KeywordGroup
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

}
