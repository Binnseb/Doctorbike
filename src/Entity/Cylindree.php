<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CylindreeRepository")
 * @UniqueEntity("valeur", message = "Cette cylindrée existe déjà")
 */
class Cylindree
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Length(
     *     max="4",
     *     min="2",
     *     maxMessage="La cylindrée ne peut pas être supérieur à 4 chiffres",
     *     minMessage="La cylindrée ne peut être inférieur à 2 chiffres"
     * )
     */
    private $valeur;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Modele", mappedBy="cylindree", orphanRemoval=true)
     */
    private $modeles;

    public function __construct()
    {
        $this->modeles = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getValeur(): ?int
    {
        return $this->valeur;
    }

    public function setValeur(int $valeur): self
    {
        $this->valeur = $valeur;

        return $this;
    }

    /**
     * @return Collection|Modele[]
     */
    public function getModeles(): Collection
    {
        return $this->modeles;
    }

    public function addModele(Modele $modele): self
    {
        if (!$this->modeles->contains($modele)) {
            $this->modeles[] = $modele;
            $modele->setCylindree($this);
        }

        return $this;
    }

    public function removeModele(Modele $modele): self
    {
        if ($this->modeles->contains($modele)) {
            $this->modeles->removeElement($modele);
            // set the owning side to null (unless already changed)
            if ($modele->getCylindree() === $this) {
                $modele->setCylindree(null);
            }
        }

        return $this;
    }
}
