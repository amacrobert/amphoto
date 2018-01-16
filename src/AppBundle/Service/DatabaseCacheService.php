<?php

namespace AppBundle\Service;

use AppBundle\Entity\DatabaseCache;

class DatabaseCacheService {

    private $em;

    public function __construct($em) {
        $this->em = $em;
    }

    public function exists($key) {
        if (!$cache = $this->em->getRepository(DatabaseCache::class)->findOneBy(['key' => $key])) {
            return false;
        }

        return !$this->expired($cache);
    }

    public function store($key, $value, $ttl = 84600) {
        if (!$cache = $this->em->getRepository(DatabaseCache::class)->findOneBy(['key' => $key])) {
            $cache = new DatabaseCache;
            $this->em->persist($cache);
        }

        $cache
            ->setKey($key)
            ->setValue(json_encode($value))
            ->setTTL($ttl)
            ->setDateCreatedToNow()
        ;

        $this->em->flush();
    }

    public function fetch($key) {


        $cache = $this->em->getRepository(DatabaseCache::class)->findOneBy(['key' => $key]);

        if (!$cache || $this->expired($cache)) {
            return null;
        }

        return json_decode($cache->getValue());
    }

    private function expired(DatabaseCache $cache) {
        $now = time();
        $expiration = $cache->getDateCreated()->getTimestamp() + $cache->getTTL();

        if ($now < $expiration) {
            return false;
        }

        return true;
    }

}
