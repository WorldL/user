<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CollegeRepository")
 */
class College
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * 中文名
     * @ORM\Column(type="string", length=255)
     */
    private $name_cn;

    /**
     * 英文名
     * @ORM\Column(type="string", length=255)
     */
    private $name_en;

    /**
     * 所在地域
     * @ORM\Column(type="string", length=255)
     */
    private $region;

    /**
     * 本科生学费
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tuition_fee_undergraduate;

    /**
     * 学校性质 综合性、文理、艺术
     * @ORM\Column(type="string", length=32)
     */
    private $category;

    /**
     * 本科托福成绩要求
     * @ORM\Column(type="integer", nullable=true)
     */
    private $toefl_undergraduate;

    /**
     * 本科雅思成绩要求
     * @ORM\Column(type="float", nullable=true)
     */
    private $ielts_undergraduate;

    /**
     * 学校简介
     * @ORM\Column(type="text", nullable=true)
     */
    private $introduction;

    /**
     * 优势学科
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $pro_subject;

    /**
     * 师生比例
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $faculty_student_ratio;

    /**
     * 申请人数
     * @ORM\Column(type="integer", nullable=true)
     */
    private $applications;

    /**
     * 录取人数
     * @ORM\Column(type="integer", nullable=true)
     */
    private $enrollment;

    /**
     * 实际入学人数
     * @ORM\Column(type="integer", nullable=true)
     */
    private $actual_enrollment;

    /**
     * 就业率
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $employment;

    /**
     * 毕业起薪
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $starting_salary;

    /**
     * 男女比例
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $male_female_ratio;

    /**
     * 学生总数
     * @ORM\Column(type="integer", nullable=true)
     */
    private $total_amount_students;

    /**
     * 本科生人数
     * @ORM\Column(type="integer", nullable=true)
     */
    private $total_amount_undergraduate;

    /**
     * 研究生人数
     * @ORM\Column(type="integer", nullable=true)
     */
    private $total_amount_graduate;

    /**
     * 成立时间
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $establish_date;

    /**
     * 学校类型 私立、公立
     * @ORM\Column(type="string", length=50)
     */
    private $type;

    /**
     * 招生办信息
     * @ORM\Column(type="text", nullable=true)
     */
    private $register_office_info;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $possible_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $abbreviation;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $is_show;

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

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(string $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getTuitionFeeUndergraduate(): ?string
    {
        return $this->tuition_fee_undergraduate;
    }

    public function setTuitionFeeUndergraduate(?string $tuition_fee_undergraduate): self
    {
        $this->tuition_fee_undergraduate = $tuition_fee_undergraduate;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getToeflUndergraduate(): ?int
    {
        return $this->toefl_undergraduate;
    }

    public function setToeflUndergraduate(?int $toefl_undergraduate): self
    {
        $this->toefl_undergraduate = $toefl_undergraduate;

        return $this;
    }

    public function getIeltsUndergraduate(): ?float
    {
        return $this->ielts_undergraduate;
    }

    public function setIeltsUndergraduate(?float $ielts_undergraduate): self
    {
        $this->ielts_undergraduate = $ielts_undergraduate;

        return $this;
    }

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(?string $introduction): self
    {
        $this->introduction = $introduction;

        return $this;
    }

    public function getProSubject(): ?string
    {
        return $this->pro_subject;
    }

    public function setProSubject(?string $pro_subject): self
    {
        $this->pro_subject = $pro_subject;

        return $this;
    }

    public function getFacultyStudentRatio(): ?string
    {
        return $this->faculty_student_ratio;
    }

    public function setFacultyStudentRatio(?string $faculty_student_ratio): self
    {
        $this->faculty_student_ratio = $faculty_student_ratio;

        return $this;
    }

    public function getApplications(): ?int
    {
        return $this->applications;
    }

    public function setApplications(?int $applications): self
    {
        $this->applications = $applications;

        return $this;
    }

    public function getEnrollment(): ?int
    {
        return $this->enrollment;
    }

    public function setEnrollment(?int $enrollment): self
    {
        $this->enrollment = $enrollment;

        return $this;
    }

    public function getActualEnrollment(): ?int
    {
        return $this->actual_enrollment;
    }

    public function setActualEnrollment(?int $actual_enrollment): self
    {
        $this->actual_enrollment = $actual_enrollment;

        return $this;
    }

    public function getEmployment(): ?string
    {
        return $this->employment;
    }

    public function setEmployment(?string $employment): self
    {
        $this->employment = $employment;

        return $this;
    }

    public function getStartingSalary(): ?string
    {
        return $this->starting_salary;
    }

    public function setStartingSalary(?string $starting_salary): self
    {
        $this->starting_salary = $starting_salary;

        return $this;
    }

    public function getMaleFemaleRatio(): ?string
    {
        return $this->male_female_ratio;
    }

    public function setMaleFemaleRatio(?string $male_female_ratio): self
    {
        $this->male_female_ratio = $male_female_ratio;

        return $this;
    }

    public function getTotalAmountStudents(): ?int
    {
        return $this->total_amount_students;
    }

    public function setTotalAmountStudents(?int $total_amount_students): self
    {
        $this->total_amount_students = $total_amount_students;

        return $this;
    }

    public function getTotalAmountUndergraduate(): ?int
    {
        return $this->total_amount_undergraduate;
    }

    public function setTotalAmountUndergraduate(?int $total_amount_undergraduate): self
    {
        $this->total_amount_undergraduate = $total_amount_undergraduate;

        return $this;
    }

    public function getTotalAmountGraduate(): ?int
    {
        return $this->total_amount_graduate;
    }

    public function setTotalAmountGraduate(?int $total_amount_graduate): self
    {
        $this->total_amount_graduate = $total_amount_graduate;

        return $this;
    }

    public function getEstablishDate(): ?string
    {
        return $this->establish_date;
    }

    public function setEstablishDate(?string $establish_date): self
    {
        $this->establish_date = $establish_date;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getRegisterOfficeInfo(): ?string
    {
        return $this->register_office_info;
    }

    public function setRegisterOfficeInfo(?string $register_office_info): self
    {
        $this->register_office_info = $register_office_info;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPossibleName(): ?string
    {
        return $this->possible_name;
    }

    public function setPossibleName(?string $possible_name): self
    {
        $this->possible_name = $possible_name;

        return $this;
    }

    public function getAbbreviation(): ?string
    {
        return $this->abbreviation;
    }

    public function setAbbreviation(?string $abbreviation): self
    {
        $this->abbreviation = $abbreviation;

        return $this;
    }

    public function getIsShow(): ?string
    {
        return $this->is_show;
    }

    public function setIsShow(?string $is_show): self
    {
        $this->is_show = $is_show;

        return $this;
    }
}
