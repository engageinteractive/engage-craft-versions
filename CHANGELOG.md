# Changelog

All notable changes to the Craft Versions plugin are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [2.0.0] – 2026-06-02

### Changed

- Started the 2.x release line for Craft CMS 5.
- Updated Composer dependencies.
- Lowered the minimum Craft CMS 5 version requirement.

## [1.0.3] – 2026-06-01

### Changed

- Update composer dependencies

## [1.0.2] – 2026-05-13

### Changed

- Updated endpoint handling to require JSON-capable requests and return `401` for missing or invalid authentication
- Restricted the version-check endpoint to `GET` requests only
- Updated the documentation to reflect the current API key flow and endpoint requirements

## [1.0.1] – 2026-05-06

### Changed

## [1.0.0] – 2026-05-06

### Added

- Initial release
- **Endpoint:** `GET /actions/versions/check` – Returns Craft, PHP, and plugin version information
- **Authentication:** API key validation via `apiKey` header (timing-safe comparison)
- **Console command:** `php craft versions/generate-api-key` – Generate and store random API key
- **Settings:** Plugin configuration panel in Craft CP
- **Craft 5 support:** Full compatibility with Craft CMS 5.9.0+
- **Environment variable support:** API key reference stored as `$CRAFT_VERSIONS_API_KEY`

### Features

- Read-only version metadata endpoint (Craft version, PHP version, installed plugins)
- Automatic random key generation (32 characters)
- Per-site independent configuration
- CSRF exemption for remote sheet integrations
- Constant-time string comparison to prevent timing attacks
