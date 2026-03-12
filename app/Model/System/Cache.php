<?php

namespace App\Model\System;

use Nette\Caching\Storage;
use Nette\Caching\Cache as NetteCache;

class Cache
{
    private $storage;
    private $cache;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
        $this->cache = new NetteCache($storage, 'app_cache');
    }

    /**
     * @param string $key
     * @param callable $fallback
     * @param array|null $tags
     * @return mixed
     */
    public function load(string $key, callable $fallback, ?array $tags = null)
    {
        return $this->cache->load($key, function (&$dependencies) use ($fallback, $tags) {
            if ($tags) {
                $dependencies[NetteCache::Tags] = $tags;
            }
            return $fallback();
        });
    }

    public function save(string $key, $value, ?array $tags = null): void
    {
        $this->cache->save($key, $value, [
            NetteCache::Tags => $tags
        ]);
    }

    public function remove(string $key): void
    {
        $this->cache->remove($key);
    }

    public function clean(array $conditions): void
    {
        $this->cache->clean($conditions);
    }

    public function invalidateByTags(array $tags): void
    {
        $this->cache->clean([
            NetteCache::Tags => $tags
        ]);
    }
}
