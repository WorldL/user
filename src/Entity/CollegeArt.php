<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CollegeArtRepository")
 */
class CollegeArt
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
     * SIA录取率
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $sia_acceptance_rate;

    /**
     * 平均录取率
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $average_acceptance_rate;

    /**
     * 申请截止时间
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $apply_deadline;

    /**
     * 申请难度
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $apply_difficulty;

    /**
     * 总花费
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $total_fee;

    /**
     * 学费
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $education_fee;

    /**
     * 书本费
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $book_fee;

    /**
     * 住宿费
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $boarding_fee;

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

    public function getSiaAcceptanceRate(): ?string
    {
        return $this->sia_acceptance_rate;
    }

    public function setSiaAcceptanceRate(?string $sia_acceptance_rate): self
    {
        $this->sia_acceptance_rate = $sia_acceptance_rate;

        return $this;
    }

    public function getAverageAcceptanceRate(): ?string
    {
        return $this->average_acceptance_rate;
    }

    public function setAverageAcceptanceRate(?string $average_acceptance_rate): self
    {
        $this->average_acceptance_rate = $average_acceptance_rate;

        return $this;
    }

    public function getApplyDeadline(): ?string
    {
        return $this->apply_deadline;
    }

    public function setApplyDeadline(?string $apply_deadline): self
    {
        $this->apply_deadline = $apply_deadline;

        return $this;
    }

    public function getApplyDifficulty(): ?string
    {
        return $this->apply_difficulty;
    }

    public function setApplyDifficulty(?string $apply_difficulty): self
    {
        $this->apply_difficulty = $apply_difficulty;

        return $this;
    }

    public function getTotalFee(): ?string
    {
        return $this->total_fee;
    }

    public function setTotalFee(?string $total_fee): self
    {
        $this->total_fee = $total_fee;

        return $this;
    }

    public function getEducationFee(): ?string
    {
        return $this->education_fee;
    }

    public function setEducationFee(?string $education_fee): self
    {
        $this->education_fee = $education_fee;

        return $this;
    }

    public function getBookFee(): ?string
    {
        return $this->book_fee;
    }

    public function setBookFee(?string $book_fee): self
    {
        $this->book_fee = $book_fee;

        return $this;
    }

    public function getBoardingFee(): ?string
    {
        return $this->boarding_fee;
    }

    public function setBoardingFee(?string $boarding_fee): self
    {
        $this->boarding_fee = $boarding_fee;

        return $this;
    }
}
