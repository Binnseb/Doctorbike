<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MarqueRepository")
 * @UniqueEntity("nom", message = "Cette marque existe déjà")
 */
class Marque
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\Length(
     *     max="60",
     *     maxMessage="Le nom de la marque ne peut pas être supérieur à 60 caractères",
     *     min="3",
     *     minMessage="Le nom de la marque ne peut pas être inférieur à 3 caractères"
     * )
     */
    private $nom;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Modele", mappedBy="marque", orphanRemoval=true)
     */
    private $modeles;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Image", cascade={"persist", "remove"})
     */
    private $image;


    public function __construct()
    {
        $this->modeles = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->image;
    }

    public function setLogo(?string $image): self
    {
        $this->image = $image;

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
            $modele->setMarque($this);
        }

        return $this;
    }

    public function removeModele(Modele $modele): self
    {
        if ($this->modeles->contains($modele)) {
            $this->modeles->removeElement($modele);
            // set the owning side to null (unless already changed)
            if ($modele->getMarque() === $this) {
                $modele->setMarque(null);
            }
        }

        return $this;
    }

    public function __ToString()
    {
        return $this->nom;
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
}
