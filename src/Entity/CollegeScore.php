<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CollegeScoreRepository")
 */
class CollegeScore
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
    private $college_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $school_motto;

    /**
     * @ORM\Column(type="float")
     */
    private $cost;

    /**
     * @ORM\Column(type="float")
     */
    private $academics;

    /**
     * @ORM\Column(type="float")
     */
    private $application;

    /**
     * @ORM\Column(type="float")
     */
    private $safety;

    /**
     * @ORM\Column(type="float")
     */
    private $diversity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCollegeId(): ?int
    {
        return $this->college_id;
    }

    public function setCollegeId(int $college_id): self
    {
        $this->college_id = $college_id;

        return $this;
    }

    public function getSchoolMotto(): ?string
    {
        return $this->school_motto;
    }

    public function setSchoolMotto(?string $school_motto): self
    {
        $this->school_motto = $school_motto;

        return $this;
    }

    public function getCost(): ?float
    {
        return $this->cost;
    }

    public function setCost(float $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    public function getAcademics(): ?float
    {
        return $this->academics;
    }

    public function setAcademics(float $academics): self
    {
        $this->academics = $academics;

        return $this;
    }

    public function getApplication(): ?float
    {
        return $this->application;
    }

    public function setApplication(float $application): self
    {
        $this->application = $application;

        return $this;
    }

    public function getSafety(): ?float
    {
        return $this->safety;
    }

    public function setSafety(float $safety): self
    {
        $this->safety = $safety;

        return $this;
    }

    public function getDiversity(): ?float
    {
        return $this->diversity;
    }

    public function setDiversity(float $diversity): self
    {
        $this->diversity = $diversity;

        return $this;
    }
}
