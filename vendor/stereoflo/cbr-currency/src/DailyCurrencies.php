<?php

namespace Stereoflo\CbrCurrency;

use DateTime;
use Stereoflo\CbrCurrency\HttpClient\HttpClientInterface;
use Stereoflo\CbrCurrency\Parser\ParserInterface;

final class DailyCurrencies
{
    const URI_XML_DAILY = 'https://www.cbr.ru/scripts/XML_daily.asp';

    protected DateTime $date;
    protected HttpClientInterface $httpClient;
    protected ParserInterface $parser;
    protected array $data = [];

    public function __construct(HttpClientInterface $httpClient, ParserInterface $parser)
    {
        $this->httpClient = $httpClient;
        $this->parser     = $parser;
        $this->date       = new DateTime();
    }

    public function withDate(DateTime $date): void
    {
        $this->date = $date;
    }

    public function retrieve(): bool
    {
        $this->httpClient->addQuery('date_req', $this->date->format('d/m/Y'));
        $this->httpClient->execute(self::URI_XML_DAILY);

        if (!$this->httpClient->isRequestSuccess()) {

            return false;
        }

        $xml    = $this->httpClient->getResponseBody();
        $parser = $this->parser;

        if ($this->data = $parser->parse($xml)) {

            return true;
        }

        return false;
    }

    public function get(string $charCode): ?DailyCurrenciesItem
    {
        $charCode = strtoupper($charCode);

        if (!isset($this->data[$charCode])) {
            return null;
        }

        return $this->data[$charCode];
    }

    /**
     * @return DailyCurrenciesItem[]
     */
    public function getAll(): array
    {
        return $this->data;
    }
}
