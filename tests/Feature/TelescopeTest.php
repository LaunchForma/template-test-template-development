<?php

it('telescope config file exists', function () {
    expect(config_path('telescope.php'))->toBeFile();
});

it('telescope migrations are in shared directory', function () {
    $migrations = glob(database_path('migrations/shared/*telescope*'));
    expect($migrations)->not->toBeEmpty();
});
