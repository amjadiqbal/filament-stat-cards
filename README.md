<p align="center">
  <picture>
    <source media="(prefers-color-scheme: dark)" srcset="https://raw.githubusercontent.com/amjadiqbal/filament-stat-cards/main/art/banner-dark.png">
    <img alt="Stat Cards: richer stats overview widgets for Filament" src="https://raw.githubusercontent.com/amjadiqbal/filament-stat-cards/main/art/banner-light.png" width="100%">
  </picture>
</p>

# Stat Cards for Filament

Richer stat cards for the Filament stats overview widget: progress bars, icon tiles, background, label and value
colours, trends, badges and bar charts. It works with Filament 4 and 5, inside the `StatsOverviewWidget` you already
have.

[![Latest Version](https://img.shields.io/packagist/v/amjadiqbal/filament-stat-cards.svg?style=flat-square)](https://packagist.org/packages/amjadiqbal/filament-stat-cards)
[![PHP Version](https://img.shields.io/packagist/php-v/amjadiqbal/filament-stat-cards?style=flat-square)](https://packagist.org/packages/amjadiqbal/filament-stat-cards)
[![License](https://img.shields.io/packagist/l/amjadiqbal/filament-stat-cards.svg?style=flat-square)](LICENSE.md)

![Stat cards with icon tiles, trends, progress bars and a bar chart in a Filament panel](https://raw.githubusercontent.com/amjadiqbal/filament-stat-cards/main/art/screenshots/stats-light.png)

<p align="center">
  <img alt="The same stat cards in dark mode" src="https://raw.githubusercontent.com/amjadiqbal/filament-stat-cards/main/art/screenshots/stats-dark.png" width="100%">
</p>

## What it does

Filament's `Stat` shows a label, a value, a description and a sparkline. This package's `Stat` is a subclass of it, so
everything core does still works, and it adds:

- **Progress bars**, with a custom maximum and an optional label ("72%" or your own text).
- **Icon tiles**: the icon in a coloured square, at the start of the label row or at the far end of the card.
- **Colours** for the card background, the icon, the label, the value and the description.
- **Trends**: give it the current and previous numbers and it writes "12.4% increase" with an arrow, green when the
  change is good and red when it is bad (or the other way round for refunds, churn and errors).
- **Badges** next to the label ("Live", "Beta", "This month").
- **Bar charts** as well as core's line sparkline. The bars are plain SVG, with no JavaScript.

There is no new widget class and no Livewire component to register. You change one `use` line in a widget you already
have.

## Requirements

- PHP 8.2+
- Laravel 12 or 13
- Filament 4 or 5

## Installation

```bash
composer require amjadiqbal/filament-stat-cards
```

That is all. The service provider is discovered automatically. No theme, Tailwind config or `npm run build` is needed:
the styles are plain CSS. If you run `php artisan filament:assets` (Filament's installer adds it to Composer's
`post-autoload-dump`), the panel links the published stylesheet. If you don't, the same CSS (about 5 KB) is printed
inline in the panel's `<head>`.

## Usage

Swap the import in any stats overview widget:

```diff
- use Filament\Widgets\StatsOverviewWidget\Stat;
+ use AmjadIqbal\FilamentStatCards\Stat;
```

```php
use AmjadIqbal\FilamentStatCards\Stat;
use Filament\Widgets\StatsOverviewWidget;

class SalesOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Revenue', '$48,200')
                ->icon('heroicon-o-banknotes')
                ->iconBackgroundColor('primary')
                ->iconPosition('end')
                ->trend(48200, 42880)            // "12.4% increase", green, arrow up
                ->progress(48200, 60000)         // 80% of the monthly target
                ->progressLabel('80% of the $60k target')
                ->badge('This month', 'primary'),

            Stat::make('Refunds', '31')
                ->icon('heroicon-o-receipt-refund')
                ->iconColor('danger')
                ->trend(31, 40, higherIsBetter: false) // "22.5% decrease", green
                ->chart([4, 9, 6, 12, 8, 14])
                ->chartType('bar')
                ->chartColor('danger'),

            Stat::make('New signups', '1,204')
                ->backgroundColor('info')
                ->valueColor('info')
                ->description('Since Monday')
                ->chart([1, 3, 2, 5, 4, 6]),        // core's line sparkline
        ];
    }
}
```

Core `Stat` objects and these ones can sit in the same widget.

### All methods

Everything on core's `Stat` (`description()`, `descriptionIcon()`, `descriptionColor()`, `color()`, `chart()`,
`chartColor()`, `icon()`, `url()`, `extraAttributes()` …) works as before. Added:

| Method | What it does |
|---|---|
| `progress($value, $max = 100)` | Progress bar filled to `$value / $max`, clamped to 0-100%. Accepts closures. |
| `progressBarColor($color)` / `progressColor($color)` | Bar colour. Defaults to the stat's `color()`, then `primary`. |
| `progressLabel(true \| string)` | `true` shows the percentage under the bar; a string shows that text. |
| `iconBackgroundColor($color)` | Shows the icon in a coloured tile. |
| `iconColor($color)` | Colours the icon (and overrides the tile's icon colour). |
| `iconPosition('start' \| 'end')` | `start` (default) is before the label, as in core. `end` is at the far end of the top row. `before`/`after` and `IconPosition` also work. |
| `backgroundColor($color)` | Tints the card. In dark mode the card keeps its neutral surface and gets a coloured top line. |
| `labelColor($color)`, `valueColor($color)` | Text colours. |
| `textColor($label, $value, $description)` | All three at once; pass `null` to skip one. |
| `trend($current, $previous, $higherIsBetter = true, $precision = 1)` | Writes the description, icon and colour from the change. Your own `description()`, `descriptionIcon()` or `descriptionColor()` still wins. |
| `badge($text, $color = null)` | A small badge after the label. Colour defaults to the stat's `color()`, then gray. |
| `chartType('line' \| 'bar')` | `line` is core's sparkline; `bar` draws the same data as columns. |

Colours are any Filament colour name (`primary`, `success`, `warning`, `danger`, `info`, `gray`, or one you
registered) or a palette array such as `Color::Sky` or `Color::hex('#0ea5e9')`.

### Translations

The trend and progress texts are translatable. Publish them with:

```bash
php artisan vendor:publish --tag=filament-stat-cards-translations
```

## Coming from eightynine/filament-advanced-widgets

[eightynine/filament-advanced-widgets](https://github.com/eighty9nine/filament-advanced-widgets) (MIT, by Eighty Nine)
popularised these cards and supports Filament 3 and 4. Stat Cards is a separate, independent implementation for
Filament 4 and 5 that accepts the same stat method names (`backgroundColor`, `progress`, `progressBarColor`,
`iconBackgroundColor`, `iconColor`, `iconPosition`, `labelColor`, `valueColor`, `textColor`, `descriptionIcon`,
`descriptionColor`, `chartColor`). To move a stats widget across:

1. Replace `use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;` with
   `use AmjadIqbal\FilamentStatCards\Stat;`.
2. Replace `use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;` with
   `use Filament\Widgets\StatsOverviewWidget as BaseWidget;`.

Check the result in the browser: the cards look different, and this package only covers the stats widget, not
eightynine's chart or table widgets.

## Dark mode and colour

Every colour comes from your panel's palette, so cards follow your theme. In dark mode, colours appear as solid text,
lines, bars and borders. The package never puts a translucent colour wash on a dark surface, where warm colours turn
muddy. It also sets core's sparkline to a line without an area fill in dark mode, inside these cards only.

## Testing

```bash
composer test
composer analyse
scripts/ci-local.sh   # PHP 8.2-8.5 x Laravel 12-13 x Filament 4-5
```

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## Support

- 🐛 **Bug or feature request**: [open an issue](https://github.com/amjadiqbal/filament-stat-cards/issues)
- 🔒 **Security issue**: please report it privately through GitHub's security tab

## Need this customised, or something built?

I'm available for custom development, package integration, and technical consulting.

[![Hire me on Upwork](https://img.shields.io/badge/Hire%20me-Upwork-6FDA44?style=flat-square&logo=upwork&logoColor=white)](https://www.upwork.com/freelancers/amjadkhatri)

## More packages

This is part of a family of open-source packages. See [all of them](https://github.com/amjadiqbal?tab=repositories).

| | |
|---|---|
| [filament-deleted-records](https://github.com/amjadiqbal/filament-deleted-records) | A recycle bin for Filament: browse, restore or purge deleted models |
| [filament-change-requests](https://github.com/amjadiqbal/filament-change-requests) | Hold record edits in Filament for approval, with a field-by-field diff |
| [filament-url-image-uploader](https://github.com/amjadiqbal/filament-url-image-uploader) | Upload images to Filament from a URL |

## Credits

- [Amjad Iqbal](https://github.com/amjadiqbal)
- The idea and the stat method names follow
  [eightynine/filament-advanced-widgets](https://github.com/eighty9nine/filament-advanced-widgets) (MIT). No code is
  shared, and this package is not affiliated with or endorsed by its author.

## License

The MIT License (MIT). See [LICENSE.md](LICENSE.md).
