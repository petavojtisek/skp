<?php

namespace App\Model\System;

class ModelEventManager
{
    /** @var callable[] */
    private array $listeners = [];

    private Cache $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
        
        // Register default internal listeners
        $this->on('rights_changed', function(int $groupId) {
            $this->cache->invalidateByTags(['rights_group_' . $groupId]);
        });
    }

    /**
     * Subscribe to an event
     */
    public function on(string $event, callable $callback): void
    {
        $this->listeners[$event][] = $callback;
    }

    /**
     * Trigger an event
     */
    public function trigger(string $event, ...$args): void
    {
        if (isset($this->listeners[$event])) {
            foreach ($this->listeners[$event] as $callback) {
                call_user_func_array($callback, $args);
            }
        }
    }
}
