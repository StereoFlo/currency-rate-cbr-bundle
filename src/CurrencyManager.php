<?php

namespace CurrencyRateCbrBundle;

use DateTime;
use Stereoflo\CbrCurrency\DailyCurrencies;
use Stereoflo\CbrCurrency\DailyCurrenciesItem;
use Symfony\Contracts\Cache\CacheInterface;

class CurrencyManager
{
    private DailyCurrencies $dailyCurrencies;
    private ?CacheInterface $cache;

    public function __construct(CurrencyFactory $currencyFactory, CacheInterface $cache = null)
    {
        $this->dailyCurrencies = $currencyFactory->create();
        $this->cache           = $cache;
    }

    public function get(string $currency, DateTime $dateTime = null): ?DailyCurrenciesItem
    {
        if (empty($dateTime)) {
            $dateTime = new DateTime();
        }

        if (!$this->cache) {
            $this->dailyCurrencies->withDate($dateTime);
            $this->dailyCurrencies->retrieve();

            return $this->dailyCurrencies->get($currency);
        }

        $currencies = $this->cache($dateTime);

        return $this->getFromArray($currencies, $currency);
    }

    public function cache(DateTime $dateTime): array
    {
        $cache = $this->cache->getItem($dateTime->format('d-m-Y'));

        if (!$cache->isHit()) {
            $this->dailyCurrencies->withDate($dateTime);
            $this->dailyCurrencies->retrieve();
            $currencies = $this->dailyCurrencies->getAll();

            $cache->expiresAfter(1200);
            $this->cache->save($cache->set($currencies));

            return $currencies;
        }

        return $cache->get();
    }

    private function getFromArray(array $currencies, string $currency): ?DailyCurrenciesItem
    {
        $charCode = strtoupper($currency);

        if (!isset($currencies[$charCode])) {
            return null;
        }

        return $currencies[$charCode];
    }
}
