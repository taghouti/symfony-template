<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Cve
 *
 * @ORM\Table(name="cve")
 * @ORM\Entity
 */
class Cve
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cve", type="text", length=65535, nullable=true)
     */
    private $cve;

    /**
     * @var string|null
     *
     * @ORM\Column(name="link", type="text", length=65535, nullable=true)
     */
    private $link;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cve_description", type="text", length=65535, nullable=true)
     */
    private $cveDescription;

    /**
     * @var string|null
     *
     * @ORM\Column(name="attack_vector", type="text", length=65535, nullable=true)
     */
    private $attackVector;

    /**
     * @var string|null
     *
     * @ORM\Column(name="base_score", type="text", length=65535, nullable=true)
     */
    private $baseScore;

    /**
     * @var string|null
     *
     * @ORM\Column(name="matching", type="text", length=65535, nullable=true)
     */
    private $matching;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cots", type="text", length=65535, nullable=true)
     */
    private $cots;

    /**
     * @var string|null
     *
     * @ORM\Column(name="analysis_status", type="text", length=65535, nullable=true)
     */
    private $analysisStatus;

    /**
     * @var string|null
     *
     * @ORM\Column(name="analysis_date", type="text", length=65535, nullable=true)
     */
    private $analysisDate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="applicability_status", type="text", length=65535, nullable=true)
     */
    private $applicabilityStatus;

    /**
     * @var string|null
     *
     * @ORM\Column(name="applicability_rationale", type="text", length=65535, nullable=true)
     */
    private $applicabilityRationale;

    /**
     * @var string|null
     *
     * @ORM\Column(name="consequence", type="text", length=65535, nullable=true)
     */
    private $consequence;

    /**
     * @var string|null
     *
     * @ORM\Column(name="operational_impact_level", type="text", length=65535, nullable=true)
     */
    private $operationalImpactLevel;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cve_condition", type="text", length=65535, nullable=true)
     */
    private $cveCondition;

    /**
     * @var string|null
     *
     * @ORM\Column(name="exploit_likelihood", type="text", length=65535, nullable=true)
     */
    private $exploitLikelihood;

    /**
     * @var string|null
     *
     * @ORM\Column(name="exploit_likelihood_rationale", type="text", length=65535, nullable=true)
     */
    private $exploitLikelihoodRationale;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(name="updated", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updated;

    public function getCreated()
    {
        return $this->created;
    }

    public function getUpdated()
    {
        return $this->updated;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCve(): ?string
    {
        return $this->cve;
    }

    public function setCve(?string $cve): self
    {
        $this->cve = $cve;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getCveDescription(): ?string
    {
        return $this->cveDescription;
    }

    public function setCveDescription(?string $cveDescription): self
    {
        $this->cveDescription = $cveDescription;

        return $this;
    }

    public function getAttackVector(): ?string
    {
        return $this->attackVector;
    }

    public function setAttackVector(?string $attackVector): self
    {
        $this->attackVector = $attackVector;

        return $this;
    }

    public function getBaseScore(): ?string
    {
        return $this->baseScore;
    }

    public function setBaseScore(?string $baseScore): self
    {
        $this->baseScore = $baseScore;

        return $this;
    }

    public function getMatching(): ?string
    {
        return $this->matching;
    }

    public function setMatching(?string $matching): self
    {
        $this->matching = $matching;

        return $this;
    }

    public function getCots(): ?string
    {
        return $this->cots;
    }

    public function setCots(?string $cots): self
    {
        $this->cots = $cots;

        return $this;
    }

    public function getAnalysisStatus(): ?string
    {
        return $this->analysisStatus;
    }

    public function setAnalysisStatus(?string $analysisStatus): self
    {
        $this->analysisStatus = $analysisStatus;

        return $this;
    }

    public function getAnalysisDate(): ?string
    {
        return $this->analysisDate;
    }

    public function setAnalysisDate(?string $analysisDate): self
    {
        $this->analysisDate = $analysisDate;

        return $this;
    }

    public function getApplicabilityStatus(): ?string
    {
        return $this->applicabilityStatus;
    }

    public function setApplicabilityStatus(?string $applicabilityStatus): self
    {
        $this->applicabilityStatus = $applicabilityStatus;

        return $this;
    }

    public function getApplicabilityRationale(): ?string
    {
        return $this->applicabilityRationale;
    }

    public function setApplicabilityRationale(?string $applicabilityRationale): self
    {
        $this->applicabilityRationale = $applicabilityRationale;

        return $this;
    }

    public function getConsequence(): ?string
    {
        return $this->consequence;
    }

    public function setConsequence(?string $consequence): self
    {
        $this->consequence = $consequence;

        return $this;
    }

    public function getOperationalImpactLevel(): ?string
    {
        return $this->operationalImpactLevel;
    }

    public function setOperationalImpactLevel(?string $operationalImpactLevel): self
    {
        $this->operationalImpactLevel = $operationalImpactLevel;

        return $this;
    }

    public function getCveCondition(): ?string
    {
        return $this->cveCondition;
    }

    public function setCveCondition(?string $cveCondition): self
    {
        $this->cveCondition = $cveCondition;

        return $this;
    }

    public function getExploitLikelihood(): ?string
    {
        return $this->exploitLikelihood;
    }

    public function setExploitLikelihood(?string $exploitLikelihood): self
    {
        $this->exploitLikelihood = $exploitLikelihood;

        return $this;
    }

    public function getExploitLikelihoodRationale(): ?string
    {
        return $this->exploitLikelihoodRationale;
    }

    public function setExploitLikelihoodRationale(?string $exploitLikelihoodRationale): self
    {
        $this->exploitLikelihoodRationale = $exploitLikelihoodRationale;

        return $this;
    }


}
