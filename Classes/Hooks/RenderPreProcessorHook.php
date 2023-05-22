<?php

namespace WapplerSystems\FontDownloader\Hooks;


use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 *
 * @author Sven Wappler <typo3YYYY@wapplersystems.de>
 *
 */
class RenderPreProcessorHook
{


    /**
     * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
     */
    private $contentObjectRenderer;

    /**
     * Main hook function
     *
     * @param array $params Array of CSS/javascript and other files
     * @param PageRenderer $pagerenderer Pagerenderer object
     * @return void
     * @throws FileDoesNotExistException
     * @throws NoSuchCacheException
     */
    public function renderPreProcessorProc(array &$params, PageRenderer $pagerenderer): void
    {
        if ($GLOBALS['TYPO3_REQUEST'] == null ||
            !ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isFrontend()
        ) {
            return;
        }

        if (!\is_array($params['cssFiles'])) {
            return;
        }

        $setup = $GLOBALS['TSFE']->tmpl->setup;

        // we need to rebuild the CSS array to keep order of CSS files
        $cssFiles = [];
        foreach ($params['cssFiles'] as $file => $conf) {
            $pathInfo = pathinfo($conf['file']);

            if ($pathInfo['extension'] !== 'scss') {
                $cssFiles[$file] = $conf;
                continue;
            }

            $useSourceMap = false;
            $outputFilePath = null;
            $variables = [];

            // search settings for scss file
            if (is_array($GLOBALS['TSFE']->pSetup['includeCSS.'] ?? [])) {
                foreach ($GLOBALS['TSFE']->pSetup['includeCSS.'] as $key => $keyValue) {
                    if (str_ends_with($key, '.')) {
                        continue;
                    }

                    if ($file === $keyValue) {
                        $subConf = $GLOBALS['TSFE']->pSetup['includeCSS.'][$key . '.'] ?? [];

                        $outputFilePath = $subConf['outputfile'] ?? null;
                        $useSourceMap = $this->parseBooleanSetting($subConf['sourceMap'] ?? false, false);
                        if (isset($subConf['outputStyle']) && ($subConf['outputStyle'] === 'expanded' || $subConf['outputStyle'] === 'compressed')) {
                            $outputStyle = $subConf['outputStyle'];
                        }
                        $variables = array_filter($subConf['variables.'] ?? []);
                        $inlineOutput = $this->parseBooleanSetting($GLOBALS['TSFE']->pSetup['includeCSS.'][$key . '.']['inlineOutput'] ?? false, false);
                    }
                }
            }

            $scssFilePath = GeneralUtility::getFileAbsFileName($conf['file']);
            $pathChunks = explode('/', PathUtility::getAbsoluteWebPath($scssFilePath));
            $assetPath = implode('/', array_splice($pathChunks, 0, 3)) . '/';

            if ($inlineOutput) {
                $useSourceMap = false;
            }


            $cssFiles[$cssFilePath] = $params['cssFiles'][$file];
            $cssFiles[$cssFilePath]['file'] = $cssFilePath;

        }
        $params['cssFiles'] = $cssFiles;
    }

    private function parseBooleanSetting(string $value, bool $defaultValue): bool
    {
        if (trim($value) === 'true' || trim($value) === '1') {
            return true;
        }
        if (trim($value) === 'false' || trim($value) === '0') {
            return false;
        }
        return $defaultValue;
    }
}
