<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * 本科生信息
 * @ORM\Entity(repositoryClass="App\Repository\CollegeUndergraduateRepository")
 */
class CollegeUndergraduate
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
     * offer发放时间
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $offer_distribution_date;

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
     * 住宿费
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $boarding_fee;

    /**
     * 生活费
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $living_fee;

    /**
     * 书本费
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $book_fee;

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
     * SAT成绩
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $sat_score;

    /**
     * SAT2成绩
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $sat2_score;

    /**
     * ACT成绩
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $act_score;

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

    public function getOfferDistributionDate(): ?string
    {
        return $this->offer_distribution_date;
    }

    public function setOfferDistributionDate(?string $offer_distribution_date): self
    {
        $this->offer_distribution_date = $offer_distribution_date;

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

    public function getBoardingFee(): ?string
    {
        return $this->boarding_fee;
    }

    public function setBoardingFee(?string $boarding_fee): self
    {
        $this->boarding_fee = $boarding_fee;

        return $this;
    }

    public function getLivingFee(): ?string
    {
        return $this->living_fee;
    }

    public function setLivingFee(?string $living_fee): self
    {
        $this->living_fee = $living_fee;

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

    public function getSatScore(): ?string
    {
        return $this->sat_score;
    }

    public function setSatScore(?string $sat_score): self
    {
        $this->sat_score = $sat_score;

        return $this;
    }

    public function getSat2Score(): ?string
    {
        return $this->sat2_score;
    }

    public function setSat2Score(?string $sat2_score): self
    {
        $this->sat2_score = $sat2_score;

        return $this;
    }

    public function getActScore(): ?string
    {
        return $this->act_score;
    }

    public function setActScore(?string $act_score): self
    {
        $this->act_score = $act_score;

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
