<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="pad")
 */
class Pad
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Nom du pad
     * @var string $nom
     * @ORM\Column(name="nom", type="string", nullable=false)
     */
    private $nom;

    /**
     * @var \AppBundle\Entity\Ranch $ranch
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Ranch", inversedBy="pads")
     * @ORM\JoinColumn(name="ranch_id", referencedColumnName="id", nullable=false)
     */
    private $ranch;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Pad
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set ranch
     *
     * @param \AppBundle\Entity\Ranch $ranch
     *
     * @return Pad
     */
    public function setRanch(\AppBundle\Entity\Ranch $ranch)
    {
        $this->ranch = $ranch;

        return $this;
    }

    /**
     * Get ranch
     *
     * @return \AppBundle\Entity\Ranch
     */
    public function getRanch()
    {
        return $this->ranch;
    }
}
