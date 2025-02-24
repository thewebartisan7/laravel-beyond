<?php

namespace Regnerisch\LaravelBeyond\Commands;

use Regnerisch\LaravelBeyond\Resolvers\DomainNameSchemaResolver;

class MakeBuilderCommand extends BaseCommand
{
    protected $signature = 'beyond:make:builder {name?} {--overwrite}';

    protected $description = 'Make a new eloquent builder';

    public function handle(): void
    {
        try {
            $name = $this->argument('name');
            $overwrite = $this->option('overwrite');

            $schema = (new DomainNameSchemaResolver($this, $name))->handle();

            beyond_copy_stub(
                'builder.stub',
                $schema->path('Builders'),
                [
                    '{{ namespace }}' => $schema->namespace(),
                    '{{ className }}' => $schema->className(),
                ],
                $overwrite
            );

            $this->info(
                'Please add following code to your related model' . PHP_EOL . PHP_EOL .

                'public function newEloquentBuilder($query)' . PHP_EOL .
                '{' . PHP_EOL .
                "\t" . 'return new ' . $schema->className() . '($query); ' . PHP_EOL .
                '}'
            );

            $this->components->info('Builder created.');
        } catch (\Exception $exception) {
            $this->components->error($exception->getMessage());
        }
    }
}
