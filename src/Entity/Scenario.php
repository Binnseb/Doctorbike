<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ScenarioRepository")
 */
class Scenario
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="scenarios", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="boolean")
     */
    private $estTermine;

    /**
     * @ @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\MotCle", inversedBy="scenarios", orphanRemoval=true, cascade={"persist"})
     */
    private $motCle;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\QuestionReponse", mappedBy="scenario", orphanRemoval=true)
     */
    private $questionReponses;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Historique", mappedBy="scenario", orphanRemoval=true)
     */
    private $historiques;

    public function __construct()
    {
        $this->motCle = new ArrayCollection();
        $this->questionReponses = new ArrayCollection();
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getEstTermine(): ?bool
    {
        return $this->estTermine;
    }

    public function setEstTermine(bool $estTermine): self
    {
        $this->estTermine = $estTermine;

        return $this;
    }

    public function getEstValide(): ?bool
    {
        return $this->estValide;
    }

    public function setEstValide(bool $estValide): self
    {
        $this->estValide = $estValide;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function GetIsAuthor(User $user = null)
    {
        return $user && $user->getId() === $this->getUser();
    }

    /**
     * @return Collection|MotCle[]
     */
    public function getMotCle(): Collection
    {
        return $this->motCle;
    }

    public function addMotCle(MotCle $motCle): self
    {
        if (!$this->motCle->contains($motCle))
        {
            $this->motCle[] = $motCle;
        }

        return $this;
    }

    public function removeMotCle(MotCle $motCle): self
    {
        if ($this->motCle->contains($motCle))
        {
            $this->motCle->removeElement($motCle);
        }

        return $this;
    }

    /**
     * @return Collection|QuestionReponse[]
     */
    public function getQuestionReponses(): Collection
    {
        return $this->questionReponses;
    }

    public function addQuestionReponse(QuestionReponse $questionReponse): self
    {
        if (!$this->questionReponses->contains($questionReponse))
        {
            $this->questionReponses[] = $questionReponse;
            $questionReponse->setScenario($this);
        }

        return $this;
    }

    public function removeQuestionReponse(QuestionReponse $questionReponse): self
    {
        if ($this->questionReponses->contains($questionReponse))
        {
            $this->questionReponses->removeElement($questionReponse);
            // set the owning side to null (unless already changed)
            if ($questionReponse->getScenario() === $this)
            {
                $questionReponse->setScenario(null);
            }
        }

        return $this;
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
        if (!$this->historiques->contains($historique))
        {
            $this->historiques[] = $historique;
            $historique->setScenario($this);
        }

        return $this;
    }

    public function removeHistorique(Historique $historique): self
    {
        if ($this->historiques->contains($historique))
        {
            $this->historiques->removeElement($historique);
            // set the owning side to null (unless already changed)
            if ($historique->getScenario() === $this)
            {
                $historique->setScenario(null);
            }
        }

        return $this;
    }

    public function satisfactionPourcentage()
    {
        $votePositifs = 0;

        foreach ($this->historiques as $historique)
        {
            if($historique->getVoteReponse())
            {
                $votePositifs ++;
            }
        }

        return $votePositifs / sizeof($this->historiques) * 100;
    }
}
