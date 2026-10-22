<?php

declare(strict_types=1);

namespace AmjadIqbal\FilamentStatCards\Tests\Fixtures;

use AmjadIqbal\FilamentStatCards\Stat;
use Filament\Widgets\StatsOverviewWidget;

class EscapingWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('<b>label</b>', '<script>value</script>')
                ->icon('heroicon-o-user')
                ->iconBackgroundColor('primary')
                ->description('<i>desc</i>')
                ->badge('<u>badge</u>')
                ->progress(1)
                ->progressLabel('<s>prog</s>'),
        ];
    }
}
