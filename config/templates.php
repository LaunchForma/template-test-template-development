<?php

return [
    'templates' => [
        'test-template-development' => array (
  'name' => 'Test Template Development',
  'entry_route' => 'test-template-development.home',
  'railway_template_id' => 'forma-test-template-development',
  'user_traits' => 
  array (
    0 => 'HasTestTemplateDevelopment',
  ),
  'user_fields' => 
  array (
    'fillable' => 
    array (
      0 => 'is_test_template',
      1 => 'is_developer',
      2 => 'test_array',
    ),
    'casts' => 
    array (
      'is_test_template' => 'boolean',
      'is_developer' => 'boolean',
      'test_array' => 'array',
    ),
  ),
  'migrations' => 
  array (
    0 => 'test-template-development',
  ),
  'seeders' => 
  array (
    0 => 'TestTemplateDevelopmentSeeder',
  ),
),
    ],
];
