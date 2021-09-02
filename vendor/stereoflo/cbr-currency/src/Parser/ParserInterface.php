<?php

namespace Stereoflo\CbrCurrency\Parser;

interface ParserInterface
{
    public function parse(string $data): array;
}
