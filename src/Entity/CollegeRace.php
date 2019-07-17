<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CollegeRaceRepository")
 */
class CollegeRace
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
     * 本科or研究生
     * @ORM\Column(type="string", length=50)
     */
    private $diploma;

    /**
     * 白人
     * @ORM\Column(type="float", nullable=true)
     */
    private $caucasian;

    /**
     * 非裔
     * @ORM\Column(type="float", nullable=true)
     */
    private $aferican;

    /**
     * 拉丁裔
     * @ORM\Column(type="float", nullable=true)
     */
    private $latino;

    /**
     * 亚裔
     * @ORM\Column(type="float", nullable=true)
     */
    private $asian;

    /**
     * 留学生
     * @ORM\Column(type="float", nullable=true)
     */
    private $international_student;

    /**
     * 其他
     * @ORM\Column(type="float", nullable=true)
     */
    private $other;

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

    public function getDiploma(): ?string
    {
        return $this->diploma;
    }

    public function setDiploma(string $diploma): self
    {
        $this->diploma = $diploma;

        return $this;
    }

    public function getCaucasian(): ?float
    {
        return $this->caucasian;
    }

    public function setCaucasian(?float $caucasian): self
    {
        $this->caucasian = $caucasian;

        return $this;
    }

    public function getAferican(): ?float
    {
        return $this->aferican;
    }

    public function setAferican(?float $aferican): self
    {
        $this->aferican = $aferican;

        return $this;
    }

    public function getLatino(): ?float
    {
        return $this->latino;
    }

    public function setLatino(?float $latino): self
    {
        $this->latino = $latino;

        return $this;
    }

    public function getAsian(): ?float
    {
        return $this->asian;
    }

    public function setAsian(?float $asian): self
    {
        $this->asian = $asian;

        return $this;
    }

    public function getInternationalStudent(): ?float
    {
        return $this->international_student;
    }

    public function setInternationalStudent(?float $international_student): self
    {
        $this->international_student = $international_student;

        return $this;
    }

    public function getOther(): ?float
    {
        return $this->other;
    }

    public function setOther(?float $other): self
    {
        $this->other = $other;

        return $this;
    }
}
