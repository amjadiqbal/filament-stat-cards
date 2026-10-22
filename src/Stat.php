<?php

declare(strict_types=1);

namespace AmjadIqbal\FilamentStatCards;

use BackedEnum;
use Closure;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Stat as BaseStat;
use Illuminate\Contracts\Support\Htmlable;
use InvalidArgumentException;

/**
 * A drop-in replacement for Filament's stats overview `Stat`: return it from `getStats()` of any
 * `StatsOverviewWidget`. Everything the core stat does still works; this adds progress bars, icon tiles,
 * background, label and value colours, trends, a badge and a bar chart.
 */
class Stat extends BaseStat
{
    public const COLOR_SHADES = [50, 100, 200, 300, 400, 500, 600];

    protected string $view = 'filament-stat-cards::stat';

    /** @var string | array<int | string, string> | Closure | null */
    protected string|array|Closure|null $backgroundColor = null;

    /** @var string | array<int | string, string> | Closure | null */
    protected string|array|Closure|null $iconColor = null;

    /** @var string | array<int | string, string> | Closure | null */
    protected string|array|Closure|null $iconBackgroundColor = null;

    protected string|Closure|null $iconPosition = null;

    /** @var string | array<int | string, string> | Closure | null */
    protected string|array|Closure|null $labelColor = null;

    /** @var string | array<int | string, string> | Closure | null */
    protected string|array|Closure|null $valueColor = null;

    protected int|float|Closure|null $progress = null;

    protected int|float|Closure $progressMax = 100;

    /** @var string | array<int | string, string> | Closure | null */
    protected string|array|Closure|null $progressBarColor = null;

    protected bool|string|Htmlable|Closure $progressLabel = false;

    protected string|Closure $chartType = 'line';

    protected int|float|Closure|null $trendCurrent = null;

    protected int|float|Closure|null $trendPrevious = null;

    protected bool $trendHigherIsBetter = true;

    protected int $trendPrecision = 1;

    protected string|Htmlable|Closure|null $badge = null;

    /** @var string | array<int | string, string> | Closure | null */
    protected string|array|Closure|null $badgeColor = null;

    /**
     * Tint the whole card. In dark mode the card keeps its neutral surface and gets a solid accent line instead,
     * so warm colours never become a translucent (brown-looking) wash.
     *
     * @param  string | array<int | string, string> | Closure | null  $color
     */
    public function backgroundColor(string|array|Closure|null $color): static
    {
        $this->backgroundColor = $color;

        return $this;
    }

    /**
     * @param  string | array<int | string, string> | Closure | null  $color
     */
    public function iconColor(string|array|Closure|null $color): static
    {
        $this->iconColor = $color;

        return $this;
    }

    /**
     * Show the icon in a coloured tile.
     *
     * @param  string | array<int | string, string> | Closure | null  $color
     */
    public function iconBackgroundColor(string|array|Closure|null $color): static
    {
        $this->iconBackgroundColor = $color;

        return $this;
    }

    /**
     * `start` puts the icon before the label, as core does. `end` puts it at the far end of the card's top row.
     * `before` / `after` and `IconPosition` are accepted as synonyms.
     */
    public function iconPosition(string|IconPosition|Closure|null $position): static
    {
        $this->iconPosition = $position instanceof IconPosition ? $position->value : $position;

        return $this;
    }

    /**
     * @param  string | array<int | string, string> | Closure | null  $color
     */
    public function labelColor(string|array|Closure|null $color): static
    {
        $this->labelColor = $color;

        return $this;
    }

    /**
     * @param  string | array<int | string, string> | Closure | null  $color
     */
    public function valueColor(string|array|Closure|null $color): static
    {
        $this->valueColor = $color;

        return $this;
    }

    /**
     * Set the label, value and description colours in one call. Pass null to leave one unchanged.
     *
     * @param  string | array<int | string, string> | null  $label
     * @param  string | array<int | string, string> | null  $value
     * @param  string | array<int | string, string> | null  $description
     */
    public function textColor(string|array|null $label = null, string|array|null $value = null, string|array|null $description = null): static
    {
        if ($label !== null) {
            $this->labelColor($label);
        }

        if ($value !== null) {
            $this->valueColor($value);
        }

        if ($description !== null) {
            $this->descriptionColor($description);
        }

        return $this;
    }

    /**
     * Show a progress bar. The bar fills `$value / $max`, clamped to 0-100%.
     */
    public function progress(int|float|Closure|null $value, int|float|Closure $max = 100): static
    {
        $this->progress = $value;
        $this->progressMax = $max;

        return $this;
    }

    /**
     * @param  string | array<int | string, string> | Closure | null  $color
     */
    public function progressBarColor(string|array|Closure|null $color): static
    {
        $this->progressBarColor = $color;

        return $this;
    }

    /**
     * @param  string | array<int | string, string> | Closure | null  $color
     */
    public function progressColor(string|array|Closure|null $color): static
    {
        return $this->progressBarColor($color);
    }

    /**
     * `true` shows the percentage under the bar; a string shows that text instead.
     */
    public function progressLabel(bool|string|Htmlable|Closure $label = true): static
    {
        $this->progressLabel = $label;

        return $this;
    }

