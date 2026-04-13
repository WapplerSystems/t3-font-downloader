<?php

namespace WapplerSystems\FontDownloader\Hooks;

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Exception;
use TYPO3\CMS\Core\Cache\Exception\InvalidDataException;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Page\PageRenderer;
use WapplerSystems\FontDownloader\FontLoader\FontLoaderRegistry;

/**
 * @author Sven Wappler <typo3YYYY@wapplersystems.de>
 */
readonly class RenderPreProcessorHook
{
    private FrontendInterface $cache;

    public function __construct(
        private FontLoaderRegistry $fontLoaderRegistry,
        CacheManager $cacheManager,
    ) {
        $this->cache = $cacheManager->getCache('font-downloader');
    }

    public function renderPreProcessorProc(array &$params, PageRenderer $pageRenderer): void
    {
        if ($GLOBALS['TYPO3_REQUEST'] === null ||
            !ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isFrontend()
        ) {
            return;
        }

        $cssFiles = $params['cssFiles'];
        foreach ($cssFiles as $file => $cssFile) {

            if (!str_starts_with($file, 'https://') && !str_starts_with($file, 'http://')) {
                continue;
            }

            $cacheKey = hash('sha1', $file);
            if ($this->cache->has($cacheKey)) {
                $path = $this->cache->get($cacheKey);
                $pageRenderer->addCssFile($path, $cssFile['rel'] ?? 'stylesheet', $cssFile['media'] ?? 'all', $cssFile['title'] ?? '', forceOnTop: $cssFile['forceOnTop'] ?? false, allWrap: $cssFile['allWrap'] ?? '', splitChar: $cssFile['splitChar'] ?? '|', inline: $cssFile['inline'] ?? false);
                unset($params['cssFiles'][$file]);
                continue;
            }

            $fontLoaders = $this->fontLoaderRegistry->getFontLoaders();

            foreach ($fontLoaders as $fontLoader) {
                if (
                    $fontLoader->isResponsible($file)
                    && ($path = $fontLoader->load($file))
                ) {
                    $pageRenderer->addCssFile($path, $cssFile['rel'] ?? 'stylesheet', $cssFile['media'] ?? 'all', $cssFile['title'] ?? '', forceOnTop: $cssFile['forceOnTop'] ?? false, allWrap: $cssFile['allWrap'] ?? '', splitChar: $cssFile['splitChar'] ?? '|', inline: $cssFile['inline'] ?? false);

                    try {
                        $this->cache->set($cacheKey, $path, ['fontloader'], 0);
                    } catch (InvalidDataException) {
                    } catch (Exception) {
                    }

                    unset($params['cssFiles'][$file]);
                }
            }
        }
    }
}