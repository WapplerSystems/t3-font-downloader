<?php

declare(strict_types=1);

namespace WapplerSystems\FontDownloader\FontLoader;

class FontAwesomeLoader extends AbstractFontLoader
{
    protected string $identifier = 'fontawesome';

    public function isResponsible(string $url): bool
    {
        // Example: https://use.fontawesome.com/releases/v5.12.0/css/all.css?wpfas=true
        return (bool)preg_match('/^https?:\/\/(\w+.)?fontawesome\.com\/releases\/./i', $url);
    }

}
