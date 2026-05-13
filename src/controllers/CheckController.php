<?php

namespace engageinteractive\craftversions\controllers;

use Craft;
use craft\helpers\App;
use craft\web\Controller;
use craft\web\ServiceUnavailableHttpException;
use engageinteractive\craftversions\Versions;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\base\Action;
use yii\web\UnauthorizedHttpException;

/**
 * API endpoint controller for the Versions plugin.
 *
 * Handles authenticated requests to the `/actions/versions/check` endpoint.
 * Returns version information (Craft, PHP, plugins) when a valid API key is provided.
 *
 * @since 1.0.0
 */
class CheckController extends Controller
{
    /** @var string Default action to execute */
    public $defaultAction = 'check';

    /** @var array|int|bool Actions that do not require authentication */
    protected array|int|bool $allowAnonymous = ['check'];

    protected array $verbs = [
        'check' => ['GET'],
    ];

    /**
     * Runs before each action.
     *
     * Disables CSRF validation for the check action to allow remote requests (e.g., from Google Sheets)
     * to authenticate via the `apiKey` header without CSRF tokens.
     *
     * @param Action $action The action being executed
     * @return bool Whether to continue executing the action
     * @throws BadRequestHttpException
     * @throws ServiceUnavailableHttpException
     * @throws ForbiddenHttpException
     * @throws UnauthorizedHttpException
     */
    public function beforeAction($action): bool
    {
        if ($action->id === 'check') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    /**
     * Returns version information when authenticated with the API key.
     *
     * Expects the `apiKey` header to contain the API key. Uses timing-safe comparison
     * to prevent timing attacks. Returns version data on successful authentication,
     * or a 403 error if the key is missing or incorrect.
     *
     * Request header:
     *   apiKey: your-32-character-key
     *
     * Success response (200):
     *   {
     *     "success": true,
     *     "data": {
     *       "craftVersion": "5.4.0",
     *       "phpVersion": "8.2.15",
     *       "plugins": {"handle": "version", ...}
     *     }
     *   }
     *
     * Failure response (403):
     *   {"success": false, "error": "Unauthorised."}
     *
     * @return Response JSON response with version data or error message
     * @throws BadRequestHttpException
     */
    public function actionCheck(): Response
    {
        $this->requireAcceptsJson();
        $token = Craft::$app->request->getHeaders()->get('apiKey');
        if (!$token) {
            $this->response->setStatusCode(401);
            return $this->asJson(['success' => false, 'error' => 'Unauthorised.']);
        }
        $settings = Versions::getInstance()->getSettings();
        $storedKey = App::parseEnv($settings->apiKey);
        if ($storedKey && hash_equals($storedKey, $token)) {
            $this->response->setStatusCode(200);
            $versionData = Versions::getInstance()->versionsService->fetchVersion();
            return $this->asJson(['success' => true, 'data' => $versionData]);
        }
        $this->response->setStatusCode(401);
        return $this->asJson(['success' => false, 'error' => 'Unauthorised.']);
    }
}
