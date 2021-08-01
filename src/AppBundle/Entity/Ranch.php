<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="ranch")
 */
class Ranch
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \Doctrine\Common\Collections\Collection $users
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", inversedBy="ranchs")
     * @ORM\JoinTable(name="ranchs_users")
     */
    private $users;

    /**
     * Nom du ranch
     * @var string $nom
     * @ORM\Column(name="nom", type="string", nullable=false)
     */
    private $nom;

    /**
     * @var \Doctrine\Common\Collections\Collection $pads
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Pad", mappedBy="ranch")
     */
    private $pads;

    /**
     * @var \Doctrine\Common\Collections\Collection $calcs
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Calc", mappedBy="ranch")
     */
    private $calcs;

    /**
     * @var \Doctrine\Common\Collections\Collection $scrumblrs
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Scrumblr", mappedBy="ranch")
     */
    private $scrumblrs;



    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * @return Ranch
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
     * Add user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Ranch
     */
    public function addUser(\AppBundle\Entity\User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \AppBundle\Entity\User $user
     */
    public function removeUser(\AppBundle\Entity\User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add pad
     *
     * @param \AppBundle\Entity\Pad $pad
     *
     * @return Ranch
     */
    public function addPad(\AppBundle\Entity\Pad $pad)
    {
        $this->pads[] = $pad;

        return $this;
    }

    /**
     * Remove pad
     *
     * @param \AppBundle\Entity\Pad $pad
     */
    public function removePad(\AppBundle\Entity\Pad $pad)
    {
        $this->pads->removeElement($pad);
    }

    /**
     * Get pads
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPads()
    {
        return $this->pads;
    }

    /**
     * Add calc
     *
     * @param \AppBundle\Entity\Calc $calc
     *
     * @return Ranch
     */
    public function addCalc(\AppBundle\Entity\Calc $calc)
    {
        $this->calcs[] = $calc;

        return $this;
    }

    /**
     * Remove calc
     *
     * @param \AppBundle\Entity\Calc $calc
     */
    public function removeCalc(\AppBundle\Entity\Calc $calc)
    {
        $this->calcs->removeElement($calc);
    }

    /**
     * Get calcs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCalcs()
    {
        return $this->calcs;
    }

    /**
     * Add scrumblr
     *
     * @param \AppBundle\Entity\Scrumblr $scrumblr
     *
     * @return Ranch
     */
    public function addScrumblr(\AppBundle\Entity\Scrumblr $scrumblr)
    {
        $this->scrumblrs[] = $scrumblr;

        return $this;
    }

    /**
     * Remove scrumblr
     *
     * @param \AppBundle\Entity\Scrumblr $scrumblr
     */
    public function removeScrumblr(\AppBundle\Entity\Scrumblr $scrumblr)
    {
        $this->scrumblrs->removeElement($scrumblr);
    }

    /**
     * Get scrumblrs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getScrumblrs()
    {
        return $this->scrumblrs;
    }
}
