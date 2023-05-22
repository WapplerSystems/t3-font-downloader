<?php

declare(strict_types=1);

namespace WapplerSystems\FontDownloader\FontLoader;

class FontLoaderRegistry
{
    /**
     * @var FontLoaderInterface[]
     */
    private array $fontLoaders = [];

    public function __construct(iterable $fontLoaders)
    {
        foreach ($fontLoaders as $fontLoader) {
            if (!($fontLoader instanceof FontLoaderInterface)) {
                continue;
            }

            $identifier = $fontLoader->getIdentifier();
            if ($identifier === '') {
                throw new \InvalidArgumentException('Identifier for font loader ' . get_class($fontLoader) . ' is empty.', 1647241084);
            }
            if (isset($this->fontLoaders[$identifier])) {
                throw new \InvalidArgumentException('Font loader with identifier ' . $identifier . ' is already registered.', 1647241085);
            }
            $this->fontLoaders[$identifier] = $fontLoader;
        }
    }

    /**
     */
    public function hasFontLoader(string $identifier): bool
    {
        return isset($this->fontLoaders[$identifier]);
    }

    public function getFontLoader(string $identifier): FontLoaderInterface
    {
        if (!$this->hasFontLoader($identifier)) {
            throw new \UnexpectedValueException('Font loader with identifier ' . $identifier . ' is not registered.', 1647241086);
        }

        return $this->fontLoaders[$identifier];
    }

    /**
     *
     * @return FontLoaderInterface[]
     */
    public function getFontLoaders(): array
    {
        return $this->fontLoaders;
    }
}
