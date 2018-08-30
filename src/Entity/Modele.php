<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ModeleRepository")
 *  * @UniqueEntity(
 *     fields={"nom", "marque", "cylindree"},
 *     message="Ce modèle existe déjà en DB")
 */
class Modele
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
     *     maxMessage="Le nom de la marque ne peut pas être supérieur à 60 caractères"
     * )
     */
    private $nom;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Marque", inversedBy="modeles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $marque;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Cylindree", inversedBy="modeles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $cylindree;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Moto", mappedBy="modele", orphanRemoval=true)
     */
    private $motos;

    public function __construct()
    {
        $this->motos = new ArrayCollection();
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

    public function getMarque(): ?Marque
    {
        return $this->marque;
    }

    public function setMarque(?Marque $marque): self
    {
        $this->marque = $marque;

        return $this;
    }

    public function getCylindree(): ?Cylindree
    {
        return $this->cylindree;
    }

    public function setCylindree(?Cylindree $cylindree): self
    {
        $this->cylindree = $cylindree;

        return $this;
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
            $moto->setModele($this);
        }

        return $this;
    }

    public function removeMoto(Moto $moto): self
    {
        if ($this->motos->contains($moto)) {
            $this->motos->removeElement($moto);
            // set the owning side to null (unless already changed)
            if ($moto->getModele() === $this) {
                $moto->setModele(null);
            }
        }

        return $this;
    }

    public function __ToString()
    {
        return $this->nom;
    }

}
