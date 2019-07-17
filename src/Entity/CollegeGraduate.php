<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * 研究生信息
 * @ORM\Entity(repositoryClass="App\Repository\CollegeGraduateRepository")
 */
class CollegeGraduate
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
     * 申请截止时间
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $apply_deadline;

    /**
     * 总花费
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $total_fee;

    /**
     * 申请费
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $apply_fee;

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

    /**
     * 托福成绩
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $toefl_score;

    /**
     * 雅思成绩
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $ielts_score;

    /**
     * GRE成绩
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $gre_score;

    /**
     * SAT成绩
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $sat_score;

    /**
     * Gmat成绩
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $gmat_score;

    /**
     * GPA
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $gpa;

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

    public function getApplyDeadline(): ?string
    {
        return $this->apply_deadline;
    }

    public function setApplyDeadline(?string $apply_deadline): self
    {
        $this->apply_deadline = $apply_deadline;

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

    public function getApplyFee(): ?string
    {
        return $this->apply_fee;
    }

    public function setApplyFee(?string $apply_fee): self
    {
        $this->apply_fee = $apply_fee;

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

    public function getToeflScore(): ?string
    {
        return $this->toefl_score;
    }

    public function setToeflScore(?string $toefl_score): self
    {
        $this->toefl_score = $toefl_score;

        return $this;
    }

    public function getIeltsScore(): ?string
    {
        return $this->ielts_score;
    }

    public function setIeltsScore(?string $ielts_score): self
    {
        $this->ielts_score = $ielts_score;

        return $this;
    }

    public function getGreScore(): ?string
    {
        return $this->gre_score;
    }

    public function setGreScore(?string $gre_score): self
    {
        $this->gre_score = $gre_score;

        return $this;
    }

    public function getSatScore(): ?string
    {
        return $this->sat_score;
    }

    public function setSatScore(?string $sat_score): self
    {
        $this->sat_score = $sat_score;

        return $this;
    }

    public function getGmatScore(): ?string
    {
        return $this->gmat_score;
    }

    public function setGmatScore(?string $gmat_score): self
    {
        $this->gmat_score = $gmat_score;

        return $this;
    }

    public function getGpa(): ?string
    {
        return $this->gpa;
    }

    public function setGpa(?string $gpa): self
    {
        $this->gpa = $gpa;

        return $this;
    }
}
