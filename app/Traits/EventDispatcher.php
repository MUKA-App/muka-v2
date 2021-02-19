<?php

namespace App\Traits;

use App\Events\Event;

trait EventDispatcher
{
    /** @var Event[] */
    private array $raisedEvents = [];

    public static function bootEventDispatcher()
    {
        static::saved(function ($model) {
            $raisedEvents = $model->popEvents();
            foreach ($raisedEvents as $raisedEvent) {
                event($raisedEvent);
            }
        });
    }

    public function popEvents(): array
    {
        $events = $this->raisedEvents;

        $this->raisedEvents = [];

        return $events;
    }

    public function raise(Event $event)
    {
        $this->raisedEvents[] = $event;
    }
}
