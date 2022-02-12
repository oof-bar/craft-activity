<?php

namespace oofbar\activity\services;

use Craft;
use craft\base\Component;
use craft\base\Element;
use craft\base\ElementInterface;
use craft\db\Query;
use craft\helpers\Db;

use oofbar\activity\events\TrackEvent;
use oofbar\activity\models\Event;
use oofbar\activity\records\Event as EventRecord;

class Events extends Component
{
    /**
     * @var string Emitted prior to saving a new Event.
     */
    const EVENT_BEFORE_TRACK = 'beforeTrack';

    /**
     * @var string Emitted after saving a new Event.
     */
    const EVENT_AFTER_TRACK = 'afterTrack';

    /**
     * @var string Interval expression for the length of time a tracking token will be valid for.
     */
    const DEFAULT_TOKEN_DURATION = 'PT1H';

    /**
     * Executes a count query on Events for the provided Element and category.
     * 
     * @param ElementInterface $element
     * @param string $category
     * @param \DateTime $edge How far back in time to collect.
     * @return array
     */
    public function getElementEventsTotal(ElementInterface $element, string $category, \DateTime $edge = null): int
    {
        $q = (new Query)
            ->select(['value'])
            ->from([EventRecord::tableName()])
            ->where([
                'elementId' => $element->id,
                'category' => $category,
            ]);

        // Apply the edge, if provided:
        if (!is_null($edge)) {
            $q->andWhere(Db::parseDateParam('dateCreated', $edge, '>='));
        }

        return $q->sum('value') ?? 0;
    }

    /**
     * Creates a token that can be consumed or redeemed by a client, within a defined window of time.
     * 
     * @param array $options An array of Event attributes that will hydrate a model before being tracked. Passing an entire Element object under the `element` key is *strongly* discouraged, as serialization and deserialization may result in a significant performance hit or inconsistencies! Use `elementId` whenever possible.
     * @return string Token
     */
    public function trackAsync(array $options, string $duration = self::DEFAULT_TOKEN_DURATION): string
    {
        $expiry = (new \DateTime)->add(new \DateInterval($duration));

        return Craft::$app->getTokens()->createToken(
            ['activity/events/track', ['config' => $options]],
            1,
            $expiry
        );
    }

    /**
     * Builds an analytics event with the provided data.
     * 
     * If more control over the resulting Event is required, use {@see Analytics::saveEvent()} directly.
     * 
     * @param array $options Event configuration.
     * @return Event|false
     */
    public function track(array $options)
    {
        $event = new Event($options);

        if (!$event->validate()) {
            Craft::warning('An event did not pass validation, and will not be tracked.', 'activity');

            return false;
        }

        $trackEvent =  new TrackEvent([
            'event' => $event,
        ]);

        $this->trigger(self::EVENT_BEFORE_TRACK, $trackEvent);

        // Did a plugin cancel it?
        if (!$trackEvent->isValid) {
            Craft::warning("Something stopped a `{$event->category}` event from being tracked.", 'activity');

            return false;
        }

        if (!$this->saveEvent($event)) {
            Craft::warning("Failed to track a `{$event->category}` event.", 'activity');

            return false;
        }

        $this->trigger(self::EVENT_AFTER_TRACK, new TrackEvent([
            'event' => $event,
        ]));

        return $event;
    }

    /**
     * Persists an Event model to the database.
     * 
     * By calling this directly, no {@see oofbar\activity\events\TrackEvent} events will be emitted—this means that other Plugins and modules won't have a chance to intercept or clean up!
     * 
     * @param Event $event Event to save.
     */
    public function saveEvent(Event $event): bool
    {
        $record = new EventRecord;
        $record->elementId = $event->elementId;
        $record->category = $event->category;
        $record->value = $event->value;
        $record->detail = $event->detail;

        if (!$record->save()) {
            return false;
        }

        $event->id = $record->id;
        $event->dateCreated = $record->dateCreated;
        $event->uid = $record->uid;

        return true;
    }
}
