<?php

declare(strict_types=1);

namespace WapplerSystems\FontDownloader;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use WapplerSystems\FontDownloader\FontLoader\FontLoaderInterface;

return static function (ContainerConfigurator $container, ContainerBuilder $containerBuilder) {
    $containerBuilder->registerForAutoconfiguration(FontLoaderInterface::class)->addTag('fontloader');

};
