<?php

namespace Regnerisch\LaravelBeyond\Actions;

use Illuminate\Filesystem\Filesystem;

class CopyAndRefactorDirectoryAction
{
    public function __construct(
        protected CopyAndRefactorFileAction $copyAndRefactorFileAction,
    ) {
    }

    public function execute(string $sourcePath, string $targetPath, array $refactor = [], bool $overwrite = false): void
    {
        $fs = new Filesystem();
        $files = $fs->files($sourcePath);

        foreach ($files as $file) {
            $this->copyAndRefactorFileAction->execute(
                $sourcePath . '/' . $file->getFilename(),
                $targetPath . '/' . $file->getFilename(),
                $refactor,
                $overwrite
            );
        }
    }
}
