<?php

namespace engageinteractive\craftversions\services;

use Craft;
use craft\helpers\App;
use engageinteractive\craftversions\models\Settings;
use engageinteractive\craftversions\Versions;
use Throwable;
use yii\base\Component;
use yii\base\Exception;

/**
 * Service for managing and collecting version information.
 *
 * Handles generation and storage of the API key, and collects current system version data
 * (Craft, PHP, plugins). This service centralises all version-related logic for the plugin.
 *
 * @since 1.0.0
 */
class VersionsService extends Component
{
    /**
     * Generates and saves a new API key.
     *
     * Called by the console command to create a random 32-character key, store it in `.env`,
     * and update the plugin settings. Errors are logged but not re-thrown.
     *
     * @return void
     */
    public function setup(): void
    {
        try {
            $this->saveApiKey();
        } catch (Throwable $e) {
            Craft::error('Failed to generate API key: ' . $e->getMessage(), __METHOD__);
        }
    }

    /**
     * Persists the API key to project settings.
     *
     * Generates a new key and stores it in the `.env` file, then updates the plugin settings
     * in the project config to reference `$CRAFT_VERSIONS_API_KEY`.
     *
     * @return void
     * @throws Exception
     */
    private function saveApiKey(): void
    {
        $this->createEnvVariableName();
        $settings = new Settings();
        $settings->apiKey = '$CRAFT_VERSIONS_API_KEY';
        Craft::$app->plugins->savePluginSettings(Versions::getInstance(), $settings->toArray());
    }

    /**
     * Saves the API key to the `.env` file.
     *
     * Uses Craft's config API to write the generated key to `.env` as `CRAFT_VERSIONS_API_KEY`.
     *
     * @return void
     * @throws Exception
     */
    private function createEnvVariableName(): void
    {
        Craft::$app->getConfig()->setDotEnvVar('CRAFT_VERSIONS_API_KEY', $this->generateApiKey());
    }

    /**
     * Generates a random API key.
     *
     * Creates a cryptographically secure random 32-character string for use as the API key.
     *
     * @return string A random 32-character API key
     * @throws Exception
     */
    private function generateApiKey(): string
    {
        return Craft::$app->security->generateRandomString();
    }

    /**
     * Collects and returns the current version information.
     *
     * Gathers the Craft CMS version, PHP version, and all installed plugin versions.
     * This data is returned by the authenticated endpoint.
     *
     * @return array Version data in the format:
     *               [
     *                   'craftVersion' => string,
     *                   'phpVersion' => string,
     *                   'plugins' => ['handle' => 'version', ...]
     *               ]
     */
    public function fetchVersion(): array
    {
        $craftVersion = Craft::$app->getVersion();
        $phpVersion = App::phpVersion();
        $plugins = array_map(function ($plugin) {
            return $plugin->getVersion();
        }, Craft::$app->getPlugins()->getAllPlugins());

        return [
            'craftVersion' => $craftVersion,
            'phpVersion' => $phpVersion,
            'plugins' => $plugins,
        ];
    }
}
