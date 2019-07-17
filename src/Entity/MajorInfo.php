<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MajorInfoRepository")
 */
class MajorInfo
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name_cn;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name_en;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $category_cn;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $category_en;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameCn(): ?string
    {
        return $this->name_cn;
    }

    public function setNameCn(string $name_cn): self
    {
        $this->name_cn = $name_cn;

        return $this;
    }

    public function getNameEn(): ?string
    {
        return $this->name_en;
    }

    public function setNameEn(string $name_en): self
    {
        $this->name_en = $name_en;

        return $this;
    }

    public function getCategoryCn(): ?string
    {
        return $this->category_cn;
    }

    public function setCategoryCn(string $category_cn): self
    {
        $this->category_cn = $category_cn;

        return $this;
    }

    public function getCategoryEn(): ?string
    {
        return $this->category_en;
    }

    public function setCategoryEn(string $category_en): self
    {
        $this->category_en = $category_en;

        return $this;
    }
}
