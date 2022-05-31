<?php

namespace Aternos\Hawk\Versions\v2730;

use Aternos\Hawk\McCoordinates2D;
use Aternos\Hawk\McCoordinates3D;
use Aternos\Hawk\Section;
use Aternos\Hawk\Versions\v2724\BlockChunkV2724;
use Aternos\Nbt\Tag\CompoundTag;
use Aternos\Nbt\Tag\Tag;
use Exception;

class BlockChunkV2730 extends BlockChunkV2724
{
    /**
     * @param int $location
     * @param int $offset
     * @param int $compressedDataLength
     * @param int $compressionScheme
     * @param Tag $tag
     * @param McCoordinates2D $coordinates
     * @param int $version
     * @throws Exception
     */
    public function __construct(int $location, int $offset, int $compressedDataLength, int $compressionScheme, Tag $tag, McCoordinates2D $coordinates, int $version)
    {
        parent::__construct($location, $offset, $compressedDataLength, $compressionScheme, $tag, $coordinates, $version);
    }

    /**
     * @param CompoundTag $tag
     * @param McCoordinates2D $coordinates
     * @param int $version
     * @return Section|null
     */
    public function newSectionFromTag(CompoundTag $tag, McCoordinates2D $coordinates, int $version): ?Section
    {
        return SectionV2730::newFromTag($tag, $coordinates, $version);
    }

    /**
     * @param McCoordinates3D $coordinates
     * @param int $version
     * @return Section
     */
    public function newEmptySection(McCoordinates3D $coordinates, int $version): Section
    {
        return SectionV2730::newEmpty($coordinates, $version);
    }

}