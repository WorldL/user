<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MsgNotifyRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 */
class MsgNotify
{

    use TimestampableEntity;
    use SoftDeleteableEntity;

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
     * @ORM\Column(type="integer")
     */
    private $info_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $notifier_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $review_id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $read_status;

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

    public function getInfoId(): ?int
    {
        return $this->info_id;
    }

    public function setInfoId(int $info_id): self
    {
        $this->info_id = $info_id;

        return $this;
    }

    public function getNotifierId(): ?int
    {
        return $this->notifier_id;
    }

    public function setNotifierId(int $notifier_id): self
    {
        $this->notifier_id = $notifier_id;

        return $this;
    }

    public function getReviewId(): ?int
    {
        return $this->review_id;
    }

    public function setReviewId(int $review_id): self
    {
        $this->review_id = $review_id;

        return $this;
    }

    public function getReadStatus(): ?string
    {
        return $this->read_status;
    }

    public function setReadStatus(string $read_status): self
    {
        $this->read_status = $read_status;

        return $this;
    }
}
