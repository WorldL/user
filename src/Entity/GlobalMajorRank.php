<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GlobalMajorRankRepository")
 */
class GlobalMajorRank
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $global_major_info_id;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $college_name_cn;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $college_name_en;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $rank;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $country;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $college_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGlobalMajorInfoId(): ?int
    {
        return $this->global_major_info_id;
    }

    public function setGlobalMajorInfoId(int $global_major_info_id): self
    {
        $this->global_major_info_id = $global_major_info_id;

        return $this;
    }

    public function getCollegeNameCn(): ?string
    {
        return $this->college_name_cn;
    }

    public function setCollegeNameCn(string $college_name_cn): self
    {
        $this->college_name_cn = $college_name_cn;

        return $this;
    }

    public function getCollegeNameEn(): ?string
    {
        return $this->college_name_en;
    }

    public function setCollegeNameEn(string $college_name_en): self
    {
        $this->college_name_en = $college_name_en;

        return $this;
    }

    public function getRank(): ?string
    {
        return $this->rank;
    }

    public function setRank(string $rank): self
    {
        $this->rank = $rank;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCollegeId(): ?int
    {
        return $this->college_id;
    }

    public function setCollegeId(?int $college_id): self
    {
        $this->college_id = $college_id;

        return $this;
    }
}
