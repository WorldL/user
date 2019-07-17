<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CollegeRepoRepository")
 */
class CollegeRepo
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
     * @ORM\Column(type="integer")
     */
    private $sort;

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

    public function getSort(): ?int
    {
        return $this->sort;
    }

    public function setSort(int $sort): self
    {
        $this->sort = $sort;

        return $this;
    }
}
