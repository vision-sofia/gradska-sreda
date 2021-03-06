<?php

namespace App\AppMain\Entity\User;

use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface as UserSecurityInterface;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_base", schema="x_main")
 * @UniqueEntity("username", groups={"register", "default"})
 */
class User implements UserSecurityInterface, UuidInterface, UserInterface
{
    use UUIDableTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected int $id;

    /**
     * @ORM\Column(length=255, unique=true)
     * @Assert\NotBlank(groups={"profile", "register"})
     */
    protected ?string $username = null;

    /**
     * @ORM\Column(type="string", length=250, nullable=false)
     */
    protected $roles;

    /**
     * @ORM\Column(name="last_login", type="datetime", nullable=true)
     */
    protected ?\DateTimeInterface $lastLogin = null;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
     * @Assert\NotBlank(groups={"profile"})
     * @Assert\Email(groups={"profile"})
     */
    protected $email;

    /**
     * @Assert\Length(max=4096)
     * @Assert\NotBlank(groups={"plain_password", "register"})
     */
    protected $plainPassword;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected ?string $password = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected ?string $name = '';

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    protected bool $isActive = true;

    /**
     * @SecurityAssert\UserPassword(groups={"current_password"})
     */
    protected $currentPassword;

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getRoles()
    {
        return unserialize($this->roles);
    }

    public function addRole(string $role): void
    {
        //  $roles = $this->roles;

        $roles = unserialize(null === $this->roles ? serialize(['ROLE_USER']) : $this->roles, []);

        $roles[] = $role;
        $roles = array_unique($roles);
        $this->roles = serialize($roles);
    }

    public function eraseCredentials(): void
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->lastLogin;
    }

    public function setLastLogin(\DateTimeInterface $lastLogin): void
    {
        $this->lastLogin = $lastLogin;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $profileName): void
    {
        $this->name = $profileName;
    }

    public function getCurrentPassword(): ?string
    {
        return $this->currentPassword;
    }

    public function setCurrentPassword(string $password): void
    {
        $this->currentPassword = $password;
    }
}
