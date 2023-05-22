<?php

declare(strict_types=1);

namespace WapplerSystems\FontDownloader\FontLoader;

class GoogleFontLoader extends AbstractFontLoader
{
    protected string $identifier = 'googlefont';

    public function isResponsible(string $url): bool
    {
        // Example: https://fonts.googleapis.com/css2?family=Hammersmith+One
        return (bool)preg_match('/^https?:\/\/fonts\.googleapis\.com\/css2?\?/i', $url);
    }

}
