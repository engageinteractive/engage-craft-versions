<?php

namespace engageinteractive\craftversions;

use Craft;
use craft\base\Model;
use craft\base\Plugin;
use engageinteractive\craftversions\models\Settings;
use engageinteractive\craftversions\services\VersionsService;
use craft\events\RegisterUrlRulesEvent;
use yii\base\Event;
use craft\web\UrlManager;

/**
 * Versions plugin – exposes an authenticated endpoint for retrieving Craft and plugin version information.
 *
 * Provides a read-only JSON API endpoint (`/actions/_versions/check`) that returns version metadata
 * for Craft CMS, PHP, and all installed plugins. Intended for internal agency tooling (Google Sheets,
 * dashboards, etc.) to track software versions across multiple client sites.
 *
 * @method static Versions getInstance()
 * @method Settings getSettings()
 * @property-read VersionsService $versionsService Service for collecting and managing version data
 *
 * @since 1.0.0
 */
class Versions extends Plugin
{
    /** @var string Plugin schema version */
    public string $schemaVersion = '1.0.0';

    /** @var bool Whether this plugin has control panel settings */
    public bool $hasCpSettings = true;

    public static function config(): array
    {
        return [
            'components' => ['versionsService' => VersionsService::class],
        ];
    }

    /**
     * Initialises the plugin.
     *
     * Sets up the plugin alias, controller namespace, and services.
     *
     * @return void
     */
    public function init(): void
    {
        parent::init();

        Craft::setAlias('@versions', __DIR__);

        // Route console and web requests to their respective controller namespaces
        if (Craft::$app->getRequest()->getIsConsoleRequest()) {
            $this->controllerNamespace = 'engageinteractive\\craftversions\\console\\controllers';
        } else {
            $this->controllerNamespace = 'engageinteractive\\craftversions\\controllers';
        }

        $this->attachEventHandlers();

        // Any code that creates an element query or loads Twig should be deferred until
        // after Craft is fully initialized, to avoid conflicts with other plugins/modules
        Craft::$app->onInit(function () {});
    }

    /**
     * Creates and returns the plugin settings model.
     *
     * @return Settings|null The settings model
     */
    protected function createSettingsModel(): ?Model
    {
        return Craft::createObject(Settings::class);
    }

    /**
     * Returns the HTML for the plugin settings page.
     *
     * @return string|null The rendered settings template
     */
    protected function settingsHtml(): ?string
    {
        return Craft::$app->view->renderTemplate('_versions/_settings.twig', [
            'plugin' => $this,
            'settings' => $this->getSettings(),
        ]);
    }

    /**
     * Attaches event handlers for the plugin.
     *
     * Reserved for future event-driven functionality.
     *
     * @return void
     */
    private function attachEventHandlers(): void
    {
        // Reserve for future event listeners if needed
    }
}
