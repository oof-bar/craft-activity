<?php

namespace oofbar\activity\controllers\site;

use oofbar\activity\controllers\BaseWebController;

/**
 * Root site-facing Controller class that all other Controllers in the namespace should extend.
 * 
 * Action routes do not belong here, as this class is not instantiable.
 */
abstract class BaseSiteController extends BaseWebController
{}
