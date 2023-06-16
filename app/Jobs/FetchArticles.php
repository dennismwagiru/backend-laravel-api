<?php

namespace App\Jobs;

use App\Domain\News\ApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
        $sources = array_filter(
            config('settings.sources'),
            fn($el) => array_key_exists('service', $el) && class_exists($el['service'])
        );

        foreach ($sources as $source) {
            $service = (new $source['service']);

            print_r($service->articles());
        }
    }
}
