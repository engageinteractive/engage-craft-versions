<?php

namespace engageinteractive\craftversions\models;

use Craft;
use craft\base\Model;
use craft\behaviors\EnvAttributeParserBehavior;

/**
 * Settings model for the Versions plugin.
 *
 * Stores and manages the API key used for authenticating requests to the version endpoint.
 * The key is stored as an environment variable reference (e.g., `$CRAFT_VERSIONS_API_KEY`).
 *
 * @since 1.0.0
 */
class Settings extends Model
{
    /** @var string|null API key for endpoint authentication, stored as environment variable reference */
    public ?string $apiKey = null;

    /**
     * Returns the model's behaviours.
     *
     * Registers the EnvAttributeParserBehavior to automatically parse environment variable
     * references in the apiKey attribute (e.g., converts `$CRAFT_VERSIONS_API_KEY` to its value).
     *
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return [
            'parser' => [
                'class' => EnvAttributeParserBehavior::class,
                'attributes' => [
                    'apiKey',
                ],
            ],
        ];
    }

    /**
     * Returns the model's validation rules.
     *
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            ['apiKey', 'string'],
            ['apiKey', 'required'],
            ['apiKey', 'default', 'value' => null],
        ];
    }
}
