<?php

namespace App\Service;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class CacheService  {
    public function set(string $key, $value, $ttlInSeconds = null): void {
        $cache = new FilesystemAdapter();

        $cacheItem = $cache->getItem($key);

        // assign a value to the item and save it
        $cacheItem->set($value);
        $cacheItem->expiresAfter($ttlInSeconds);
        $cache->save($cacheItem);
    }

    public function get(string $key) {
        $cache = new FilesystemAdapter();
        $cacheValue = false; // return false if not cache does not exist

        // get value. null if non-existing
        $cacheItem = $cache->getItem($key);
        if ($cacheItem->isHit()) {
            $cacheValue = $cacheItem->get();
        }

        return $cacheValue;
    }

    public function flushAll() {
        $cache = new FilesystemAdapter();
        $cache->clear();
    }
}