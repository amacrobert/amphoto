<?php

namespace AppBundle\Entity;

class DatabaseCache {

    private $id;
    private $key;
    private $value;
    private $ttl;
    private $date_created;

    public function getId() {
        return $this->id;
    }

    public function getKey() {
        return $this->key;
    }

    public function setKey($key) {
        $this->key = $key;
        return $this;
    }

    public function getValue() {
        return $this->value;
    }

    public function setValue($value) {
        $this->value = $value;
        return $this;
    }

    public function getTTL() {
        return $this->ttl;
    }

    public function setTTL($ttl) {
        $this->ttl = $ttl;
        return $this;
    }

    public function getDateCreated() {
        return $this->date_created;
    }

    public function setDateCreated($date_created) {
        $this->date_created = $date_created;
        return $this;
    }

    public function setDateCreatedToNow() {
        return $this->setDateCreated(new \DateTime);
    }

}
