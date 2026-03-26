<?php

it('settings config file exists', function () {
    expect(config_path('settings.php'))->toBeFile();
});

it('settings migration is in shared directory', function () {
    $migrations = glob(database_path('migrations/shared/*setting*'));
    expect($migrations)->not->toBeEmpty();
});

it('shared settings directory exists', function () {
    expect(app_path('Shared/Settings'))->toBeDirectory();
});
