<?php

namespace oofbar\activity\twig;

use Craft;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

use oofbar\activity\Activity;

class ActivityExtension extends AbstractExtension
{
    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return 'Activity Helpers';
    }

    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('track', [Activity::getInstance()->getEvents(), 'track']),
            new TwigFunction('trackAsync', [Activity::getInstance()->getEvents(), 'trackAsync']),
            new TwigFunction('trackXhr', [Activity::getInstance()->getEvents(), 'trackXhr']),
        ];
    }
}
