<?php

it('media library config file exists', function () {
    expect(config_path('media-library.php'))->toBeFile();
});

it('media library migration is in shared directory', function () {
    $migrations = glob(database_path('migrations/shared/*media*'));
    expect($migrations)->not->toBeEmpty();
});

it('temporary upload model config is null since pro is not installed', function () {
    expect(config('media-library.temporary_upload_model'))->toBeNull();
});
