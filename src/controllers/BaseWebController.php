<?php
namespace oofbar\activity\controllers;

use Craft;
use craft\web\Controller;
use craft\web\Response;

/**
 * The Base class for all web-facing Controllers (Site and CP).
 * 
 * This should not be directly extended by a Controller, but instead go via a namespace-specific `Base____Controller` class.
 */
abstract class BaseWebController extends Controller
{
    /**
     * Returns an error based on the content type that the client accepts.
     * 
     * @param string $message
     * @param array|null $params Route params to pass back to the template.
     */
    protected function _sendErrorResponse(string $message, array $params = []): ?Response
    {
        if (Craft::$app->getRequest()->getAcceptsJson()) {
            return $this->asJson([
                'success' => false,
                'message' => $message
            ]);
        }

        Craft::$app->getSession()->setError($message);

        if ($params) {
            Craft::$app->getUrlManager()->setRouteParams($params);
        }

        return null;
    }

    /**
     * Returns a successful response based on the content type that the client accepts.
     * 
     * @param string $message
     * @param mixed $redirectParams Data to make available when rendering the redirect string template or in a JSON response under the `data` key. Can be an object or array.
     * @param string|null $defaultRedirect If no `redirect` value is sent, this one is used.
     */
    protected function _sendSuccessResponse(string $message, $redirectParams = null, string $defaultRedirect = null): Response
    {
        if (Craft::$app->getRequest()->getAcceptsJson()) {
            return $this->asJson([
                'success' => true,
                'message' => $message,
                'data' => $redirectParams
            ]);
        }

        Craft::$app->getSession()->setNotice($message);

        return $this->redirectToPostedUrl($redirectParams, $defaultRedirect);
    }
}
