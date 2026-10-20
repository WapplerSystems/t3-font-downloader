<?php

declare(strict_types=1);

namespace WapplerSystems\FontDownloader\FontLoader;

use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class GoogleFontLoader extends AbstractFontLoader
{
    protected string $identifier = 'googlefont';

    /**
     * Google picks the font format by user agent. Without a browser user agent
     * (TYPO3 sends "TYPO3") it serves TrueType, which is several times larger
     * than WOFF2. Every browser TYPO3 v14 supports reads WOFF2.
     */
    private const USER_AGENT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36';

    public function isResponsible(string $url): bool
    {
        // Example: https://fonts.googleapis.com/css2?family=Hammersmith+One
        return (bool)preg_match('/^https?:\/\/fonts\.googleapis\.com\/css2?\?/i', $url);
    }

    protected function fetchCss(string $url): string|false
    {
        try {
            $response = GeneralUtility::makeInstance(RequestFactory::class)->request($url, 'GET', [
                'headers' => ['User-Agent' => self::USER_AGENT],
            ]);
        } catch (\Throwable) {
            return false;
        }

        return $response->getStatusCode() === 200 ? (string)$response->getBody() : false;
    }

    protected function getCacheKey(string $url): string
    {
        // Stylesheets cached before 14.0.2 reference TrueType files.
        return $url . '#woff2';
    }

}
