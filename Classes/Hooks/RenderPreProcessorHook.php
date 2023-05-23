<?php

namespace WapplerSystems\FontDownloader\Hooks;


use TYPO3\CMS\Core\Cache\Backend\FileBackend;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Exception;
use TYPO3\CMS\Core\Cache\Exception\InvalidDataException;
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use WapplerSystems\FontDownloader\FontLoader\FontLoaderRegistry;

/**
 *
 * @author Sven Wappler <typo3YYYY@wapplersystems.de>
 *
 */
class RenderPreProcessorHook
{

    protected FontLoaderRegistry $fontLoaderRegistry;

    public function __construct()
    {
        $this->fontLoaderRegistry = GeneralUtility::makeInstance(FontLoaderRegistry::class);
    }

    /**
     * Main hook function
     *
     * @param array $params Array of CSS/javascript and other files
     * @param PageRenderer $pageRenderer Pagerenderer object
     * @return void
     * @throws FileDoesNotExistException
     * @throws NoSuchCacheException
     */
    public function renderPreProcessorProc(array &$params, PageRenderer $pageRenderer): void
    {
        if ($GLOBALS['TYPO3_REQUEST'] == null ||
            !ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isFrontend()
        ) {
            return;
        }

        /** @var FileBackend $cache */
        $cache = GeneralUtility::makeInstance(CacheManager::class)->getCache('font-downloader');

        $cssFiles = $params['cssFiles'];
        foreach ($cssFiles as $file => $cssFile) {

            if (!str_starts_with($file,'https://') && !str_starts_with($file,'http://')) {
                continue;
            }

            $cacheKey = hash('sha1', $file);
            if ($cache->has($cacheKey)) {
                $path = $cache->get($cacheKey);
                $pageRenderer->addCssFile($path, $cssFile['rel'] ?? 'stylesheet', $cssFile['media'] ?? 'all', $cssFile['title'] ?? '', $cssFile['compress'] ?? true, $cssFile['forceOnTop'] ?? false, $cssFile['allWrap'] ?? '', $cssFile['excludeFromConcatenation'] ?? false, $cssFile['splitChar'] ?? '|', $cssFile['inline'] ?? false);
                unset($params['cssFiles'][$file]);
                continue;
            }

            $fontLoaders = $this->fontLoaderRegistry->getFontLoaders();

            foreach ($fontLoaders as $fontLoader) {
                if (
                    $fontLoader->isResponsible($file)
                    && ($path = $fontLoader->load($file))
                ) {
                    $pageRenderer->addCssFile($path, $cssFile['rel'] ?? 'stylesheet', $cssFile['media'] ?? 'all', $cssFile['title'] ?? '', $cssFile['compress'] ?? true, $cssFile['forceOnTop'] ?? false, $cssFile['allWrap'] ?? '', $cssFile['excludeFromConcatenation'] ?? false, $cssFile['splitChar'] ?? '|', $cssFile['inline'] ?? false);

                    try {
                        $cache->set($cacheKey, $path, ['fontloader'], 0);
                    } catch (InvalidDataException $e) {
                    } catch (Exception $e) {
                    }

                    unset($params['cssFiles'][$file]);
                }
            }

        }
    }

}
