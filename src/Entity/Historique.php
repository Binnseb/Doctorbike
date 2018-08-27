<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HistoriqueRepository")
 * @UniqueEntity(fields={"user", "scenario", "solution", "voteReponse"},
 *     message="Vous avez déjà voté pour cette solution"
 * )
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
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="historiques")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Scenario", inversedBy="historiques")
     */
    private $scenario;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\QuestionReponse")
     */
    private $solution;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Moto")
     */
    private $moto;

    /**
     * @ORM\Column(type="boolean")
     */
    private $voteReponse;

    public function getId()
    {
        return $this->id;
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

    public function getSolution(): ?QuestionReponse
    {
        return $this->solution;
    }

    public function setSolution(?QuestionReponse $solution): self
    {
        $this->solution = $solution;

        return $this;
    }

    public function getVoteReponse(): ?bool
    {
        return $this->voteReponse;
    }

    public function setVoteReponse(bool $voteReponse): self
    {
        $this->voteReponse = $voteReponse;

        return $this;
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
}
