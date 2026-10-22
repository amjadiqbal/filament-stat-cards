<?php

declare(strict_types=1);

namespace AmjadIqbal\FilamentStatCards;

use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\HtmlString;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentStatCardsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-stat-cards';

    public const ASSET_PACKAGE = 'amjadiqbal/filament-stat-cards';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasViews()
            ->hasTranslations();
    }

    public function packageBooted(): void
    {
        $css = static::stylesheet();

        // Registered so `php artisan filament:assets` publishes it, but printed by the hook below rather than by
        // Filament, so a panel whose assets were never published still gets the styles.
        FilamentAsset::register([$css], static::ASSET_PACKAGE);

        FilamentView::registerRenderHook(PanelsRenderHook::STYLES_AFTER, fn (): HtmlString => static::styles());
    }

    public static function stylesheet(): Css
    {
        return Css::make(static::$name, static::stylesheetPath())->loadedOnRequest();
    }

    public static function stylesheetPath(): string
    {
        return __DIR__.'/../resources/dist/filament-stat-cards.css';
    }

    /**
     * A `<link>` to the published stylesheet when it is there, else the same CSS inline (about 5 KB).
     */
    public static function styles(): HtmlString
    {
        $css = static::stylesheet()->package(static::ASSET_PACKAGE);

        if (is_file($css->getPublicPath())) {
            return new HtmlString($css->getHtml()->toHtml());
        }

        return new HtmlString('<style data-filament-stat-cards>'.file_get_contents(static::stylesheetPath()).'</style>');
    }
}
