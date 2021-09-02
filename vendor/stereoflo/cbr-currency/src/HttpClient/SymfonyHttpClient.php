<?php

namespace Stereoflo\CbrCurrency\HttpClient;

use Exception;
use Symfony\Component\HttpClient\HttpClient;

final class SymfonyHttpClient implements HttpClientInterface
{
    private array $query = [];
    private ?Exception $requestError;
    private ?string $responseBody;
    private bool $isRequestSuccess = false;

    public function execute(string $uri): bool
    {
        try {
            $this->makeRequest($uri);
            $this->isRequestSuccess = true;

            return true;
        } catch (Exception $e) {
            $this->isRequestSuccess = false;
            $this->requestError = $e;
        }

        return false;
    }

    public function getRequestError(): ?Exception
    {
        return $this->requestError;
    }

    public function isRequestSuccess(): bool
    {
        return $this->isRequestSuccess;
    }

    public function getResponseBody(): ?string
    {
        return $this->responseBody;
    }

    public function addQuery(string $key, string $value): void
    {
        $this->query[$key] = $value;
    }

    private function makeRequest(string $uri): void
    {
        $client                 = HttpClient::create();
        $response               = $client->request('GET', $this->buildUri($uri));
        $this->isRequestSuccess = true;
        $this->responseBody     = $response->getContent();
    }

    private function buildUri(string $uri): string
    {
        $path = $uri;

        if ($this->query) {
            $path .= '?' . http_build_query($this->query);
        }

        return $path;
    }
}
