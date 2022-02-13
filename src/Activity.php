<?php
namespace oofbar\activity;

use Craft;
use craft\base\Plugin;
use craft\i18n\PhpMessageSource;
use craft\events\RegisterTemplateRootsEvent;
use craft\web\View;

use yii\base\Event;

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

        // Set the controller namespace based on the type of request:
        $request = Craft::$app->getRequest();

        if ($request->getIsConsoleRequest()) {
            $this->controllerNamespace = 'oofbar\\activity\\controllers\\console';
        } else if ($request->getIsCpRequest()) {
            $this->controllerNamespace = 'oofbar\\activity\\controllers\\cp';
        } else {
            $this->controllerNamespace = 'oofbar\\activity\\controllers\\site';
        }

        Event::on(
            View::class,
            View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS,
            function (RegisterTemplateRootsEvent $e) {
                $e->roots[$this->id] = Craft::getAlias('@activity/templates');
            });

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
