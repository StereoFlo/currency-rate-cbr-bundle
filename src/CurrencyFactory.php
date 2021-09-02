<?php

namespace CurrencyRateCbrBundle;

use Stereoflo\CbrCurrency\DailyCurrencies;
use Stereoflo\CbrCurrency\HttpClient\SymfonyHttpClient;
use Stereoflo\CbrCurrency\Parser\XmlParser;

class CurrencyFactory
{
    public function create(): DailyCurrencies
    {
        return new DailyCurrencies(new SymfonyHttpClient(), new XmlParser());
    }
}
