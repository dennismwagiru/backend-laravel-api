<?php

namespace App\Jobs;

use App\Domain\News\ApiService;
use App\Domain\News\NewsApiService;
use App\Models\Source;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use ReflectionClass;

class FetchArticles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Source::whereNotNull('service_class')->each(
            function (Source $source) {
                if (class_exists($source->service_class)) {
                    $service = (new $source->service_class);
                    $service->setSource($source);
                    $service->articles();
                }
            }
        );
    }
}
