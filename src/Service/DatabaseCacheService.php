<?php

namespace App\Service;

use App\Entity\DatabaseCacheItem;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\CacheItemInterface;

class DatabaseCacheService implements CacheItemPoolInterface {

    private $em;
    private $deferred_items = [];

    public function __construct($em) {
        $this->em = $em;
    }

    // PSR-6 methods

    public function getItem($key): CacheItemInterface
    {
        $item = $this->em->getRepository(DatabaseCacheItem::class)->findOneBy(['key' => $key]);

        if (!$item) {
            $item = new DatabaseCacheItem();
            $item->setKey($key);
        }

        return $item;
    }

    public function getItems(array $keys = []): iterable
    {
        $values = [];
        foreach ($keys as $key) {
            $values[$key] = $this->getItem($key);
        }

        return $values;
    }

    public function hasItem($key): bool
    {
        if (!$item = $this->em->getRepository(DatabaseCacheItem::class)->findOneBy(['key' => $key])) {
            return false;
        }

        return !$item->expired();
    }

    public function clear(): bool
    {
        try {
            $this->em->createQuery('DELETE FROM ' . DatabaseCacheItem::class)->execute();
        }
        catch (\Exception $e) {
            return false;
        }

        return true;
    }

    public function deleteItem($key): bool
    {
        if (!$item = $this->em->getRepository(DatabaseCacheItem::class)->findOneBy(['key' => $key])) {
            return true;
        }

        try {
            $this->em->remove($item);
            $this->em->flush();
        }
        catch (\Exception $e) {
            return false;
        }

        return true;
    }

    public function deleteItems(array $keys = []): bool
    {
        $no_errors = true;

        foreach ($keys as $key) {
            $no_errors = $no_errors && $this->deleteItem($key);
        }

        return $no_errors;
    }

    public function save(CacheItemInterface $item): bool
    {
        if (!$item->getId()) {
            $this->em->persist($item);
        }

        $this->em->flush();

        return true;
    }

    public function saveDeferred(CacheItemInterface $item): bool
    {
        $this->deferred_items[] = $item;
        return true;
    }

    public function commit(): bool
    {
        foreach ($this->deferred_items as $index => $item) {
            if (!$item->getId()) {
                $this->em->persist($item);
            }
        }
        $this->deferred_items = [];

        $this->em->flush();

        return true;
    }
}
