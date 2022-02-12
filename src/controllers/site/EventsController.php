<?php

namespace oofbar\activity\controllers\site;

use Craft;
use craft\web\Response;

use oofbar\activity\Activity;

class EventsController extends BaseSiteController
{
    /**
     * @inheritdoc
     */
    protected $allowAnonymous = [
        'track'
    ];

    /**
     * Tracks an Event that was previously set up with a token.
     * 
     * @param array $config Event configuration object.
     */
    public function actionTrack(array $config): Response
    {
        $this->requireToken();

        $event = Activity::getInstance()->getEvents()->track($config);

        if (!$event) {
            return $this->_sendErrorResponse(Craft::t('activity', 'Failed to log the event.'));
        }

        return $this->_sendSuccessResponse(Craft::t('activity', 'Event logged.'), [
            'uid' => $event->uid,
        ]);
    }
}
