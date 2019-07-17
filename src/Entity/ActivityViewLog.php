<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ActivityViewLogRepository")
 */
class ActivityViewLog
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
    private $user_id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $visit_time;

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

    public function getVisitTime(): ?\DateTimeInterface
    {
        return $this->visit_time;
    }

    public function setVisitTime(\DateTimeInterface $visit_time): self
    {
        $this->visit_time = $visit_time;

        return $this;
    }
}
