<?php

return [
    'templates' => [
        'test-template-development' => [
            'name' => 'Test Template Development',
            'entry_route' => 'test-template-development.home',
            'user_traits' => ['HasTestTemplateDevelopment'],
            'user_fields' => [
                'fillable' => [
                    'is_test_template',
                    'is_developer',
                    'test_array',
                ],
                'casts' => [
                    'is_test_template' => 'boolean',
                    'is_developer' => 'boolean',
                    'test_array' => 'array',
                ],
            ],
            'migrations' => ['test-template-development'],
            'seeders' => ['TestTemplateDevelopmentSeeder'],
        ],
    ],
];
