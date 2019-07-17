<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MajorRepository")
 */
class Major
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
    private $name_cn;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name_en;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $category_cn;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $category_en;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $gender_ratio;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $course;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $intro;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $future_trend;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $employment_status;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $visa_sensitivity;

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

    public function getGenderRatio(): ?string
    {
        return $this->gender_ratio;
    }

    public function setGenderRatio(string $gender_ratio): self
    {
        $this->gender_ratio = $gender_ratio;

        return $this;
    }

    public function getCourse(): ?string
    {
        return $this->course;
    }

    public function setCourse(?string $course): self
    {
        $this->course = $course;

        return $this;
    }

    public function getIntro(): ?string
    {
        return $this->intro;
    }

    public function setIntro(?string $intro): self
    {
        $this->intro = $intro;

        return $this;
    }

    public function getFutureTrend(): ?string
    {
        return $this->future_trend;
    }

    public function setFutureTrend(?string $future_trend): self
    {
        $this->future_trend = $future_trend;

        return $this;
    }

    public function getEmploymentStatus(): ?string
    {
        return $this->employment_status;
    }

    public function setEmploymentStatus(?string $employment_status): self
    {
        $this->employment_status = $employment_status;

        return $this;
    }

    public function getVisaSensitivity(): ?string
    {
        return $this->visa_sensitivity;
    }

    public function setVisaSensitivity(?string $visa_sensitivity): self
    {
        $this->visa_sensitivity = $visa_sensitivity;

        return $this;
    }
}
