<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GlobalMajorInfoRepository")
 */
class GlobalMajorInfo
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $major_name;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $major_category;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $rank_category;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $major_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMajorName(): ?string
    {
        return $this->major_name;
    }

    public function setMajorName(string $major_name): self
    {
        $this->major_name = $major_name;

        return $this;
    }

    public function getMajorCategory(): ?string
    {
        return $this->major_category;
    }

    public function setMajorCategory(string $major_category): self
    {
        $this->major_category = $major_category;

        return $this;
    }

    public function getRankCategory(): ?string
    {
        return $this->rank_category;
    }

    public function setRankCategory(string $rank_category): self
    {
        $this->rank_category = $rank_category;

        return $this;
    }

    public function getMajorId(): ?int
    {
        return $this->major_id;
    }

    public function setMajorId(?int $major_id): self
    {
        $this->major_id = $major_id;

        return $this;
    }
}
