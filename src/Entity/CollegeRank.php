<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CollegeRankRepository")
 */
class CollegeRank
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $rank_org;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $rank_scope;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $college_type;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $college_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $college_name_en;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $college_name_cn;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $rank;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $region;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $rank_type;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRankOrg(): ?string
    {
        return $this->rank_org;
    }

    public function setRankOrg(string $rank_org): self
    {
        $this->rank_org = $rank_org;

        return $this;
    }

    public function getRankScope(): ?string
    {
        return $this->rank_scope;
    }

    public function setRankScope(string $rank_scope): self
    {
        $this->rank_scope = $rank_scope;

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

    public function getCollegeNameEn(): ?string
    {
        return $this->college_name_en;
    }

    public function setCollegeNameEn(?string $college_name_en): self
    {
        $this->college_name_en = $college_name_en;

        return $this;
    }

    public function getCollegeNameCn(): ?string
    {
        return $this->college_name_cn;
    }

    public function setCollegeNameCn(?string $college_name_cn): self
    {
        $this->college_name_cn = $college_name_cn;

        return $this;
    }

    public function getRank(): ?string
    {
        return $this->rank;
    }

    public function setRank(?string $rank): self
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

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getCollegeType(): ?string
    {
        return $this->college_type;
    }

    public function setCollegeType(?string $college_type): self
    {
        $this->college_type = $college_type;

        return $this;
    }

    public function getRankType(): ?string
    {
        return $this->rank_type;
    }

    public function setRankType(string $rank_type): self
    {
        $this->rank_type = $rank_type;

        return $this;
    }
}
