<?php
/**
 * List of product categories and subcategories used in the theme.
 * This file replaces WooCommerce categories.
 */

defined( 'ABSPATH' ) || exit;

$mytheme_categories = [
    'audio' => [
        'label' => 'AUDIO EQUIPMENT',
        'children' => [
            'complete-sound-systems'    => 'Complete Sound Systems',
            'line-arrays'               => 'Line Arrays',
            'subwoofers'                => 'Subwoofers',
            'conventional-loudspeakers' => 'Conventional Loudspeakers',
            'analogue-audio-mixers'     => 'Analogue Audio Mixers',
            'digital-audio-mixers'      => 'Digital Audio Mixers',
            'audio-amplifiers'          => 'Audio Amplifiers',
            'outboards'                 => 'Outboards',
        ],
    ],
    'lighting' => [
        'label' => 'LIGHTING',
        'children' => [
            'lighting-desks'              => 'Lighting Desks',
            'spot-moving-heads'           => 'Spot Moving Heads',
            'wash-moving-heads'           => 'Wash Moving Heads',
            'hybrid-moving-heads'         => 'Hybrid Moving Heads',
            'follow-spots'                => 'Follow Spots',
            'tv-lighting-fixtures'        => 'TV Lighting Fixtures',
            'dimmers'                     => 'Dimmers',
            'theatrical-lighting-fixtures'=> 'Theatrical Lighting Fixtures',
        ],
    ],
    'visual' => [
        'label' => 'VISUAL',
        'children' => [
            'led-wall-screens'      => 'LED Wall Screens',
            'video-data-projectors' => 'Video Data Projectors',
            'projector-lenses'      => 'Projector Lenses',
            'cameras-camcorders'    => 'Cameras - Camcorders',
            'vision-mixers'         => 'Vision mixers',
            'signal-processing-units'=> 'Signal Processing Units',
            'vision-monitors'       => 'Vision Monitors',
            'projection-screens'    => 'Projection Screens',
        ],
    ],
    'rigging' => [
        'label' => 'RIGGING - POWER DISTRIBUTION',
        'children' => [
            'electrical-hoists' => 'Electrical Hoists',
            'manual-hoists'     => 'Manual Hoists',
            'hoists-controllers'=> 'Hoists Controllers',
            'power-boards'      => 'Power Boards',
        ],
    ],
    'staging' => [
        'label' => 'STAGING - TRUSSING',
        'children' => [
            'ground-supports' => 'Ground Supports',
            'truss'           => 'Truss',
            'platforms'       => 'Platforms',
            'barriers'        => 'Barriers',
            'accessories'     => 'Accessories',
        ],
    ],
];
