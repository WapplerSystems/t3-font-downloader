<?php

declare(strict_types=1);

namespace WapplerSystems\FontDownloader\FontLoader;

interface FontLoaderInterface {

    public function getIdentifier(): string;

    public function load(string $url): string;

    public function isResponsible(string $url): bool;
}
