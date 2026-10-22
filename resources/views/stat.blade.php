@php
    use Filament\Support\Enums\IconPosition;
    use Filament\Support\Facades\FilamentAsset;
    use Filament\Support\View\ComponentAttributeBag as FilamentComponentAttributeBag;
    use Filament\Widgets\View\Components\StatsOverviewWidgetComponent\StatComponent\StatsOverviewWidgetStatChartComponent;

    $icon = $getIcon();
    $iconPosition = $getIconPosition();
    $hasTile = filled($icon) && filled($getIconBackgroundColor());
    $badge = $getBadge();
    $descriptionIcon = $getDescriptionIcon();
    $descriptionIconPosition = $getDescriptionIconPosition();
    $progressPercent = $getProgressPercent();
    $progressLabel = $getProgressLabel();
    $chart = $getChart();
    $chartType = $chart ? $getChartType() : null;
    $url = $getUrl();
    $tag = $url ? 'a' : 'div';
    $colorStyle = $getColorStyle();

    $iconHtml = filled($icon)
        ? \Filament\Support\generate_icon_html($icon, attributes: (new FilamentComponentAttributeBag)->class(['fsc-icon']))
        : null;
@endphp

<{!! $tag !!}
    @if ($url)
        {{ \Filament\Support\generate_href_html($url, $shouldOpenUrlInNewTab()) }}
    @endif
    {{
        $getExtraAttributeBag()
            ->class([
                'fi-wi-stats-overview-stat',
                ...array_keys(array_filter($getCardClasses())),
            ])
            ->style(filled($colorStyle) ? [$colorStyle] : [])
    }}
>
    <div class="fi-wi-stats-overview-stat-content fsc-content">
        <div class="fsc-head">
            @if ($iconHtml && ($iconPosition === 'start'))
                @if ($hasTile)
                    <span class="fsc-icon-tile" aria-hidden="true">{{ $iconHtml }}</span>
                @else
                    {{ $iconHtml }}
                @endif
            @endif

            <span class="fi-wi-stats-overview-stat-label fsc-label">
                {{ $getLabel() }}
            </span>

            @if (filled($badge))
                <span class="fsc-badge">{{ $badge }}</span>
            @endif

            @if ($iconHtml && ($iconPosition === 'end'))
                @if ($hasTile)
                    <span class="fsc-icon-tile fsc-icon-tile-end" aria-hidden="true">{{ $iconHtml }}</span>
                @else
                    <span class="fsc-icon-end-wrap">{{ $iconHtml }}</span>
                @endif
            @endif
        </div>

        @if (filled($value = $getValue()))
            <div class="fi-wi-stats-overview-stat-value fsc-value">
                {{ $value }}
            </div>
        @else
            <div class="fi-wi-stats-overview-stat-placeholder">
                {{ $getPlaceholder() }}
            </div>
        @endif

        @if (filled($description = $getDescription()))
            <div class="fsc-description">
                @if ($descriptionIcon && in_array($descriptionIconPosition, [IconPosition::Before, 'before'], true))
                    {{ \Filament\Support\generate_icon_html($descriptionIcon, attributes: (new FilamentComponentAttributeBag)->class(['fsc-description-icon'])) }}
                @endif

                <span>{{ $description }}</span>

                @if ($descriptionIcon && in_array($descriptionIconPosition, [IconPosition::After, 'after'], true))
                    {{ \Filament\Support\generate_icon_html($descriptionIcon, attributes: (new FilamentComponentAttributeBag)->class(['fsc-description-icon'])) }}
                @endif
            </div>
        @endif

        @if ($progressPercent !== null)
            <div class="fsc-progress">
                <div
                    class="fsc-progress-track"
                    role="progressbar"
                    aria-valuemin="0"
                    aria-valuemax="100"
                    aria-valuenow="{{ $progressPercent }}"
                    aria-label="{{ strip_tags((string) $getLabel()) }}"
                >
                    <div class="fsc-progress-bar" style="width: {{ $progressPercent }}%"></div>
                </div>

                @if (filled($progressLabel))
                    <div class="fsc-progress-label">{{ $progressLabel }}</div>
                @endif
            </div>
        @endif
    </div>

    @if ($chart && ($chartType === 'bar'))
        @php
            $bars = $getChartBars();
            $count = max(count($bars), 1);
        @endphp

        {{-- The label and value are already exposed as text, so the bars are decorative. --}}
        <svg class="fsc-bars" viewBox="0 0 {{ $count * 10 }} 100" preserveAspectRatio="none" aria-hidden="true" focusable="false">
            @foreach ($bars as $index => $height)
                <rect x="{{ $index * 10 + 1.5 }}" y="{{ 100 - max($height, 4) }}" width="7" height="{{ max($height, 4) }}" rx="1.5"></rect>
            @endforeach
        </svg>
    @elseif ($chart)
        {{-- Core's sparkline, unchanged. --}}
        <div x-data="{ statsOverviewStatChart() {} }">
            <div
                x-load
                x-load-src="{{ FilamentAsset::getAlpineComponentSrc('stats-overview/stat/chart', 'filament/widgets') }}"
                wire:ignore
                x-data="statsOverviewStatChart({
                            key: @js($getKey(false)),
                            labels: @js(array_keys($chart)),
                            values: @js(array_values($chart)),
                        })"
                {{ (new FilamentComponentAttributeBag)->color(StatsOverviewWidgetStatChartComponent::class, $getChartColor() ?? 'gray')->class(['fi-wi-stats-overview-stat-chart']) }}
            >
                <canvas x-ref="canvas" aria-hidden="true"></canvas>

                <span aria-hidden="true" x-ref="backgroundColorElement" class="fi-wi-stats-overview-stat-chart-bg-color"></span>

                <span aria-hidden="true" x-ref="borderColorElement" class="fi-wi-stats-overview-stat-chart-border-color"></span>
            </div>
        </div>
    @endif
</{!! $tag !!}>
