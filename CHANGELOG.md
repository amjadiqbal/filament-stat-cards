# Changelog

All notable changes to `amjadiqbal/filament-stat-cards` are documented in this file.
The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project uses
[Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.0] - 2026-10-23

First release. Requires PHP 8.2+, Laravel 12 or 13 and Filament 4 or 5.

### Added

- `AmjadIqbal\FilamentStatCards\Stat`, a drop-in subclass of Filament's stats overview `Stat` for Filament 4 and 5.
- Progress bars (`progress()`, `progressBarColor()`, `progressLabel()`), with ARIA progressbar markup.
- Icon tiles (`iconBackgroundColor()`, `iconColor()`, `iconPosition('start' | 'end')`).
- Card, label and value colours (`backgroundColor()`, `labelColor()`, `valueColor()`, `textColor()`).
- `trend()`: writes the description, arrow and good/bad colour from two numbers.
- `badge()` and `chartType('bar')` (SVG, no JavaScript).
- Plain-CSS stylesheet: linked when published by `filament:assets`, printed inline otherwise. No build step.
- Dark mode uses only solid colours and neutral surfaces (no translucent warm fills).

[Unreleased]: https://github.com/amjadiqbal/filament-stat-cards/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/amjadiqbal/filament-stat-cards/releases/tag/v1.0.0
