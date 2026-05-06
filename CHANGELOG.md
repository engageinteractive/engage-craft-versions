# Changelog

All notable changes to the Craft Versions plugin are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [1.0.0] – 2026-05-06

### Added

- Initial release
- **Endpoint:** `GET /actions/_versions/check` – Returns Craft, PHP, and plugin version information
- **Authentication:** API key validation via `apiKey` header (timing-safe comparison)
- **Console command:** `php craft _versions/generate-api-key` – Generate and store random API key
- **Settings:** Plugin configuration panel in Craft CP
- **Craft 5 support:** Full compatibility with Craft CMS 5.9.0+
- **Environment variable support:** API key reference stored as `$CRAFT_VERSIONS_API_KEY`

### Features

- Read-only version metadata endpoint (Craft version, PHP version, installed plugins)
- Automatic random key generation (32 characters)
- Per-site independent configuration
- CSRF exemption for remote sheet integrations
- Constant-time string comparison to prevent timing attacks
