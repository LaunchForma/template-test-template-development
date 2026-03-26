<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeSharedSeeder extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:shared-seeder {name : The name of the seeder class}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new shared seeder class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Seeder';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->resolveStubPath('/stubs/seeder.stub');
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__.'/../../../../laravel/framework/src/Illuminate/Database/Console/Seeds'.$stub;
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = str_replace(
            ['Database\\Seeders\\Shared\\', 'Database\\Seeders\\Shared'],
            '',
            $name
        );

        return $this->laravel->databasePath().'/seeders/shared/'.str_replace('\\', '/', $name).'.php';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return 'Database\\Seeders\\Shared';
    }

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        return 'Database\\Seeders\\Shared';
    }
}
