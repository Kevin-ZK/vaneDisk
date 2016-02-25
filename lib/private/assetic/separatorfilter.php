<?php

namespace OC\Assetic;

use Assetic\Filter\FilterInterface;
use Assetic\Asset\AssetInterface;

/**
 * Inserts a separator between assets to prevent merge failures
 * e.g. missing semicolon at the end of a JS file
 */
class SeparatorFilter implements FilterInterface
{
    /**
     * @var string
     */
    private $separator;

    /**
     * Constructor.
     *
     * @param string $separator Separator to use between assets
     */
    public function __construct($separator = ';')
    {
        $this->separator = $separator;
    }

    public function filterLoad(AssetInterface $asset)
    {
    }

    public function filterDump(AssetInterface $asset)
    {
        $asset->setContent($asset->getContent() . $this->separator);
    }
}
