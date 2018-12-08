<?php

namespace App\AppMain\Entity\User;

use App\AppMain\Entity\Traits\UUIDableTrait;
use App\AppMain\Entity\UuidInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface as UserSecurityInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_base", schema="x_main")
 */
class User implements UserSecurityInterface, \Serializable, UuidInterface, UserInterface
{
    use UUIDableTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(length=255, unique=true)
     * @Assert\NotBlank()
     */
    protected $username;

    /**
     * @ORM\Column(type="string", length=250, nullable=false)
     */
    protected $roles;

    /**
     * @ORM\Column(type="date_immutable", nullable=true)
     */
    protected $lastLogin;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
     * @Assert\Email()
     */
    protected $email;

    /**
     * @Assert\Length(max=4096)
     */
    protected $plainPassword;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name = '';

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    protected $isActive = true;

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

    public function getSalt(): void
    {
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getRoles()
    {
        return \unserialize($this->roles);
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

    public function serialize()
    {
        return \igbinary_serialize([
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ]);
    }

    public function unserialize($serialized): void
    {
        [
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
        ]
            = \igbinary_unserialize($serialized);
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

    public function getLastLogin(): ?\DateTimeImmutable
    {
        return $this->lastLogin;
    }

    public function setLastLogin(\DateTimeImmutable $lastLogin): void
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
}
