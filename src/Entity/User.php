<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity("email")
 * @UniqueEntity("username" )
 */
class User implements UserInterface,\Serializable
{

    const CIV = ['M.','Mme','Mlle'];
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\NotBlank(
     *     message = "Please enter your username."
     * )
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     * @Assert\NotBlank(
     *     message = "Please enter your password."
     * )
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\NotBlank(
     *     message = "Please enter your firstname."
     * )
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\NotBlank(
     *     message = "Please enter your lastname."
     * )
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=11, nullable=true)
     */
    private $tel;

    /**
     * @ORM\Column(type="string", length=255, nullable=false, unique=true, unique=true)
     * @Assert\Regex(
     *     pattern = "/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,3})$/",
     *     message     = "The email '{{ value }}' is not a valid email."
     *     )
     * @Assert\NotBlank(
     *     message = "Please enter your email."
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="integer", nullable=false)
     * @Assert\NotBlank(
     *     message = "Please enter your civility."
     * )
     */
    private $civility;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $newsletter;

    /**
     * @ORM\OneToMany(targetEntity=Property::class, mappedBy="author")
     */
    private $author;

    public function __construct()
    {
        $this->author = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(?string $tel): self
    {

        $this->tel = '0'.substr($tel,-9,9);

        return $this;
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

    public function getCivility(): ?int
    {
        return $this->civility;
    }

    public function setCivility(int $civility): self
    {
        $this->civility = $civility;

        return $this;
    }

    public function getNewsletter(): ?bool
    {
        return $this->newsletter;
    }

    public function setNewsletter(bool $newsletter): self
    {

        $this->newsletter = $newsletter;

        return $this;

    }

    /**
     * @return string[]
     */
    public function getRoles()
    {
        return ['ROLE_ADMIN'];
    }

    /**
     * @return string|null
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @return mixed
     */
    public function eraseCredentials()
    {
        return null;
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
            $this->password,
            $this->civility,
            $this->email,
            $this->firstname,
            $this->lastname,
            $this->tel,
            $this->newsletter
        ]);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->password,
            $this->civility,
            $this->email,
            $this->firstname,
            $this->lastname,
            $this->tel,
            $this->newsletter
            ) = unserialize($serialized, ['allowed_classes' => false]);
    }

    /**
     * @return Collection|Property[]
     */
    public function getAuthor(): Collection
    {
        return $this->author;
    }

    public function addAuthor(Property $author): self
    {
        if (!$this->author->contains($author)) {
            $this->author[] = $author;
            $author->setAuthor($this);
        }

        return $this;
    }

    public function removeAuthor(Property $author): self
    {
        if ($this->author->contains($author)) {
            $this->author->removeElement($author);
            // set the owning side to null (unless already changed)
            if ($author->getAuthor() === $this) {
                $author->setAuthor(null);
            }
        }

        return $this;
    }
}
