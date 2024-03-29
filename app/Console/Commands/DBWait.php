<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class DBWait extends Command
{
    /**
     * Wait sleep time for db connection in seconds
     */
    private const WAIT_SLEEP_TIME = 2;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:wait';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Waits for database availability.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        {
            for ($i = 0; $i < 60; $i += self::WAIT_SLEEP_TIME) {
                try {
                    DB::select('SHOW TABLES');
                    $this->info('Connection to the database is ok!');

                    return 0;
                } catch (QueryException $exception) {
                    $this->comment('Trying to connect to the database seconds:' . $i);
                    sleep(self::WAIT_SLEEP_TIME);

                    continue;
                }
            }

            $this->error('Can not connect to the database');

            return 1;
        }
    }
}
