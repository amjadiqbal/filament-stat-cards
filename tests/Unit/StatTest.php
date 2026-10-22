<?php

declare(strict_types=1);

use AmjadIqbal\FilamentStatCards\Stat;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Stat as CoreStat;

it('is a core stat, so it fits any StatsOverviewWidget', function (): void {
    expect(Stat::make('Users', 10))->toBeInstanceOf(CoreStat::class);
});

it('clamps progress to 0-100% of the maximum', function (int|float|null $value, int|float $max, ?float $percent): void {
    expect(Stat::make('X', 1)->progress($value, $max)->getProgressPercent())->toBe($percent);
})->with([
    'half' => [50, 100, 50.0],
    'custom max' => [30, 120, 25.0],
    'over' => [150, 100, 100.0],
    'negative' => [-5, 100, 0.0],
    'zero max' => [5, 0, 0.0],
    'none' => [null, 100, null],
]);

it('evaluates closures for progress', function (): void {
    $stat = Stat::make('X', 1)->progress(fn (): int => 3, fn (): int => 4);

    expect($stat->getProgressPercent())->toBe(75.0)
        ->and($stat->hasProgress())->toBeTrue();
});

it('labels the progress bar with the percentage or custom text', function (): void {
    expect(Stat::make('X', 1)->progress(72.4)->getProgressLabel())->toBeNull()
        ->and(Stat::make('X', 1)->progress(72.4)->progressLabel()->getProgressLabel())->toBe('72%')
        ->and(Stat::make('X', 1)->progress(3, 10)->progressLabel('3 of 10 seats')->getProgressLabel())->toBe('3 of 10 seats');
});

it('defaults the progress colour to the stat colour, then primary', function (): void {
    expect(Stat::make('X', 1)->progress(1)->getProgressBarColor())->toBe('primary')
        ->and(Stat::make('X', 1)->color('success')->progress(1)->getProgressBarColor())->toBe('success')
        ->and(Stat::make('X', 1)->color('success')->progressColor('info')->getProgressBarColor())->toBe('info');
});

it('describes a trend, coloured by whether higher is better', function (): void {
    $up = Stat::make('Revenue', 1)->trend(110, 100);
    $down = Stat::make('Revenue', 1)->trend(80, 100);
    $refunds = Stat::make('Refunds', 1)->trend(80, 100, higherIsBetter: false);

    expect($up->getTrendChange())->toBe(10.0)
        ->and($up->getDescription())->toBe('10% increase')
        ->and($up->getDescriptionIcon())->toBe('heroicon-m-arrow-trending-up')
        ->and($up->getDescriptionColor())->toBe('success')
        ->and($down->getDescription())->toBe('20% decrease')
        ->and($down->getDescriptionIcon())->toBe('heroicon-m-arrow-trending-down')
        ->and($down->getDescriptionColor())->toBe('danger')
        ->and($refunds->getDescriptionColor())->toBe('success');
});

it('handles flat, new and fractional trends', function (): void {
    expect(Stat::make('X', 1)->trend(100, 100)->getDescription())->toBe('No change')
        ->and(Stat::make('X', 1)->trend(100, 100)->getDescriptionIcon())->toBeNull()
        ->and(Stat::make('X', 1)->trend(5, 0)->getDescription())->toBe('New this period')
        ->and(Stat::make('X', 1)->trend(0, 0)->getDescription())->toBe('No change')
        ->and(Stat::make('X', 1)->trend(1, 3, precision: 2)->getDescription())->toBe('66.67% decrease')
        ->and(Stat::make('X', 1)->trend(-50, -100)->getDescription())->toBe('50% increase');
});

it('lets an explicit description, icon and colour win over the trend', function (): void {
    $stat = Stat::make('X', 1)
        ->trend(110, 100)
        ->description('Custom')
        ->descriptionIcon('heroicon-m-star')
        ->descriptionColor('gray');

    expect($stat->getDescription())->toBe('Custom')
        ->and($stat->getDescriptionIcon())->toBe('heroicon-m-star')
        ->and($stat->getDescriptionColor())->toBe('gray');
});

it('accepts start/end, before/after and IconPosition', function (): void {
    expect(Stat::make('X', 1)->getIconPosition())->toBe('start')
        ->and(Stat::make('X', 1)->iconPosition('end')->getIconPosition())->toBe('end')
        ->and(Stat::make('X', 1)->iconPosition('before')->getIconPosition())->toBe('start')
        ->and(Stat::make('X', 1)->iconPosition(IconPosition::After)->getIconPosition())->toBe('end');
});

it('rejects an unknown icon position or chart type', function (): void {
    expect(fn () => Stat::make('X', 1)->iconPosition('middle')->getIconPosition())->toThrow(InvalidArgumentException::class)
        ->and(fn () => Stat::make('X', 1)->chartType('pie')->getChartType())->toThrow(InvalidArgumentException::class);
});

it('sets label, value and description colours with textColor()', function (): void {
    $stat = Stat::make('X', 1)->textColor('gray', 'success', 'info');

    expect($stat->getLabelColor())->toBe('gray')
        ->and($stat->getValueColor())->toBe('success')
        ->and($stat->getDescriptionColor())->toBe('info');
});

it('uses the tile colour for the icon unless an icon colour is set', function (): void {
    expect(Stat::make('X', 1)->iconBackgroundColor('success')->getIconColor())->toBe('success')
        ->and(Stat::make('X', 1)->iconBackgroundColor('success')->iconColor('info')->getIconColor())->toBe('info');
});

it('scales bar heights to the largest value', function (): void {
    expect(Stat::make('X', 1)->chart([2, 4, 8])->getChartBars())->toBe([25.0, 50.0, 100.0])
        ->and(Stat::make('X', 1)->chart([5, 5])->getChartBars())->toBe([100.0, 100.0])
        ->and(Stat::make('X', 1)->chart([-2, 0, 2])->getChartBars())->toBe([0.0, 50.0, 100.0])
        ->and(Stat::make('X', 1)->getChartBars())->toBe([]);
});

it('maps named colours to Filament variables and writes arrays out', function (): void {
    expect(Stat::colorVariables('success'))->toHaveCount(7)
        ->and(Stat::colorVariables('success')[500])->toBe('var(--success-500)')
        ->and(Stat::colorVariables(Color::Sky)[500])->toBe(Color::Sky[500])
        ->and(Stat::colorVariables(null))->toBe([])
        ->and(Stat::colorVariables('x"; background: url(evil)'))->toHaveKey(500)
        ->and(Stat::colorVariables('x"; background: url(evil)')[500])->toBe('var(--xbackgroundurlevil-500)')
        ->and(Stat::colorVariables([500 => 'red; background: url(x)']))->toBe([]);
});

it('emits CSS variables only for the slots in use', function (): void {
    $style = Stat::make('X', 1)->backgroundColor('info')->progress(10)->getColorStyle();

    expect($style)->toContain('--fsc-bg-50: var(--info-50)')
        ->and($style)->toContain('--fsc-progress-500: var(--primary-500)')
        ->and($style)->not->toContain('--fsc-badge')
        ->and(Stat::make('X', 1)->getColorStyle())->toBe('');
});
