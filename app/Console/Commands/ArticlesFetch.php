<?php

namespace App\Console\Commands;

use App\Models\Source;
use Illuminate\Console\Command;

class ArticlesFetch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sources = Source::whereNotNull('service_class')->get();
        $this->withProgressBar($sources, function (Source $source) {
            if (class_exists($source->service_class)) {
                $service = (new $source->service_class);
                $service->setSource($source);
                $service->articles();
            }
        });
    }
}
