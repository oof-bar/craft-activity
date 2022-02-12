<?php
namespace oofbar\activity;

use Craft;
use craft\base\Plugin;
use craft\i18n\PhpMessageSource;

use oofbar\activity\services\Events;
use oofbar\activity\twig\ActivityExtension;

/**
 * Base Activity plugin class.
 * 
 * Responsible for bootstrapping and injecting custom features.
 */
class Activity extends Plugin
{
    /**
     * @inheritdoc
     */
    public function __construct($id, $parent = null, array $config = [])
    {
        Craft::$app->getI18n()->translations[$id] = [
            'class' => PhpMessageSource::class,
            'sourceLanguage' => 'en-US',
            'basePath' => '@activity/translations',
            'forceTranslation' => true,
            'allowOverrides' => true,
        ];

        parent::__construct($id, $parent, $config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        Craft::setAlias('@activity', $this->getBasePath());

        $this->setComponents([
            'events' => Events::class,
        ]);

        Craft::$app->getView()->registerTwigExtension(new ActivityExtension);
    }

    /**
     * Returns the Events service/component.
     * 
     * @return Events
     */
    public function getEvents(): Events
    {
        return $this->get('events');
    }
}
