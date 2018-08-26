<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity("username", message = "Ce pseudo n'est pas disponible")
 * @UniqueEntity("email", message = "Cette adresse mail n'est pas disponible")
 */
class User implements UserInterface, \Serializable
{

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Moto", cascade={"persist"}, inversedBy="users")
     * @ORM\JoinTable(name="user_moto")
     */
    private $motos;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\Length(min="5", minMessage="Votre pseudo doit être composé d'au moins 5 caractères")
     * @Assert\Regex(pattern="/^[a-zA-Z0-9]+$/i", message = "Votre pseudo ne peut pas contenir de caractères spéciaux")
     */
    private $username;

    /**
     * @ORM\Column(name = "email", type="string", length=60, unique = true)
     * @Assert\Email(message = "Veuillez renseigner une adresse mail avec un format correct (ex : Doctorbike@gmail.com)")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\Length(min="8", minMessage="Votre mot de passe  doit être composé d'au moins 8 caractères")
     * @Assert\Length(max="60", maxMessage="Votre mot de passe  ne peut excéder les 60 caractères")
     * @Assert\Regex(pattern="/^[a-zA-Z0-9]+$/i", match = false,  message = "Votre mot de passe doit contenir au moins une majuscule, un nombre et un caractère spécial")
     */
    private $password;

    /**
     * @Assert\EqualTo(propertyPath="password", message="Vos mots de passe ne sont pas identiques")
     */
    private $confirm_password;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\LessThan("Today UTC", message="La date ne peut être supérieur à aujourd'hui")
     */
    private $dateNaissance;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     */
    public $confirmationToken;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $confirmedAt;

    /**
     * @ORM\Column(type="string", length=60, nullable=true)
     */
    private $resetToken;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $resetAt;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Image", cascade={"persist", "remove"})
     */
    private $image;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Scenario", mappedBy="user", orphanRemoval=true)
     */
    private $scenarios;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Historique", mappedBy="user", orphanRemoval=true)
     */
    private $historiques;


    public function __construct()
    {
        $this->scenarios = new ArrayCollection();
        $this->historiques = new ArrayCollection();
    }

    public function getId()
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

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

    public function getConfirmPassword(): ?string
    {
        return $this->confirm_password;
    }

    public function setConfirmPassword(string $confirm_password): self
    {
        $this->confirm_password = $confirm_password;

        return $this;
    }

    public function getDateDeNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateDeNaissance(\DateTimeInterface $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }


    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): self
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function getConfirmedAt(): ?\DateTimeInterface
    {
        return $this->confirmedAt;
    }

    public function setConfirmedAt(?\DateTimeInterface $confirmedAt): self
    {
        $this->confirmedAt = $confirmedAt;

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    public function getResetAt(): ?\DateTimeInterface
    {
        return $this->resetAt;
    }

    public function setResetAt(?\DateTimeInterface $resetAt): self
    {
        $this->resetAt = $resetAt;

        return $this;
    }

    public function eraseCredentials()
    {
    }

    public function getSalt()
    {
        return null;
    }

    public function getRoles()
    {
        $roles = $this->roles;

        return $roles;
    }

    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

    /**
     * @return Collection|Moto[]
     */
    public function getMotos(): Collection
    {
        return $this->motos;
    }

    public function addMoto(Moto $moto): self
    {
        if (!$this->motos->contains($moto)) {
            $this->motos[] = $moto;
        }

        return $this;
    }

    public function removeMoto(Moto $moto): self
    {
        if ($this->motos->contains($moto)) {
            $this->motos->removeElement($moto);
        }

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): self
    {
        $this->image = $image;

        return $this;
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized, array('allowed_classes' => false));
    }

    /**
     * @return Collection|Scenario[]
     */
    public function getScenarios(): Collection
    {
        return $this->scenarios;
    }

    public function addScenario(Scenario $scenario): self
    {
        if (!$this->scenarios->contains($scenario)) {
            $this->scenarios[] = $scenario;
            $scenario->setUser($this);
        }

        return $this;
    }

    public function removeScenario(Scenario $scenario): self
    {
        if ($this->scenarios->contains($scenario)) {
            $this->scenarios->removeElement($scenario);
            // set the owning side to null (unless already changed)
            if ($scenario->getUser() === $this) {
                $scenario->setUser(null);
            }
        }

        return $this;
    }

    public function checkIfUserMotoExists($moto)
    {
        $motoIfExist = null;

        foreach ($this->getMotos() as $element) {
            if ($element == $moto) {
                $motoIfExist = $element;
                break;
            }

        }

        return $motoIfExist;
    }

    /**
     * @return Collection|Historique[]
     */
    public function getHistoriques(): Collection
    {
        return $this->historiques;
    }

    public function addHistorique(Historique $historique): self
    {
        if (!$this->historiques->contains($historique)) {
            $this->historiques[] = $historique;
            $historique->setUser($this);
        }

        return $this;
    }

    public function removeHistorique(Historique $historique): self
    {
        if ($this->historiques->contains($historique)) {
            $this->historiques->removeElement($historique);
            // set the owning side to null (unless already changed)
            if ($historique->getUser() === $this) {
                $historique->setUser(null);
            }
        }

        return $this;
    }
}