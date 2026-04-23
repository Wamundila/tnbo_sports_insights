<?php

return [
    'api_key' => env('INSIGHTS_API_KEY'),
    'api_key_header' => env('INSIGHTS_API_KEY_HEADER', 'X-API-Key'),
    'raw_event_retention_days' => (int) env('INSIGHTS_RAW_EVENT_RETENTION_DAYS', 90),
    'reporting_timezone' => env('INSIGHTS_REPORTING_TIMEZONE', config('app.timezone', 'UTC')),

    'placement_block_types' => [
        'sponsor_card' => 'Sponsor Card',
        'image_banner' => 'Image Banner',
        'image_strip' => 'Image Strip',
        'sponsored_tile' => 'Sponsored Tile',
        'audio_companion' => 'Audio Companion',
    ],

    'creative_types' => [
        'image_banner' => 'Image Banner',
        'image_strip' => 'Image Strip',
        'sponsor_card' => 'Sponsor Card',
        'sponsored_tile' => 'Sponsored Tile',
        'audio_companion' => 'Audio Companion',
    ],

    'creatable_creative_types' => [
        'image_banner' => 'Image Banner',
        'image_strip' => 'Image Strip',
    ],

    'allowed_services' => [
        'news',
        'match_center',
        'media',
        'interactive',
        'insights',
        'sponsors',
    ],

    'allowed_platforms' => [
        'android',
        'ios',
        'web',
    ],

    'starter_placements' => [
        [
            'code' => 'home_inline_1',
            'name' => 'Home Inline 1',
            'service' => 'news',
            'surface' => 'home_page',
            'block_type' => 'sponsor_card',
            'allowed_creative_types' => ['image_banner', 'sponsor_card', 'sponsored_tile'],
            'position_hint' => 'inline_1',
            'description' => 'Primary inline sponsor slot on the TNBO home page.',
        ],
        [
            'code' => 'article_inline_1',
            'name' => 'Article Inline 1',
            'service' => 'news',
            'surface' => 'article_page',
            'block_type' => 'sponsor_card',
            'allowed_creative_types' => ['image_banner', 'sponsor_card'],
            'position_hint' => 'inline_1',
            'description' => 'First sponsor slot inside article detail experiences.',
        ],
        [
            'code' => 'games_inline_1',
            'name' => 'Games Inline 1',
            'service' => 'interactive',
            'surface' => 'games_page',
            'block_type' => 'sponsor_card',
            'allowed_creative_types' => ['sponsor_card', 'sponsored_tile'],
            'position_hint' => 'inline_1',
            'description' => 'Inline games and engagement placement.',
        ],
        [
            'code' => 'watch_inline_1',
            'name' => 'Watch Inline 1',
            'service' => 'media',
            'surface' => 'watch_page',
            'block_type' => 'image_banner',
            'allowed_creative_types' => ['image_banner', 'sponsor_card'],
            'position_hint' => 'inline_1',
            'description' => 'Starter watch surface sponsorship placement.',
        ],
        [
            'code' => 'match_center_header_companion',
            'name' => 'Match Center Header Companion',
            'service' => 'match_center',
            'surface' => 'match_center_page',
            'block_type' => 'sponsor_card',
            'allowed_creative_types' => ['image_banner', 'sponsor_card', 'audio_companion'],
            'position_hint' => 'header_companion',
            'description' => 'Header companion sponsor slot for match center surfaces.',
        ],
    ],
];
