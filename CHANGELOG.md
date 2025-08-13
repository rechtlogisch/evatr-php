# Changelog

All notable changes to `evatr` will be documented in this file.

## v0.1.0 - 2025-08-13

🚀 What’s in this Release:
- 🆔 Added optional `id` field (introduced in api-docs v1.2.3.9 on 2025-08-13):
    - Parse `id` (string|null) from API responses. 
    - New getter `getId(): ?string`. 
    - Include `id` in toArray() serialization output.
- 💔 Renamed `checkAvailability()` to `getAvailability()`
- 🛠️ Refactored request, throw ErrorResponse when `anfrageZeitpunkt` or `status` key is missing, passed PHPStan level 9, added unified error handling, increased test coverage, tackled TODOs in code, removed obsolete comments.
- 📝 Updated documentation.

## v0.0.2 - 2025-08-09

🚀 What’s in this Release:

- ✨ Additional API endpoints:
   - 📨 Status messages
   - 🇪🇺 EU member state availability
   
- 🗣️ Added English status messages (configure via environment variable `EVATR_LANG=en`)
- 🛠️ Minor fixes and enhancements to the developer workflow
- 📝 Documentation updates
- 🔃 Daily check for changes in status messages (GitHub workflow)

### PRs

* feat: v1 all endpoints by @zembrowski in https://github.com/rechtlogisch/evatr-php/pull/1

**Full Changelog**: https://github.com/rechtlogisch/evatr-php/compare/v0.0.1...v0.0.2

## v0.0.1 (2025-08-07)

- Initial release of `evatr`
