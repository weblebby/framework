<?php

namespace Feadmin\Console\Commands;

use Feadmin\Support\CurrencyRate;
use Illuminate\Console\Command;

class FetchCurrencyRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feadmin:fetch-currencies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch currency rates';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        app(CurrencyRate::class)->store();

        return static::SUCCESS;
    }
}
