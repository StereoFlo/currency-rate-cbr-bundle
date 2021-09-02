<?php

namespace Stereoflo\CbrCurrency;

use DateTime;
use SimpleXMLElement;
use function str_replace;

final class DailyCurrenciesItem
{
    private DateTime $date;
    private float $value;
    private string $valueId;
    private string $numCode;
    private string $charCode;
    private string $nominal;
    private string $name;

    public function __construct(DateTime $date, SimpleXMLElement $row)
    {
        $this->date     = $date;
        $this->value    = (float) str_replace(',', '.', $row->Value);
        $this->name     = (string) $row->Name;
        $this->valueId  = (string) $row->attributes()->ID;
        $this->charCode = (string) $row->CharCode;
        $this->numCode  = (string) $row->NumCode;
        $this->nominal  = (string) $row->Nominal;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function getValueId(): string
    {
        return $this->valueId;
    }

    public function getNumCode(): string
    {
        return $this->numCode;
    }

    public function getCharCode(): string
    {
        return $this->charCode;
    }

    public function getNominal(): string
    {
        return $this->nominal;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): float
    {
        return $this->value;
    }
}
