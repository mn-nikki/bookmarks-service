<?php

declare(strict_types=1);

namespace App\Service;

use DOMDocument;

interface ParserInterface
{
    /**
     * @param DOMDocument $document
     * @return string|null
     *                     Возвращает заголовок страницы
     */
    public function getTitle(DOMDocument $document): ?string;

    /**
     * @param DOMDocument $document
     * @return string|null
     *                     Возвращает описание страницы
     */
    public function getMetaDescription(DOMDocument $document): ?string;

    /**
     * @param DOMDocument $document
     * @return string|null
     *                     Возвращает ключевые слова страницы
     */
    public function getMetaKeywords(DOMDocument $document): ?string;

    /**
     * @param string $url
     * @return string
     *                Возвращает src favicon в формате base64
     */
    public function getFavicon(string $url): ?string;
}
