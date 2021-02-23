<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\Facades\Storage;

class MigrateDisk extends Command
{
    protected $fileSystem;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:migrate {fromDisk} {toDisk}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate Storage from one  disk to another disk, s3 bucket';

    /**
     * Create a new command instance.
     *
     * @param FilesystemManager $fileSystem
     */
    public function __construct(FilesystemManager $fileSystem)
    {
        $this->fileSystem = $fileSystem;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $fromDisk = $this->argument('fromDisk');
        $toDisk = $this->argument('toDisk');
        try {
            foreach ($this->fileSystem->disk($fromDisk)->allFiles() as $file) {
                if ($file != ".gitignore") {
                    $status = Storage::disk($toDisk)->put($file,
                        Storage::disk($fromDisk)->getDriver()->readStream($file), 'public');
                    $this->info($status . ":" . $file);
                }
            }
        } catch (Exception $exception) {
            $this->error('Error to migrate!, error:' . $exception);
            return;
        }

    }
}
