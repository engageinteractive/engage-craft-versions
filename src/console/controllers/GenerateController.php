<?php

namespace engageinteractive\craftversions\console\controllers;

use Craft;
use craft\console\Controller;
use yii\console\ExitCode;
use engageinteractive\craftversions\Versions;
use Throwable;
use craft\helpers\Console;

/**
 * Console command controller for the Versions plugin.
 *
 * Provides CLI commands for managing the plugin, specifically for generating and storing
 * the API key used to authenticate endpoint requests.
 *
 * @since 1.0.0
 */
class GenerateController extends Controller
{
    /** @var string The default action to execute */
    public $defaultAction = 'api-key';

    /**
     * Returns the available options for a given action.
     *
     * @param string $actionID The action ID
     * @return array The options for the action
     */
    public function options($actionID): array
    {
        $options = parent::options($actionID);
        switch ($actionID) {
            case 'api-key':
                break;
        }
        return $options;
    }

    /**
     * Generates a new API key and saves it to `.env` and project config.
     *
     * Prompts the user for confirmation before generating and storing a new 32-character
     * random API key. The key is saved as `CRAFT_VERSIONS_API_KEY` in the `.env` file.
     *
     * Usage:
     *   php craft _versions/generate-api-key
     *
     * @return int Exit code (0 on success, non-zero on error)
     */
    public function actionApiKey(): int
    {
        if (Console::confirm("This will generate a new API key and overwrite the existing one. Are you sure?")) {
            try {
                Versions::getInstance()->versionsService->setup();
                $this->stdout("API key generated and saved successfully.\n");
            } catch (Throwable $e) {
                $this->stderr('Error generating API key: ' . $e->getMessage() . "\n");
                return ExitCode::UNSPECIFIED_ERROR;
            }
        } else {
            $this->stdout("Generation cancelled.\n");
            return ExitCode::OK;
        }
        return ExitCode::OK;
    }
}
