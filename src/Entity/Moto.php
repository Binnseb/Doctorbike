<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MotoRepository")
 * @UniqueEntity(fields={"modele", "annee"},
 *     message="Cette moto existe déjà"
 * )
 */
class Moto
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Modele", inversedBy="motos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $modele;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 4,
     *      max = 4,
     *      exactMessage="La valeur pour l'année ne peut être inférieur ou supérieur à 4 chiffres"
     * )
     */
    private $annee;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="motos")
     */
    private $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->historiques = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getModele(): ?Modele
    {
        return $this->modele;
    }

    public function setModele(?Modele $modele): self
    {
        $this->modele = $modele;

        return $this;
    }

    public function getAnnee(): ?int
    {
        return $this->annee;
    }

    public function setAnnee(int $annee): self
    {
        $this->annee = $annee;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addMoto($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $user->removeMoto($this);
        }

        return $this;
    }

    public function checkIfMotoExists($moto)
    {
        foreach ($this->getModele() as $element)
        {
            if($element == $moto)
                return $element;
        }
        return null;
    }
}
