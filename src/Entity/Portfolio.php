<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Portfolio
 *
 * @ORM\Table(name="portfolio")
 * @ORM\Entity
 */
class Portfolio
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ordinal", type="integer", nullable=true)
     */
    private $ordinal;

    /**
     * @var bool
     *
     * @ORM\Column(name="listed", type="boolean")
     */
    private $listed = false;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="machine_name", type="string")
     */
    private $machine_name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string|null
     *
     * @ORM\Column(name="banner", type="string", nullable=true)
     */
    private $banner;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Endorsement")
     * @ORM\JoinTable(name="portfolio_endorsement",
     *   joinColumns={
     *     @ORM\JoinColumn(name="portfolio_id", referencedColumnName="id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="endorsement_id", referencedColumnName="id", onDelete="CASCADE")
     *   }
     * )
     */
    private $endorsements = [];

    // unmapped - used for active menu item
    private $active = false;

    public function __toString() {
        return $this->name ?: 'New Portfolio';
    }

    public function __construct() {
        $this->endoresements = new ArrayCollection;
    }

    public function isActive() {
        return (bool)$this->active;
    }

    public function setActive($active) {
        $this->active = $active;
        return $this;
    }

    public function getId() {
        return $this->id;
    }

    public function getOrdinal() {
        return $this->ordinal;
    }

    public function setOrdinal($ordinal) {
        $this->ordinal = $ordinal;
        return $this;
    }

    public function isListed() {
        return (bool)$this->listed;
    }

    public function setListed($listed) {
        $this->listed = $listed;
        return $this;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function getMachineName() {
        return $this->machine_name;
    }

    public function setMachineName($machine_name) {
        $this->machine_name = $machine_name;
        return $this;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    public function getBanner() {
        return $this->banner;
    }

    public function setBanner($banner) {
        $this->banner = $banner;
        return $this;
    }

    public function getEndorsements() {
        return $this->endorsements;
    }

    public function addEndorsement($endorsement) {
        $this->endorsements->add($endorsements);
        return $this;
    }

    public function removeEndorsement($endorsement) {
        $this->endoresements->removeElement($endorsement);
    }
}
