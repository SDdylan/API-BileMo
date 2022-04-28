<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;
use OpenApi\Annotations as OA;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields="email", message= "Email already taken.")
 * @OA\Schema()
 * @Hateoas\Relation("self", href = "expr('/api/client/user/' ~ object.getId())", exclusion = @Hateoas\Exclusion(groups={"client:list"}))
 * @Hateoas\Relation("list", href = "expr('/api/client/users/1", exclusion = @Hateoas\Exclusion(groups={"user:detail"}))
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"client:list","user:detail"})
     * @OA\Property(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"client:list","user:detail"})
     * @Assert\NotBlank
     * @Assert\Email()
     * @OA\Property(type="string")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"client:list","user:detail"})
     * @Assert\NotBlank
     * @OA\Property(type="string")
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"client:list","user:detail"})
     * @Assert\NotBlank
     * @OA\Property(type="string")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"user:detail"})
     * @Assert\Length(min="8", minMessage="Password must be at least 8 characters.")
     * @Assert\NotBlank
     * @OA\Property(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="json")
     * @Serializer\Groups({"user:detail"})
     */
    private $roles = [];

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function encodePassword(string $clearPassword): self
    {
        $encodedPassword = password_hash($clearPassword, PASSWORD_DEFAULT);
        return $this->setPassword($encodedPassword);
    }
}
