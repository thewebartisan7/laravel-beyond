<?php

namespace Regnerisch\LaravelBeyond\Commands;

use Illuminate\Console\Command;
use Regnerisch\LaravelBeyond\Resolvers\DomainNameSchemaResolver;

class MakePolicyCommand extends Command
{
    protected $signature = 'beyond:make:policy {name} {--model=}';

    protected $description = 'Make a new policy';

    public function handle(): void
    {
        try {
            $name = $this->argument('name');
            $model = $this->option('model');

            $schema = new DomainNameSchemaResolver($name);

            if ($model) {
                $stub = 'policy.stub';
                $replacements = [
                    '{{ domain }}' => $schema->getDomainName(),
                    '{{ className }}' => $schema->getClassName(),
                    '{{ modelName }}' => $model,
                    '{{ modelVariable }}' => mb_strtolower($model),
                ];
            } else {
                $stub = 'policy.plain.stub';
                $replacements = [
                    '{{ domain }}' => $schema->getDomainName(),
                    '{{ className }}' => $schema->getClassName(),
                ];
            }

            beyond_copy_stub(
                $stub,
                base_path() . '/src/Domain/' . $schema->getPath('Policies') . '.php',
                $replacements
            );
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}
