<?php

namespace App\Service;

interface UrlValidatorInterface
{
    /**
     * @param string $url
     * @return object|null
     */
    public function validateUrl(string $url): ?array;
}
