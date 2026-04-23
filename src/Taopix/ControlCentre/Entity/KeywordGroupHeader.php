<?php

namespace Taopix\ControlCentre\Entity;

use Doctrine\ORM\Mapping as ORM;
use Taopix\ControlCentre\Repository\KeywordGroupHeaderRepository;

#[ORM\Entity(repositoryClass: KeywordGroupHeaderRepository::class), ORM\Table(name: "keywordgroupheader", schema: "controlcentre")]
class KeywordGroupHeader
{
    #[ORM\Id, ORM\GeneratedValue(strategy: "AUTO"), ORM\Column(name: "id", nullable: false)]
    private int|null $id = null;

    #[ORM\Column(name: "datecreated", nullable: false)]
    private \DateTime|null $dateCreated = null;

    #[ORM\Column(name: "groupcode", length: 50, nullable: false)]
    private string $groupCode = '';

    #[ORM\Column(name: "section", length: 10, nullable: false)]
    private string $section = '';

    #[ORM\Column(name: "productcodes", length: 16384, nullable: false)]
    private string $productCodes;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): KeywordGroupHeader
    {
        $this->id = $id;
        return $this;
    }

    public function getDateCreated(): ?\DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(?\DateTime $dateCreated): KeywordGroupHeader
    {
        $this->dateCreated = $dateCreated;
        return $this;
    }

    public function getGroupCode(): string
    {
        return $this->groupCode;
    }

    public function setGroupCode(string $groupCode): KeywordGroupHeader
    {
        $this->groupCode = $groupCode;
        return $this;
    }

    public function getSection(): string
    {
        return $this->section;
    }

    public function setSection(string $section): KeywordGroupHeader
    {
        $this->section = $section;
        return $this;
    }

    public function getProductCodes(): string
    {
        return $this->productCodes;
    }

    public function setProductCodes(string $productCodes): KeywordGroupHeader
    {
        $this->productCodes = $productCodes;
        return $this;
    }

    public function getProductCodeArray()
    {
        return \explode(',', $this->productCodes);
    }
}
