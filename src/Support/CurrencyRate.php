<?php

namespace Feadmin\Support;

use Feadmin\Enums\CurrencyEnum;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CurrencyRate
{
    protected Collection $rates;

    public function __construct()
    {
        $this->loadRates();
    }

    public function get(): Collection
    {
        return $this->rates;
    }

    public function find(CurrencyEnum $currency): ?object
    {
        return $this->get()->where('currency', $currency->value)->first();
    }

    public function store(): void
    {
        $rates = app()->environment('production')
            ? $this->ratesFromApi()
            : $this->ratesFromLocal();

        foreach ($rates['data'] as $currency) {
            DB::table('currency_rates')->updateOrInsert(
                ['currency' => $currency['code']],
                ['rate' => round($currency['value'], 6) * Currency::digits()],
            );
        }
    }

    public function ratesFromApi(): array
    {
        $response = Http::get('https://api.currencyapi.com/v3/latest', [
            'apikey' => config('services.currency-api.access_token'),
            'base_currency' => Currency::primary()->value,
            'currencies' => implode(',', array_map(fn(CurrencyEnum $currency) => $currency->value, CurrencyEnum::casesWithoutPrimary())),
        ]);

        return $response->json();
    }

    public function ratesFromLocal(): array
    {
        return [
            'data' => [
                ['code' => 'TRY', 'value' => 26.801215],
            ],
        ];
    }

    public function loadRates(): void
    {
        $this->rates = DB::table('currency_rates')
            ->select('id', 'currency', 'rate')
            ->get();
    }
}
