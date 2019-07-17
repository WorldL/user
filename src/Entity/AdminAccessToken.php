<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AdminAccessTokenRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 */
class AdminAccessToken
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
    private $admin_id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $token;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $refresh_token;

    /**
     * @ORM\Column(type="datetime")
     */
    private $expires;

    /**
     * @ORM\Column(type="json_array")
     */
    private $scope;

    /**
     * @ORM\Column(type="json_array")
     */
    private $client;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdminId(): ?int
    {
        return $this->admin_id;
    }

    public function setAdminId(int $admin_id): self
    {
        $this->admin_id = $admin_id;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refresh_token;
    }

    public function setRefreshToken(string $refresh_token): self
    {
        $this->refresh_token = $refresh_token;

        return $this;
    }

    public function getExpires(): ?\DateTimeInterface
    {
        return $this->expires;
    }

    public function setExpires(\DateTimeInterface $expires): self
    {
        $this->expires = $expires;

        return $this;
    }

    public function getScope()
    {
        return $this->scope;
    }

    public function setScope($scope): self
    {
        $this->scope = $scope;

        return $this;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function setClient($client): self
    {
        $this->client = $client;

        return $this;
    }
}
