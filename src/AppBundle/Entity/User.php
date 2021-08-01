<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Ranchs dans lesquels l'utilisateur est inscrit
     * @var \AppBundle\Entity\Ranch $ranchs
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Ranch", mappedBy="users")
     */
    private $ranchs;

    /**
     * Id du ranch actif de l'utilisateur
     * @var int $ranchActif
     * @ORM\Column(name="ranch_actif", type="integer", nullable=true)
     */
    private $ranchActif;

    /**
     * Classes ou groupes dans lequel est l'élève
     * @var \AppBundle\Entity\Classe $classes
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Classe", mappedBy="users")
     */
    private $classes;


    /**
     * Set ranchActif
     *
     * @param integer $ranchActif
     *
     * @return User
     */
    public function setRanchActif($ranchActif)
    {
        $this->ranchActif = $ranchActif;

        return $this;
    }

    /**
     * Get ranchActif
     *
     * @return integer
     */
    public function getRanchActif()
    {
        return $this->ranchActif;
    }

    /**
     * Add ranch
     *
     * @param \AppBundle\Entity\Ranch $ranch
     *
     * @return User
     */
    public function addRanch(\AppBundle\Entity\Ranch $ranch)
    {
        $this->ranchs[] = $ranch;

        return $this;
    }

    /**
     * Remove ranch
     *
     * @param \AppBundle\Entity\Ranch $ranch
     */
    public function removeRanch(\AppBundle\Entity\Ranch $ranch)
    {
        $this->ranchs->removeElement($ranch);
    }

    /**
     * Get ranchs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRanchs()
    {
        return $this->ranchs;
    }

    /**
     * Add class
     *
     * @param \AppBundle\Entity\Classe $class
     *
     * @return User
     */
    public function addClass(\AppBundle\Entity\Classe $class)
    {
        $this->classes[] = $class;

        return $this;
    }

    /**
     * Remove class
     *
     * @param \AppBundle\Entity\Classe $class
     */
    public function removeClass(\AppBundle\Entity\Classe $class)
    {
        $this->classes->removeElement($class);
    }

    /**
     * Get classes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getClasses()
    {
        return $this->classes;
    }
}
