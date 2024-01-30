<?php

namespace Weblebby\Framework\Console\Commands;

use Illuminate\Console\Command;
use Weblebby\Framework\Support\CurrencyRate;

class FetchCurrencyRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weblebby:fetch-currencies';

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
