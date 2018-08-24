<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuestionReponseRepository")
 */
class QuestionReponse
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Image", cascade={"persist", "remove"})
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Scenario", inversedBy="questionReponses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $scenario;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $question;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $aide;

    /**
     * @ORM\Column(type="boolean")
     */
    private $estSolution;

    /**
     * @ORM\Column(type="boolean")
     */
    private $estPremiereQuestion;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\QuestionReponse", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $idQuestionSiOui;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\QuestionReponse", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $idQuestionSiNon;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\QuestionReponse", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $idQuestionSiJenesaispas;

    public function getId()
    {
        return $this->id;
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

    public function getScenario(): ?Scenario
    {
        return $this->scenario;
    }

    public function setScenario(?Scenario $scenario): self
    {
        $this->scenario = $scenario;

        return $this;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getAide(): ?string
    {
        return $this->aide;
    }

    public function setAide(?string $aide): self
    {
        $this->aide = $aide;

        return $this;
    }

    public function getEstSolution(): ?bool
    {
        return $this->estSolution;
    }

    public function setEstSolution(bool $estSolution): self
    {
        $this->estSolution = $estSolution;

        return $this;
    }

    public function getEstPremiereQuestion(): ?bool
    {
        return $this->estPremiereQuestion;
    }

    public function setEstPremiereQuestion(bool $estPremiereQuestion): self
    {
        $this->estPremiereQuestion = $estPremiereQuestion;

        return $this;
    }

    public function getIdQuestionSiOui(): ?self
    {
        return $this->idQuestionSiOui;
    }

    public function setIdQuestionSiOui(self $idQuestionSiOui): self
    {
        $this->idQuestionSiOui = $idQuestionSiOui;

        return $this;
    }

    public function getIdQuestionSiNon(): ?self
    {
        return $this->idQuestionSiNon;
    }

    public function setIdQuestionSiNon(self $idQuestionSiNon): self
    {
        $this->idQuestionSiNon = $idQuestionSiNon;

        return $this;
    }

    public function getIdQuestionSiJenesaispas(): ?self
    {
        return $this->idQuestionSiJenesaispas;
    }

    public function setIdQuestionSiJenesaispas(?self $idQuestionSiJenesaispas): self
    {
        $this->idQuestionSiJenesaispas = $idQuestionSiJenesaispas;

        return $this;
    }


}
