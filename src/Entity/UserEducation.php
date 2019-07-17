<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserEducationRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 */
class UserEducation
{
    use TimestampableEntity;
    use SoftDeleteableEntity;
    
    public static $degreeMap = [
        'MIDDLE_SCHOOL_STUDENT' => '高中',
        'UNDERGRADUATE' => '学士',
        'POSTGRADUATE' => '硕士',
        'DOCTOR' => '博士',
        'OTHER' => '其他',
    ];

    public static $statusMap = [
        'UNAUTHORIZED' => '未认证',
        'AUTHORIZING' => '认证中',
        'AUTHORIZED' => '已认证',
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $user_id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $degree;

    /**
     * @ORM\Column(type="integer")
     */
    private $college_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $major_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $graduate_year;

    /**
     * @ORM\Column(type="integer")
     */
    private $graduate_month;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $as_default;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $verify_email;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getDegree(): ?string
    {
        return $this->degree;
    }

    public function setDegree(string $degree): self
    {
        $this->degree = $degree;

        return $this;
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

    public function getMajorId(): ?int
    {
        return $this->major_id;
    }

    public function setMajorId(int $major_id): self
    {
        $this->major_id = $major_id;

        return $this;
    }

    public function getGraduateYear(): ?int
    {
        return $this->graduate_year;
    }

    public function setGraduateYear(int $graduate_year): self
    {
        $this->graduate_year = $graduate_year;

        return $this;
    }

    public function getGraduateMonth(): ?int
    {
        return $this->graduate_month;
    }

    public function setGraduateMonth(int $graduate_month): self
    {
        $this->graduate_month = $graduate_month;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getAsDefault(): ?string
    {
        return $this->as_default;
    }

    public function setAsDefault(?string $as_default): self
    {
        $this->as_default = $as_default;

        return $this;
    }

    public function getVerifyEmail(): ?string
    {
        return $this->verify_email;
    }

    public function setVerifyEmail(?string $verify_email): self
    {
        $this->verify_email = $verify_email;

        return $this;
    }
}
