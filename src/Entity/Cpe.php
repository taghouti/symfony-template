<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cpe
 *
 * @ORM\Table(name="cpe")
 * @ORM\Entity
 */
class Cpe
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="version", type="string", length=255, nullable=false)
     */
    private $version;

    /**
     * @var string
     *
     * @ORM\Column(name="cpe", type="string", length=255, nullable=false)
     */
    private $cpe;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(string $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getCpe(): ?string
    {
        return $this->cpe;
    }

    public function setCpe(string $cpe): self
    {
        $this->cpe = $cpe;

        return $this;
    }


}
