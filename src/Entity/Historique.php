<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HistoriqueRepository")
 */
class Historique
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Moto", inversedBy="historiques")
     * @ORM\JoinColumn(nullable=false)
     */
    private $moto;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="historiques")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Scenario")
     * @ORM\JoinColumn(nullable=false)
     */
    private $scenario;

    public function getId()
    {
        return $this->id;
    }

    public function getMoto(): ?Moto
    {
        return $this->moto;
    }

    public function setMoto(?Moto $moto): self
    {
        $this->moto = $moto;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getScenario(): ?Scenario
    {
        return $this->scenario;
    }

    public function setScenario(?Scenario $scenario): self
    {
        $this->scenario = $scenario;

        return $this;
    }
}
