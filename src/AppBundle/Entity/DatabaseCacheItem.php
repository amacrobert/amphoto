<?php

namespace AppBundle\Entity;

class DatabaseCacheItem implements \Psr\Cache\CacheItemInterface {

    private $id;
    private $key;
    private $value;
    private $ttl;
    private $date_created;

    // PSR-6 methods

    public function getKey() {
        return $this->key;
    }

    public function get() {
        return $this->expired() ? null : json_decode($this->getValue());
    }

    public function isHit() {
        return (bool)$this->getId();
    }

    public function set($value) {
        return $this
            ->setValue(json_encode($value))
            ->setDateCreatedToNow()
        ;
    }

    public function expiresAt($expiration) {
        if (is_numeric($expiration)) {
            $expires_after = $expiration - time();
        }
        elseif ($expiration instanceof \DateInterval) {
            $expires_after = date_create('@0')->add($expiration)->getTimestamp();
        }
        else {
            throw new \Exception('Expiration must be numeric or a DateInterval');
        }

        return $this->setTTL($expires_after);
    }

    public function expiresAfter($time) {
        return $this->setTTL($time);
    }


    // Additional doctrine getters/setters

    public function getId() {
        return $this->id;
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

    // Determine if this cache item is expired
    private function expired() {
        $now = time();
        $expiration = $this->getDateCreated()->getTimestamp() + $this->getTTL();

        if ($now < $expiration) {
            return false;
        }

        return true;
    }
}
