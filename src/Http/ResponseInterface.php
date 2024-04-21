<?php

namespace OutlineApiClient\Http;

interface ResponseInterface
{
    public function getStatusCode(): int;
    public function getReasonPhrase(): string;
    public function getBody(): string;
    public function getHeaders(): array;
}