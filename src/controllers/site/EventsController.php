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
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        // Let Craft take care of core features, first:
        if (!parent::beforeAction($action)) {
            return false;
        }

        // Don't bother with CSRF for tracking requests:
        if ($action->id === 'track') {
            $this->enableCsrfValidation = false;
        }

        // Craft being fully-typed can't come soon enough!
        return true;
    }

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
            return $this->asErrorJson(Craft::t('activity', 'Failed to log the event.'));
        }

        return $this->asJson([
            'uid' => $event->uid,
        ]);
    }
}
