<?php

namespace Regnerisch\LaravelBeyond\Commands;

use Regnerisch\LaravelBeyond\Resolvers\DomainNameSchemaResolver;

class MakeEventCommand extends BaseCommand
{
    protected $signature = 'beyond:make:event {name?} {--overwrite}';

    protected $description = 'Make a new event';

    public function handle(): void
    {
        try {
            $name = $this->argument('name');
            $overwrite = $this->option('overwrite');

            $schema = (new DomainNameSchemaResolver($this, $name))->handle();

            beyond_copy_stub(
                'event.stub',
                $schema->path('Events'),
                [
                    '{{ namespace }}' => $schema->namespace(),
                    '{{ className }}' => $schema->className(),
                ],
                $overwrite
            );

            $this->components->info('Event created.');
        } catch (\Exception $exception) {
            $this->components->error($exception->getMessage());
        }
    }
}
