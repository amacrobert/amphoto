<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Portfolio
{
    private $id;
    private $ordinal;
    private $listed = false;
    private $name;
    private $machine_name;
    private $description;
    private $banner;
    private $endorsements;

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
