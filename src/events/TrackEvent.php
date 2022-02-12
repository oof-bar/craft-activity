<?php

namespace oofbar\activity\events;

use craft\events\CancelableEvent;

use oofbar\activity\models\Event;

/**
 * Emitted in the process of tracking an analytics Event model.
 * 
 * The naming here is a little confusing, as the class extends {@see craft\events\CancelableEvent}, but includes a single attribute for an {@see oofbar\activity\models\Event}!
 */
class TrackEvent extends CancelableEvent
{
    /**
     * @var Event
     */
    public $event;
}
