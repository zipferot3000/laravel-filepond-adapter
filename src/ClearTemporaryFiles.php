<?php

namespace Zipferot3000\LaravelFilepondAdapter;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ClearTemporaryFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fp_adapter:clear {--hour=5}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear temporary files';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $h = $this->option('hour');

        $where = [
            ['collection_name', config('fp_adapter.media_collection')],
            ['created_at', '<=', now()->subHours($h)]
        ];

        $media = Media::where($where)->get();

        $media->each(function($file) {
            Storage::disk(config('fp_adapter.filesystem'))->deleteDirectory($file->id);
            $file->delete();
        });

        return 0;
    }
}