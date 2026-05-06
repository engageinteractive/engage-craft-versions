# Craft Versions Plugin – Developer Setup Guide

## Overview

The Versions plugin exposes an authenticated endpoint that returns version information about Craft CMS, PHP, and installed plugins. It is used by internal agency tooling (Google Sheets) to track software versions across all client sites.

## Requirements

- PHP ≥ 8.2
- Craft CMS ≥ 5.9.0

## Installation

This is a private Composer package installed from its GitHub repository. Add it to the site's `composer.json`:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/engageinteractive/engage-craft-versions"
    }
]
```

Then require it:

```bash
composer require engageinteractive/craft-versions
```

Install the plugin in Craft:

```bash
php craft plugin/install _versions
```

## Configuration

### Local environment

Run the console command to generate a key and write it automatically to `.env`:

```bash
php craft _versions/generate-api-key
```

This will:
- Generate a cryptographically random 32-character API key
- Write `CRAFT_VERSIONS_API_KEY=<key>` to `.env`
- Save the `$CRAFT_VERSIONS_API_KEY` reference in project config

### Staging and production

The console key generator writes to the local `.env` only. On staging and production you must set the environment variable manually via your hosting platform, deploy configuration, or server `.env` file:

```env
CRAFT_VERSIONS_API_KEY=your-key-here
```

> **Note:** You can use the same key value across multiple sites if your team prefers — each site holds its own independent copy of the variable.

### Viewing the key

Admin users can see the resolved value of `CRAFT_VERSIONS_API_KEY` on the plugin settings page in the Craft control panel:

```
/admin/settings/plugins/_versions
```

The field also accepts the `$CRAFT_VERSIONS_API_KEY` environment variable reference directly if you need to update it manually via the CP.

## Endpoint

### `GET /actions/_versions/check`

Returns version information for Craft CMS, PHP, and all installed plugins.

**Authentication:**
- Required header: `apiKey`
- Value: must match `CRAFT_VERSIONS_API_KEY`
- Comparison: timing-safe (`hash_equals`)

**Test locally:**

```bash
curl -X GET http://localhost:8000/actions/_versions/check \
  -H "apiKey: your-key-here"
```

**Success response (200):**
```json
{
  "success": true,
  "data": {
    "craftVersion": "5.4.0",
    "phpVersion": "8.2.15",
    "plugins": {
      "verbb-hyper": "2.2.0",
      "spicyweb-neo": "5.2.1"
    }
  }
}
```

**Failure response (403):**
```json
{
  "success": false,
  "error": "Unauthorised."
}
```

## Console Commands

### `php craft _versions/generate-api-key`

Generates a new random API key and writes it to `.env`. Prompts for confirmation before overwriting an existing key.

**Local use only** — does not work on environments where `.env` is not writable or managed outside the project.

## Google Sheets Integration

Use Google Apps Script (`UrlFetchApp`) to call the endpoint and write data into sheet cells:

```javascript
function fetchVersionInfo() {
  const url = 'https://example.com/actions/_versions/check';
  const apiKey = 'your-key-here';

  const response = UrlFetchApp.fetch(url, {
    method: 'get',
    headers: { 'apiKey': apiKey },
    muteHttpExceptions: true,
  });

  const data = JSON.parse(response.getContentText());

  if (data.success) {
    // Use data.data.craftVersion, data.data.phpVersion, data.data.plugins, etc.
  }
}
```

> Native sheet `IMPORTDATA` / `IMPORTJSON` functions cannot send custom headers. Google Apps Script is required.

## File Structure

```
src/
├── Versions.php                     # Main plugin class
├── controllers/
│   └── CheckController.php         # Endpoint handler
├── console/
│   └── controllers/
│       └── GenerateController.php  # CLI key generation
├── models/
│   └── Settings.php                # Plugin settings model
├── services/
│   └── VersionsService.php         # Version data collection
└── templates/
    └── _settings.twig              # CP settings UI
```

## Troubleshooting

### 403 – Unauthorised

- Verify `CRAFT_VERSIONS_API_KEY` is set in the environment
- Confirm the `apiKey` request header value matches
- On staging/prod, check the key is set in the server environment, not just `.env`
- Sync project config if the plugin settings reference is missing: `php craft project-config/apply`

### 404 – Not found

- The plugin handle is `_versions` (with underscore prefix)
- Confirm the plugin is enabled: Settings → Plugins in the Craft CP
- Verify `"handle": "_versions"` in `composer.json`

### Key generation fails

- The command only works locally where `.env` is writable
- Check file permissions on `.env`
- Review Craft logs at `storage/logs/`

## Security Notes

- The endpoint returns read-only version metadata only — no secrets, paths, or credentials are exposed
- CSRF validation is disabled only for `/actions/_versions/check`
- Comparison uses `hash_equals` to prevent timing attacks
- The resolved API key value is visible to admin users only via the CP settings page

## Support

For issues or changes, contact the development team.
