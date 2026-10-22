<?php

declare(strict_types=1);

use AmjadIqbal\FilamentStatCards\FilamentStatCardsServiceProvider;
use AmjadIqbal\FilamentStatCards\Tests\Fixtures\EscapingWidget;
use AmjadIqbal\FilamentStatCards\Tests\Fixtures\StatCardsWidget;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Livewire\Livewire;

beforeEach(function (): void {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

it('renders stat cards inside a core stats overview widget', function (): void {
    Livewire::test(StatCardsWidget::class)
        ->assertOk()
        ->assertSeeText(['Revenue', '$48,200', 'Refunds', 'Signups', 'Core stat'])
        ->assertSeeHtml('fsc-stat')
        ->assertSeeHtml('fsc-icon-tile fsc-icon-tile-end')
        ->assertSeeHtml('fsc-has-bg');
});

it('renders an accessible progress bar with its label', function (): void {
    Livewire::test(StatCardsWidget::class)
        ->assertSeeHtml('role="progressbar"')
        ->assertSeeHtml('aria-valuenow="72"')
        ->assertSeeHtml('aria-label="Revenue"')
        ->assertSeeHtml('style="width: 72%"')
        ->assertSeeText('72%');
});

it('renders trends, badges and bar charts', function (): void {
    Livewire::test(StatCardsWidget::class)
        ->assertSeeText(['12.4% increase', '22.5% decrease', 'Live', 'This week'])
        ->assertSeeHtml('--fsc-desc-600: var(--success-600)')
        ->assertSeeHtml('--fsc-badge-600: var(--success-600)')
        ->assertSeeHtml('class="fsc-bars"')
        ->assertSeeHtml('<rect x="51.5" y="0" width="7" height="100" rx="1.5"></rect>');
});

it('keeps core sparklines and core stats working', function (): void {
    Livewire::test(StatCardsWidget::class)
        ->assertSeeHtml('statsOverviewStatChart(')
        ->assertSeeHtml('fi-wi-stats-overview-stat-chart');

    $html = Livewire::test(StatCardsWidget::class)->html();

    // The core stat keeps core's own markup (its label container) and none of ours.
    expect(substr_count($html, 'fi-wi-stats-overview-stat-label-ctn'))->toBe(1)
        ->and(substr_count($html, 'fi-wi-stats-overview-stat '))->toBeGreaterThanOrEqual(1);
});

it('escapes user text in every slot', function (): void {
    $html = Livewire::test(EscapingWidget::class)->html();

    expect($html)->not->toContain('<script>value</script>')
        ->and($html)->not->toContain('<b>label</b>')
        ->and($html)->not->toContain('<i>desc</i>')
        ->and($html)->not->toContain('<u>badge</u>')
        ->and($html)->not->toContain('<s>prog</s>')
        ->and($html)->toContain('&lt;script&gt;value&lt;/script&gt;');
});

it('inlines the stylesheet when the assets were never published', function (): void {
    $path = FilamentStatCardsServiceProvider::stylesheet()->package(FilamentStatCardsServiceProvider::ASSET_PACKAGE)->getPublicPath();
    @unlink($path);

    $html = FilamentView::renderHook(PanelsRenderHook::STYLES_AFTER)->toHtml();

    expect($html)->toContain('<style data-filament-stat-cards>')
        ->and($html)->toContain('.fsc-stat .fsc-progress-bar');
});

it('links the published stylesheet after filament:assets', function (): void {
    $path = FilamentStatCardsServiceProvider::stylesheet()->package(FilamentStatCardsServiceProvider::ASSET_PACKAGE)->getPublicPath();

    $this->artisan('filament:assets')->assertSuccessful();

    try {
        expect(is_file($path))->toBeTrue();

        $html = FilamentView::renderHook(PanelsRenderHook::STYLES_AFTER)->toHtml();

        expect($html)->toContain('css/amjadiqbal/filament-stat-cards/filament-stat-cards.css')
            ->and($html)->not->toContain('<style data-filament-stat-cards>');
    } finally {
        @unlink($path);
    }
});

it('does not print the stylesheet a second time through Filament\'s own styles', function (): void {
    $styles = (string) FilamentAsset::renderStyles();

    expect($styles)->not->toContain('filament-stat-cards.css');
});
