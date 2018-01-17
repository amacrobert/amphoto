<?php

namespace AppBundle\Service;

use AppBundle\Entity\DatabaseCacheItem;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\CacheItemInterface;

class DatabaseCacheService implements CacheItemPoolInterface {

    private $em;
    private $deferred_items = [];

    public function __construct($em) {
        $this->em = $em;
    }

    // PSR-6 methods

    public function getItem($key) {
        $item = $this->em->getRepository(DatabaseCacheItem::class)->findOneBy(['key' => $key]);

        if (!$item) {
            $item = new DatabaseCacheItem();
            $item->setKey($key);
        }

        return $item;
    }

    public function getItems(array $keys = []) {
        $values = [];
        foreach ($keys as $key) {
            $values[$key] = $this->getItem($key);
        }

        return $values;
    }

    public function hasItem($key) {
        if (!$item = $this->em->getRepository(DatabaseCacheItem::class)->findOneBy(['key' => $key])) {
            return false;
        }

        return !$item->expired();
    }

    public function clear() {
        try {
            $this->em->createQuery('DELETE FROM ' . DatabaseCacheItem::class)->execute();
        }
        catch (\Exception $e) {
            return false;
        }

        return true;
    }

    public function deleteItem($key) {
        if (!$item = $this->em->getRepository(DatabaseCacheItem::class)->findOneBy(['key' => $key])) {
            return true;
        }

        try {
            $this->em->remove($item);
            $this->em->flush();
            return true;
        }
        catch (\Exception $e) {
            return false;
        }
    }

    public function deleteItems(array $keys = []) {
        $no_errors = true;

        foreach ($keys as $key) {
            $no_errors = $no_errors && $this->deleteItem($key);
        }

        return $no_errors;
    }

    public function save(CacheItemInterface $item) {
        if (!$item->getId()) {
            $this->em->persist($item);
        }

        $this->em->flush();
    }

    public function saveDeferred(CacheItemInterface $item) {
        $this->deferred_items[] = $item;
    }

    public function commit() {
        foreach ($this->deferred_items as $index => $item) {
            if (!$item->getId()) {
                $this->em->persist($item);
            }
        }
        $this->deferred_items = [];

        $this->em->flush();
    }

    public function store($key, $value, $ttl = 84600) {
        if (!$item = $this->em->getRepository(DatabaseCacheItem::class)->findOneBy(['key' => $key])) {
            $item = new DatabaseCacheItem;
            $this->em->persist($item);
        }

        $item
            ->setKey($key)
            ->setValue(json_encode($value))
            ->setTTL($ttl)
            ->setDateCreatedToNow()
        ;

        $this->save($item);
    }
}
