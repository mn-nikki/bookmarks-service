<?php


namespace App\Service;


use DOMDocument;

class Parser implements ParserInterface
{
    /**
     * @inheritDoc
     */
    public function getTitle(DOMDocument $document): ?string
    {
        $tags = $document->getElementsByTagName('title');
        return $tags[0]->nodeValue ?? null;
    }
    /**
     * @inheritDoc
     */
    public function getMetaDescription(DOMDocument $document): ?string
    {
        foreach($document->getElementsByTagName('meta') as $tag)
        {
            $name = \strtolower($tag->getAttribute('name'));

            if(!empty($name) && $name === 'description')
            {
                $description = $tag->getAttribute('content');
            }
        }

        return $description ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getMetaKeywords(DOMDocument $document): ?string
    {
        foreach($document->getElementsByTagName('meta') as $tag)
        {
            $name = \strtolower($tag->getAttribute('name'));

            if(!empty($name) && $name === 'keywords')
            {
                $keywords = $tag->nodeValue;
            }
        }

        return $keywords ?? null;
    }
    /**
     * @inheritDoc
     */
    public function getFavicon(string $url): ?string
    {
        $iconSrc = null;
        $parts = parse_url($url);
        $url = $parts['scheme'] . '://' . $parts['host'];

        $iconUrl = $this->parseFaviconFromApi($url);

        if($iconUrl !== null)
        {
            $iconSize = getimagesize($iconUrl);
            $iconData = base64_encode(file_get_contents($iconUrl));
            $iconSrc = "data:{$iconSize['mime']};base64,{$iconData}";
        }

        return $iconSrc ?? null;
    }

    /**
     * @param string $url
     * @param DOMDocument $document
     * @return string|null
     */
    private function parseFavicon(string $url, DOMDocument $document): ?string
    {
        $iconsData = [];
        $iconUrl = null;

        foreach($document->getElementsByTagName('link') as $tag)
        {
            $rel = \strtolower($tag->getAttribute('rel'));
            $href = $tag->getAttribute('href');

            if(!empty($rel) && !empty($href) && !\strpos($href, 'http') === false && !\strpos($href, '.svg') === false
                && ($rel === 'icon' || $rel === 'shortcut icon'))
            {
                $rel = str_replace(" ", "_", $rel);
                $iconsData[$rel] = $url . $href;
            }
        }

        if (isset($iconsData['icon']) && $iconsData['icon']) {
            $iconUrl = $iconsData['icon'];
        } elseif((isset($iconsData['shortcut_icon']) && $iconsData['shortcut_icon'])) {
            $iconUrl = $iconsData['shortcut_icon'];
        }

        if($iconUrl === null) {
            $iconUrl = $this->parseFaviconFromApi($url);
        }

        return $iconUrl ?? null;
    }

    /**
     * @param $url
     * @return string
     * Использует google api, чтобы получить иконку
     */
    private function parseFaviconFromApi(string $url): ?string
    {
        $iconUrl = "https://www.google.com/s2/favicons?domain={$url}";
        if(!$this->checkAvailability($iconUrl)) $iconUrl = null;
        return $iconUrl;
    }

    /**
     * @param ?string $url
     * @return bool
     */
    private function checkAvailability(?string $url): bool
    {
        $access = false;
        if($url !== null && filter_var($url, FILTER_VALIDATE_URL) !== false && file_get_contents($url) !== false) $access = true;
        return $access;
    }
}