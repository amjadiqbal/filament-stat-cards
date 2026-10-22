<?php

declare(strict_types=1);

namespace AmjadIqbal\FilamentStatCards\Tests\Fixtures;

use Filament\Panel;
use Filament\PanelProvider;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->widgets([StatCardsWidget::class]);
    }
}