    /**
     * `line` is core's sparkline. `bar` draws the same data as columns, with no JavaScript.
     */
    public function chartType(string|Closure $type): static
    {
        $this->chartType = $type;

        return $this;
    }

    /**
     * Describe the change from `$previous` to `$current`: "12.5% increase" with an arrow, green when it is good and
     * red when it is bad. Set `$higherIsBetter` to false for numbers that should go down (refunds, churn, errors).
     * An explicit description, description icon or description colour still wins.
     */
    public function trend(int|float|Closure|null $current, int|float|Closure|null $previous, bool $higherIsBetter = true, int $precision = 1): static
    {
        $this->trendCurrent = $current;
        $this->trendPrevious = $previous;
        $this->trendHigherIsBetter = $higherIsBetter;
        $this->trendPrecision = $precision;

        return $this;
    }

    /**
     * @param  string | array<int | string, string> | Closure | null  $color
     */
    public function badge(string|Htmlable|Closure|null $badge, string|array|Closure|null $color = null): static
    {
        $this->badge = $badge;
        $this->badgeColor = $color;

        return $this;
    }

    /**
     * @return string | array<int | string, string> | null
     */
    public function getBackgroundColor(): string|array|null
    {
        return $this->evaluate($this->backgroundColor);
    }

    /**
     * @return string | array<int | string, string> | null
     */
    public function getIconBackgroundColor(): string|array|null
    {
        return $this->evaluate($this->iconBackgroundColor);
    }

    /**
     * @return string | array<int | string, string> | null
     */
    public function getIconColor(): string|array|null
    {
        return $this->evaluate($this->iconColor) ?? $this->getIconBackgroundColor();
    }

    public function getIconPosition(): string
    {
        $position = $this->evaluate($this->iconPosition) ?? 'start';

        return match ($position) {
            'start', 'before' => 'start',
            'end', 'after' => 'end',
            default => throw new InvalidArgumentException("Icon position [{$position}] is not supported. Use [start] or [end]."),
        };
    }

    /**
     * @return string | array<int | string, string> | null
     */
    public function getLabelColor(): string|array|null
    {
        return $this->evaluate($this->labelColor);
    }

    /**
     * @return string | array<int | string, string> | null
     */
    public function getValueColor(): string|array|null
    {
        return $this->evaluate($this->valueColor);
    }

    public function hasProgress(): bool
    {
        return $this->evaluate($this->progress) !== null;
    }

    public function getProgressMax(): float
    {
        return (float) $this->evaluate($this->progressMax);
    }

    /**
     * The filled share of the bar, 0-100, or null when there is no progress bar.
     */
    public function getProgressPercent(): ?float
    {
        $value = $this->evaluate($this->progress);

        if ($value === null) {
            return null;
        }

        $max = $this->getProgressMax();

        if ($max <= 0) {
            return 0.0;
        }

        return round(max(0.0, min(100.0, ((float) $value / $max) * 100)), 2);
    }

    /**
     * @return string | array<int | string, string>
     */
    public function getProgressBarColor(): string|array
    {
        return $this->evaluate($this->progressBarColor) ?? $this->getColor() ?? 'primary';
    }

    public function getProgressLabel(): string|Htmlable|null
    {
        $label = $this->evaluate($this->progressLabel);

        if ($label === false || $label === null || $label === '') {
            return null;
        }

        if ($label === true) {
            $percent = $this->getProgressPercent() ?? 0.0;

            return __('filament-stat-cards::stat.progress', ['percent' => $this->formatNumber($percent, 0)]);
        }

        return $label;
    }

    public function getChartType(): string
    {
        $type = $this->evaluate($this->chartType);

        if (! in_array($type, ['line', 'bar'], true)) {
            throw new InvalidArgumentException("Chart type [{$type}] is not supported. Use [line] or [bar].");
        }

        return $type;
    }

    /**
     * Bar heights as percentages of the largest value (0-100), in chart order.
     *
     * @return array<int, float>
     */
    public function getChartBars(): array
    {
        $values = array_map(fn ($value): float => (float) $value, array_values($this->getChart() ?? []));

        if ($values === []) {
            return [];
        }

        $min = min(0.0, min($values));
        $range = max($values) - $min;

        return array_map(
            fn (float $value): float => $range > 0 ? round((($value - $min) / $range) * 100, 2) : 100.0,
            $values,
        );
    }

    /**
     * The percentage change from the previous value, or null when there is no trend or no previous value to compare.
     */
    public function getTrendChange(): ?float
    {
        $current = $this->evaluate($this->trendCurrent);
        $previous = $this->evaluate($this->trendPrevious);

        if ($current === null || $previous === null || (float) $previous == 0.0) {
            return null;
        }

        return round((((float) $current - (float) $previous) / abs((float) $previous)) * 100, $this->trendPrecision);
    }

    public function hasTrend(): bool
    {
        return $this->evaluate($this->trendCurrent) !== null && $this->evaluate($this->trendPrevious) !== null;
    }

