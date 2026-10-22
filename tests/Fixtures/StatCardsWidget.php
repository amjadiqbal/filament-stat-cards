<?php

declare(strict_types=1);

namespace AmjadIqbal\FilamentStatCards\Tests\Fixtures;

use AmjadIqbal\FilamentStatCards\Stat;
use Filament\Support\Colors\Color;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat as CoreStat;

class StatCardsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Revenue', '$48,200')
                ->icon('heroicon-o-banknotes')
                ->iconBackgroundColor('primary')
                ->iconPosition('end')
                ->trend(48200, 42880)
                ->progress(72)
                ->progressLabel()
                ->badge('Live', 'success'),
            Stat::make('Refunds', '31')
                ->icon('heroicon-o-receipt-refund')
                ->iconColor('danger')
                ->trend(31, 40, higherIsBetter: false)
                ->chart([4, 9, 6, 12, 8, 14])
                ->chartType('bar')
                ->chartColor('danger'),
            Stat::make('Signups', '1,204')
                ->backgroundColor(Color::Sky)
                ->valueColor('info')
                ->description('This week')
                ->chart([1, 3, 2, 5, 4, 6]),
            CoreStat::make('Core stat', '12'),
        ];
    }
}
