<?php
/**
 * This file is part of Notadd.
 * @author TwilRoad <269044570@qq.com>
 * @copyright (c) 2015, iBenchu.org
 * @datetime 2015-12-01 18:04
 */
namespace Notadd\Foundation\Queue\Console;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Notadd\Foundation\Composer\Composer;
use Notadd\Foundation\Console\Command;
/**
 * Class TableCommand
 * @package Notadd\Foundation\Queue\Console
 */
class TableCommand extends Command {
    /**
     * @var string
     */
    protected $name = 'queue:table';
    /**
     * @var string
     */
    protected $description = 'Create a migration for the queue jobs database table';
    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;
    /**
     * @var \Notadd\Foundation\Composer\Composer
     */
    protected $composer;
    /**
     * TableCommand constructor.
     * @param \Illuminate\Filesystem\Filesystem $files
     * @param \Notadd\Foundation\Composer\Composer $composer
     */
    public function __construct(Filesystem $files, Composer $composer) {
        parent::__construct();
        $this->files = $files;
        $this->composer = $composer;
    }
    /**
     * @return void
     */
    public function fire() {
        $table = $this->notadd['config']['queue.connections.database.table'];
        $tableClassName = Str::studly($table);
        $fullPath = $this->createBaseMigration($table);
        $stub = str_replace([
            '{{table}}',
            '{{tableClassName}}'
        ], [
            $table,
            $tableClassName
        ], $this->files->get(__DIR__ . '/stubs/jobs.stub'));
        $this->files->put($fullPath, $stub);
        $this->info('Migration created successfully!');
        $this->composer->dumpAutoloads();
    }
    /**
     * @param string $table
     * @return string
     */
    protected function createBaseMigration($table = 'jobs') {
        $name = 'create_' . $table . '_table';
        $path = $this->notadd->frameworkPath() . '/migrations';
        return $this->notadd['migration.creator']->create($name, $path);
    }
}