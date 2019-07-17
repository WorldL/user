<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FamousAlumniRepository")
 */
class FamousAlumni
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
     * @ORM\Column(type="string", length=100)
     */
    private $name_cn;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name_en;

    /**
     * @ORM\Column(type="string", length=500)
     */
    private $intro;

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

    public function getIntro(): ?string
    {
        return $this->intro;
    }

    public function setIntro(string $intro): self
    {
        $this->intro = $intro;

        return $this;
    }
}
