<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DocumentAuthorRepository")
 */
class DocumentAuthor
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
    private $author_id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $nickname;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $session;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $major;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name_cn;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name_en;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getSession(): ?string
    {
        return $this->session;
    }

    public function setSession(string $session): self
    {
        $this->session = $session;

        return $this;
    }

    public function getMajor(): ?string
    {
        return $this->major;
    }

    public function setMajor(string $major): self
    {
        $this->major = $major;

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
}
