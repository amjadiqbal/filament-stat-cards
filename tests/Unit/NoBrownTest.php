<?php

declare(strict_types=1);

// Amjad's rule: no brown in any UI. A translucent amber, orange or red fill on a dark surface reads as brown, and so
// do the 700-950 shades of the warm palettes. The stylesheet only uses shades up to 600 and no translucent colour.

function statCardsCss(): string
{
    // Comments are stripped: they explain the rule and name the colours it bans.
    return (string) preg_replace('#/\*.*?\*/#s', '', (string) file_get_contents(__DIR__.'/../../resources/dist/filament-stat-cards.css'));
}

it('uses no shade above 600 and no translucent colour in the stylesheet', function (): void {
    $css = statCardsCss();

    preg_match_all('/var\(--fsc-[a-z]+-(\d+)/', $css, $slotShades);
    expect(array_map('intval', $slotShades[1]))->each->toBeLessThanOrEqual(600);

    expect($css)
        ->not->toMatch('/var\(--fsc-[a-z]+-\d+\)\s*\/\s*0?\./')
        ->not->toMatch('/color-mix\(/')
        ->not->toMatch('/\b(rgba|hsla)\(/')
        ->not->toMatch('/opacity\s*:/')
        ->not->toMatch('/--(warning|danger|primary|amber|orange|yellow|red)-(7|8|9)\d\d/')
        ->not->toMatch('/\b(brown|tan|maroon|sienna|chocolate)\b/i');
});

it('fills dark-mode surfaces only with neutral grays or solid colours', function (): void {
    $css = statCardsCss();

    preg_match_all('/:is\(\.dark[^{]*\{([^}]*)\}/', $css, $blocks);

    foreach ($blocks[1] as $block) {
        if (preg_match('/background-color:\s*([^;]+);/', $block, $match)) {
            expect(trim($match[1]))->toMatch('/^(transparent|var\(--gray-(800|900)\)|var\(--fsc-[a-z]+-[1-6]00\))$/');
        }
    }

    expect($blocks[1])->not->toBeEmpty();
});
