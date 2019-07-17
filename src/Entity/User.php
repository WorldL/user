<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false, hardDelete=true)
 */
class User implements UserInterface
{

    use TimestampableEntity;
    use SoftDeleteableEntity;

    const IMG_DOMAIN = 'cdn.xiaohailang.net';
    const DEFAULT_AVATAR = '/avatar/avatar.png';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $gender;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $birthday;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $avatar;

    /**
     * @ORM\Column(type="integer")
     */
    private $region_code;

    /**
     * @ORM\Column(type="string", length=11, unique=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $sign;

    /**
     * @ORM\Column(type="string", length=50, options={"default": "NO"})
     */
    private $is_kol;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Sets birthday.
     *
     * @param \DateTime|null $birthday
     *
     * @return $this
     */
    public function setBirthday($birthday = null)
    {
        if ($birthday instanceof \DateTime) {
            $this->birthday = $birthday;
        } else {
            $this->birthday = new \DateTime($birthday);
        }

        return $this;
    }

    /**
     * Returns birthday.
     *
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }


    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return empty($this->avatar) ? self::DEFAULT_AVATAR : $this->avatar;
    }

    public function setAvatar(string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getRegionCode(): ?int
    {
        return $this->region_code;
    }

    public function setRegionCode(int $region_code): self
    {
        $this->region_code = $region_code;

        return $this;
    }

    public function getPhone(): ?int
    {
        return $this->phone;
    }

    public function setPhone(int $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    // 获取图片url
    public function getAvatarUrl($rule = '', $scheme = 'http')
    {
        $rule = empty($rule) ? '' : '?x-oss-process=style/'.$rule;
        return $scheme.'://'.self::IMG_DOMAIN.'/'.$this->getAvatar().$rule;
    }

    public function getSign(): ?string
    {
        return $this->sign;
    }

    public function setSign(?string $sign): self
    {
        $this->sign = $sign;

        return $this;
    }

    public function getIsKol(): ?string
    {
        return $this->is_kol;
    }

    public function setIsKol(string $is_kol): self
    {
        $this->is_kol = $is_kol;

        return $this;
    }
}