    public function getDescription(): string|Htmlable|null
    {
        $description = parent::getDescription();

        if (filled($description) || ! $this->hasTrend()) {
            return $description;
        }

        $change = $this->getTrendChange();

        if ($change === null) {
            return (float) $this->evaluate($this->trendCurrent) == 0.0 ? __('filament-stat-cards::stat.trend.unchanged') : __('filament-stat-cards::stat.trend.new');
        }

        $percent = $this->formatNumber(abs($change), $this->trendPrecision);

        return match (true) {
            $change > 0 => __('filament-stat-cards::stat.trend.increase', ['percent' => $percent]),
            $change < 0 => __('filament-stat-cards::stat.trend.decrease', ['percent' => $percent]),
            default => __('filament-stat-cards::stat.trend.unchanged'),
        };
    }

    public function getDescriptionIcon(): string|BackedEnum|Htmlable|null
    {
        if (($icon = parent::getDescriptionIcon()) !== null) {
            return $icon;
        }

        $change = $this->getTrendChange();

        return match (true) {
            $change === null => null,
            $change > 0 => 'heroicon-m-arrow-trending-up',
            $change < 0 => 'heroicon-m-arrow-trending-down',
            default => null,
        };
    }

    /**
     * @return string | array<int | string, string> | null
     */
    public function getDescriptionColor(): string|array|null
    {
        if ($this->descriptionColor !== null) {
            return $this->descriptionColor;
        }

        $change = $this->getTrendChange();

        if ($change !== null && $change != 0.0) {
            return (($change > 0) === $this->trendHigherIsBetter) ? 'success' : 'danger';
        }

        return $this->getColor();
    }

    public function getBadge(): string|Htmlable|null
    {
        return $this->evaluate($this->badge);
    }

    /**
     * @return string | array<int | string, string>
     */
    public function getBadgeColor(): string|array
    {
        return $this->evaluate($this->badgeColor) ?? $this->getColor() ?? 'gray';
    }

    /**
     * The inline CSS custom properties for every colour slot the card uses: `--fsc-<slot>-<shade>`.
     * Named colours point at Filament's own `--<name>-<shade>` variables, so they follow the panel's palette;
     * arrays (`Color::Blue`, `Color::hex()`) are written out directly.
     */
    public function getColorStyle(): string
    {
        $slots = [
            'bg' => $this->getBackgroundColor(),
            'icon' => $this->getIconColor(),
            'tile' => $this->getIconBackgroundColor(),
            'label' => $this->getLabelColor(),
            'value' => $this->getValueColor(),
            'desc' => $this->getDescriptionColor(),
            'progress' => $this->hasProgress() ? $this->getProgressBarColor() : null,
            'chart' => ($this->getChart() !== null) ? ($this->getChartColor() ?? 'gray') : null,
            'badge' => filled($this->getBadge()) ? $this->getBadgeColor() : null,
        ];

        $declarations = [];

        foreach ($slots as $slot => $color) {
            foreach (static::colorVariables($color) as $shade => $value) {
                $declarations[] = "--fsc-{$slot}-{$shade}: {$value}";
            }
        }

        return implode('; ', $declarations);
    }

    /**
     * @param  string | array<int | string, string> | null  $color
     * @return array<int, string>
     */
    public static function colorVariables(string|array|null $color): array
    {
        if (blank($color)) {
            return [];
        }

        $variables = [];

        foreach (static::COLOR_SHADES as $shade) {
            if (is_array($color)) {
                $value = $color[$shade] ?? $color[(string) $shade] ?? null;

                // Only plain colour values make it into the style attribute.
                if (is_string($value) && preg_match('/^[#a-zA-Z0-9(),.%\s\/-]+$/', $value)) {
                    $variables[$shade] = $value;
                }

                continue;
            }

            $name = (string) preg_replace('/[^a-zA-Z0-9_-]/', '', $color);

            if ($name !== '') {
                $variables[$shade] = "var(--{$name}-{$shade})";
            }
        }

        return $variables;
    }

    /**
     * Space-separated slot names that have a colour, used as `fsc-has-<slot>` classes.
     *
     * @return array<string, bool>
     */
    public function getCardClasses(): array
    {
        $tile = filled($this->getIconBackgroundColor());

        return [
            'fsc-stat' => true,
            'fsc-has-bg' => filled($this->getBackgroundColor()),
            'fsc-has-tile' => $tile && filled($this->getIcon()),
            'fsc-has-icon-color' => filled($this->getIconColor()),
            'fsc-has-label-color' => filled($this->getLabelColor()),
            'fsc-has-value-color' => filled($this->getValueColor()),
            'fsc-has-desc-color' => filled($this->getDescriptionColor()),
            'fsc-has-chart' => $this->getChart() !== null,
            'fsc-icon-end' => filled($this->getIcon()) && $this->getIconPosition() === 'end',
        ];
    }

    protected function formatNumber(float $number, int $precision): string
    {
        $formatted = number_format($number, $precision, '.', '');

        return str_contains($formatted, '.') ? rtrim(rtrim($formatted, '0'), '.') : $formatted;
    }
}
