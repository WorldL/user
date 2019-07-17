<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DocumentRepository")
 */
class Document
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
    private $author_id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $education;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $major_en;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $major_cn;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $doc_type;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $doc_word_num;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $doc_tips;

    /**
     * @ORM\Column(type="text")
     */
    private $doc_content;

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

    public function getAuthorId(): ?int
    {
        return $this->author_id;
    }

    public function setAuthorId(int $author_id): self
    {
        $this->author_id = $author_id;

        return $this;
    }

    public function getEducation(): ?string
    {
        return $this->education;
    }

    public function setEducation(string $education): self
    {
        $this->education = $education;

        return $this;
    }

    public function getMajorEn(): ?string
    {
        return $this->major_en;
    }

    public function setMajorEn(string $major_en): self
    {
        $this->major_en = $major_en;

        return $this;
    }

    public function getMajorCn(): ?string
    {
        return $this->major_cn;
    }

    public function setMajorCn(string $major_cn): self
    {
        $this->major_cn = $major_cn;

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

    public function getDocType(): ?string
    {
        return $this->doc_type;
    }

    public function setDocType(string $doc_type): self
    {
        $this->doc_type = $doc_type;

        return $this;
    }

    public function getDocWordNum(): ?string
    {
        return $this->doc_word_num;
    }

    public function setDocWordNum(string $doc_word_num): self
    {
        $this->doc_word_num = $doc_word_num;

        return $this;
    }

    public function getDocTips(): ?string
    {
        return $this->doc_tips;
    }

    public function setDocTips(?string $doc_tips): self
    {
        $this->doc_tips = $doc_tips;

        return $this;
    }

    public function getDocContent(): ?string
    {
        return $this->doc_content;
    }

    public function setDocContent(string $doc_content): self
    {
        $this->doc_content = $doc_content;

        return $this;
    }
}
