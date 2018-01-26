<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class User {

    private $id;
    private $email;
    private $date_created;
    private $freebies;

    public function __construct() {
        $this->freebies = new ArrayCollection;
    }

    public function getId() {
        return $this->id;
    }

    public function getFreebies() {
        return $this->freebies;
    }

    public function addFreebie($freebie) {
        $this->freebies->add($freebie);
        return $this;
    }

    public function removeFreebie($freebie) {
        $this->removeElement($freebie);
        return $this;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
        return $this->email;
    }

    public function getDateCreated() {
        return $this->date_created;
    }

    public function setDateCreated($date) {
        $this->date_created = $date;
        return $this;
    }

    public function setDateCreatedToNow() {
        return $this->setDateCreated(new \DateTime);
    }
}
