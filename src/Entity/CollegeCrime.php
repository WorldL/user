<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CollegeCrimeRepository")
 */
class CollegeCrime
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
     * 年份
     * @ORM\Column(type="string", length=10)
     */
    private $year;

    /**
     * 持枪逮捕
     * @ORM\Column(type="integer", nullable=true)
     */
    private $gunmen_arrested;

    /**
     * 毒品逮捕
     * @ORM\Column(type="integer", nullable=true)
     */
    private $drug_arrested;

    /**
     * 酗酒逮捕
     * @ORM\Column(type="integer", nullable=true)
     */
    private $drunk_arrested;

    /**
     * 持枪记过
     * @ORM\Column(type="integer", nullable=true)
     */
    private $gunmen_recorded;

    /**
     * 毒品记过
     * @ORM\Column(type="integer", nullable=true)
     */
    private $drug_recorded;

    /**
     * 酗酒记过
     * @ORM\Column(type="integer", nullable=true)
     */
    private $drunk_recorded;

    /**
     * 家暴
     * @ORM\Column(type="integer", nullable=true)
     */
    private $domestic_violence;

    /**
     * 约会犯罪
     * @ORM\Column(type="integer", nullable=true)
     */
    private $dating_crime;

    /**
     * 跟踪
     * @ORM\Column(type="integer", nullable=true)
     */
    private $track;

    /**
     * 谋杀
     * @ORM\Column(type="integer", nullable=true)
     */
    private $murder;

    /**
     * 过失杀人
     * @ORM\Column(type="integer", nullable=true)
     */
    private $manslaughter;

    /**
     * 强奸
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rape;

    /**
     * 性骚扰
     * @ORM\Column(type="integer", nullable=true)
     */
    private $sexual_harassment;

    /**
     * 乱伦
     * @ORM\Column(type="integer", nullable=true)
     */
    private $incest;

    /**
     * 抢劫
     * @ORM\Column(type="integer", nullable=true)
     */
    private $robbery;

    /**
     * 袭击
     * @ORM\Column(type="integer", nullable=true)
     */
    private $assault;

    /**
     * 盗窃
     * @ORM\Column(type="integer", nullable=true)
     */
    private $steal;

    /**
     * 偷车
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vehicle_steal;

    /**
     * 纵火
     * @ORM\Column(type="integer", nullable=true)
     */
    private $incendiary;

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

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(string $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getGunmenArrested(): ?int
    {
        return $this->gunmen_arrested;
    }

    public function setGunmenArrested(?int $gunmen_arrested): self
    {
        $this->gunmen_arrested = $gunmen_arrested;

        return $this;
    }

    public function getDrugArrested(): ?int
    {
        return $this->drug_arrested;
    }

    public function setDrugArrested(?int $drug_arrested): self
    {
        $this->drug_arrested = $drug_arrested;

        return $this;
    }

    public function getDrunkArrested(): ?int
    {
        return $this->drunk_arrested;
    }

    public function setDrunkArrested(?int $drunk_arrested): self
    {
        $this->drunk_arrested = $drunk_arrested;

        return $this;
    }

    public function getGunmenRecorded(): ?int
    {
        return $this->gunmen_recorded;
    }

    public function setGunmenRecorded(?int $gunmen_recorded): self
    {
        $this->gunmen_recorded = $gunmen_recorded;

        return $this;
    }

    public function getDrugRecorded(): ?int
    {
        return $this->drug_recorded;
    }

    public function setDrugRecorded(?int $drug_recorded): self
    {
        $this->drug_recorded = $drug_recorded;

        return $this;
    }

    public function getDrunkRecorded(): ?int
    {
        return $this->drunk_recorded;
    }

    public function setDrunkRecorded(?int $drunk_recorded): self
    {
        $this->drunk_recorded = $drunk_recorded;

        return $this;
    }

    public function getDomesticViolence(): ?int
    {
        return $this->domestic_violence;
    }

    public function setDomesticViolence(?int $domestic_violence): self
    {
        $this->domestic_violence = $domestic_violence;

        return $this;
    }

    public function getDatingCrime(): ?int
    {
        return $this->dating_crime;
    }

    public function setDatingCrime(?int $dating_crime): self
    {
        $this->dating_crime = $dating_crime;

        return $this;
    }

    public function getTrack(): ?int
    {
        return $this->track;
    }

    public function setTrack(?int $track): self
    {
        $this->track = $track;

        return $this;
    }

    public function getMurder(): ?int
    {
        return $this->murder;
    }

    public function setMurder(?int $murder): self
    {
        $this->murder = $murder;

        return $this;
    }

    public function getManslaughter(): ?int
    {
        return $this->manslaughter;
    }

    public function setManslaughter(?int $manslaughter): self
    {
        $this->manslaughter = $manslaughter;

        return $this;
    }

    public function getRape(): ?int
    {
        return $this->rape;
    }

    public function setRape(?int $rape): self
    {
        $this->rape = $rape;

        return $this;
    }

    public function getSexualHarassment(): ?int
    {
        return $this->sexual_harassment;
    }

    public function setSexualHarassment(?int $sexual_harassment): self
    {
        $this->sexual_harassment = $sexual_harassment;

        return $this;
    }

    public function getIncest(): ?int
    {
        return $this->incest;
    }

    public function setIncest(?int $incest): self
    {
        $this->incest = $incest;

        return $this;
    }

    public function getRobbery(): ?int
    {
        return $this->robbery;
    }

    public function setRobbery(?int $robbery): self
    {
        $this->robbery = $robbery;

        return $this;
    }

    public function getAssault(): ?int
    {
        return $this->assault;
    }

    public function setAssault(?int $assault): self
    {
        $this->assault = $assault;

        return $this;
    }

    public function getSteal(): ?int
    {
        return $this->steal;
    }

    public function setSteal(?int $steal): self
    {
        $this->steal = $steal;

        return $this;
    }

    public function getVehicleSteal(): ?int
    {
        return $this->vehicle_steal;
    }

    public function setVehicleSteal(?int $vehicle_steal): self
    {
        $this->vehicle_steal = $vehicle_steal;

        return $this;
    }

    public function getIncendiary(): ?int
    {
        return $this->incendiary;
    }

    public function setIncendiary(?int $incendiary): self
    {
        $this->incendiary = $incendiary;

        return $this;
    }
}
