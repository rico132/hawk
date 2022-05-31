<?php

namespace Aternos\Hawk\Tests\Integration;

use Aternos\Hawk\Entity;
use Aternos\Hawk\File;
use Aternos\Hawk\Hawk;
use Aternos\Hawk\McCoordinates3D;
use Aternos\Hawk\McCoordinatesFloat;
use DirectoryIterator;
use Exception;
use PHPUnit\Framework\TestCase;

class HawkTest extends TestCase
{
    public function getBlockCoords(): McCoordinates3D
    {
        return new McCoordinates3D(1, 1, 1);
    }

    public function getNegativeBlockCoords(): McCoordinates3D
    {
        return new McCoordinates3D(-1, 1, -1);
    }

    public function getEntityCoords(): McCoordinatesFloat
    {
        return new McCoordinatesFloat(1.4, 64, 1.4);
    }

    public function getNegativeEntityCoords(): McCoordinatesFloat
    {
        return new McCoordinatesFloat(-0.4, 64, -0.4);
    }

    public function getExactEntityCoords(): McCoordinatesFloat
    {
        return new McCoordinatesFloat(1.5, 64, 1.5);
    }

    public function getExactNegativeEntityCoords(): McCoordinatesFloat
    {
        return new McCoordinatesFloat(-0.5, 64, -0.5);
    }

    function copyTestFiles($src = null, $dst = null)
    {
        if($src === null){
            $src = __DIR__ . "/../../examples/resources/versions";
        }
        if($dst === null){
            $dst = __DIR__ . "/../files/versions";
        }

        $dir = opendir($src);
        if (!file_exists($dst)) {
            @mkdir($dst, recursive: true);
        }
        while (($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->copyTestFiles($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }


    /**
     * @return array
     * @throws Exception
     */
    public function provideSingleBlockFile(): array
    {
        $this->copyTestFiles();
        $blockFiles = [];
        $versions = new DirectoryIterator(__DIR__ . "/../files/versions");
        foreach ($versions as $version) {
            if (!$version->isDot()) {
                $dirName = $version->getFilename();
                $versionName = explode("(", $dirName)[0];
                $major = explode(".", $versionName)[1];
                if ($major > 16) {
                    $blockFiles[$versionName] = [
                        [
                            new File(__DIR__ . "/../files/versions/" . $dirName . "/region/r.0.0.mca")
                        ],
                        [
                            new File(__DIR__ . "/../files/versions/" . $dirName . "/entities/r.0.0.mca")
                        ],
                    ];
                } else {
                    $blockFiles[$versionName] = [
                        [
                            new File(__DIR__ . "/../files/versions/" . $dirName . "/region/r.0.0.mca")
                        ],
                        []
                    ];
                }
            }
        }
        return $blockFiles;
    }

    /**
     * @return array
     * @throws Exception
     */
    public function provideNegativeSingleBlockFile(): array
    {
        $this->copyTestFiles();
        $blockFiles = [];
        $versions = new DirectoryIterator(__DIR__ . "/../files/versions");
        foreach ($versions as $version) {
            if (!$version->isDot()) {
                $dirName = $version->getFilename();
                $versionName = explode("(", $dirName)[0];
                $major = explode(".", $versionName)[1];
                if ($major > 16) {
                    $blockFiles[$versionName] = [
                        [
                            new File(__DIR__ . "/../files/versions/" . $dirName . "/region/r.-1.-1.mca")
                        ],
                        [
                            new File(__DIR__ . "/../files/versions/" . $dirName . "/entities/r.-1.-1.mca")
                        ],
                    ];
                } else {
                    $blockFiles[$versionName] = [
                        [
                            new File(__DIR__ . "/../files/versions/" . $dirName . "/region/r.-1.-1.mca")
                        ],
                        []
                    ];
                }
            }
        }
        return $blockFiles;
    }

    /**
     * @dataProvider provideSingleBlockFile
     * @param array $blockFiles
     * @param array $entitiesFiles
     * @return void
     * @throws Exception
     */
    public function testGetBlock(array $blockFiles, array $entitiesFiles): void
    {
        $hawk = new Hawk(blockFiles: $blockFiles, entitiesFiles: $entitiesFiles);
        $block = $hawk->getBlock($this->getBlockCoords());
        $this->assertEquals("minecraft:furnace", $block->getPaletteBlock()->getName());
    }

    /**
     * @dataProvider provideNegativeSingleBlockFile
     * @param array $blockFiles
     * @param array $entitiesFiles
     * @return void
     * @throws Exception
     */
    public function testGetBlockNegative(array $blockFiles, array $entitiesFiles): void
    {
        $hawk = new Hawk(blockFiles: $blockFiles, entitiesFiles: $entitiesFiles);
        $block = $hawk->getBlock($this->getNegativeBlockCoords());
        $this->assertEquals("minecraft:furnace", $block->getPaletteBlock()->getName());
    }

    /**
     * @dataProvider provideSingleBlockFile
     * @param array $blockFiles
     * @param array $entitiesFiles
     * @return void
     * @throws Exception
     */
    public function testReplaceBlock(array $blockFiles, array $entitiesFiles): void
    {
        $hawk = new Hawk(blockFiles: $blockFiles, entitiesFiles: $entitiesFiles);
        $block = $hawk->getBlock($this->getBlockCoords());
        $this->assertEquals("minecraft:furnace", $block->getPaletteBlock()->getName());
        $hawk->replaceBlock($this->getBlockCoords(), "minecraft:wool");
        $hawk->save();

        $hawk = new Hawk(blockFiles: $blockFiles, entitiesFiles: $entitiesFiles);
        $block = $hawk->getBlock($this->getBlockCoords());
        $this->assertEquals("minecraft:wool", $block->getPaletteBlock()->getName());
    }

    /**
     * @dataProvider provideNegativeSingleBlockFile
     * @param array $blockFiles
     * @param array $entitiesFiles
     * @return void
     * @throws Exception
     */
    public function testReplaceBlockNegative(array $blockFiles, array $entitiesFiles): void
    {
        $hawk = new Hawk(blockFiles: $blockFiles, entitiesFiles: $entitiesFiles);
        $block = $hawk->getBlock($this->getNegativeBlockCoords());
        $this->assertEquals("minecraft:furnace", $block->getPaletteBlock()->getName());
        $hawk->replaceBlock($this->getNegativeBlockCoords(), "minecraft:wool");
        $hawk->save();

        $hawk = new Hawk(blockFiles: $blockFiles, entitiesFiles: $entitiesFiles);
        $block = $hawk->getBlock($this->getNegativeBlockCoords());
        $this->assertEquals("minecraft:wool", $block->getPaletteBlock()->getName());
    }

    /**
     * @dataProvider provideSingleBlockFile
     * @param array $blockFiles
     * @param array $entitiesFiles
     * @return void
     * @throws Exception
     */
    public function testGetAllEntitiesFromChunk(array $blockFiles, array $entitiesFiles): void
    {
        $hawk = new Hawk(blockFiles: $blockFiles, entitiesFiles: $entitiesFiles);
        $entities = $hawk->getAllEntitiesFromChunk($this->getBlockCoords());
        foreach ($entities as $entity) {
            $this->assertInstanceOf(Entity::class, $entity);
        }
    }

    /**
     * @dataProvider provideNegativeSingleBlockFile
     * @param array $blockFiles
     * @param array $entitiesFiles
     * @return void
     * @throws Exception
     */
    public function testGetAllEntitiesFromNegativeChunk(array $blockFiles, array $entitiesFiles): void
    {
        $hawk = new Hawk(blockFiles: $blockFiles, entitiesFiles: $entitiesFiles);
        $entities = $hawk->getAllEntitiesFromChunk($this->getNegativeBlockCoords());
        foreach ($entities as $entity) {
            $this->assertInstanceOf(Entity::class, $entity);
        }
    }

    /**
     * @dataProvider provideSingleBlockFile
     * @param array $blockFiles
     * @param array $entitiesFiles
     * @return void
     * @throws Exception
     */
    public function testGetEntities(array $blockFiles, array $entitiesFiles): void
    {
        $hawk = new Hawk(blockFiles: $blockFiles, entitiesFiles: $entitiesFiles);
        $entities = $hawk->getEntities("minecraft:chicken", $this->getEntityCoords());
        foreach ($entities as $entity) {
            $this->assertInstanceOf(Entity::class, $entity);
            $this->assertEquals("minecraft:chicken", $entity->getName());
            $this->assertTrue($entity->equals($this->getExactEntityCoords()));
        }
    }

    /**
     * @dataProvider provideNegativeSingleBlockFile
     * @param array $blockFiles
     * @param array $entitiesFiles
     * @return void
     * @throws Exception
     */
    public function testGetNegativeEntities(array $blockFiles, array $entitiesFiles): void
    {
        $hawk = new Hawk(blockFiles: $blockFiles, entitiesFiles: $entitiesFiles);
        $entities = $hawk->getEntities("minecraft:chicken", $this->getNegativeEntityCoords());
        foreach ($entities as $entity) {
            $this->assertInstanceOf(Entity::class, $entity);
            $this->assertEquals("minecraft:chicken", $entity->getName());
            $this->assertTrue($entity->equals($this->getExactNegativeEntityCoords(),0.1));
        }
    }

    /**
     * @dataProvider provideSingleBlockFile
     * @param array $blockFiles
     * @param array $entitiesFiles
     * @return void
     * @throws Exception
     */
    public function testDeleteEntity(array $blockFiles, array $entitiesFiles): void
    {
        $hawk = new Hawk(blockFiles: $blockFiles, entitiesFiles: $entitiesFiles);
        $entities = $hawk->getEntities("minecraft:chicken", $this->getEntityCoords());
        $this->assertEquals("minecraft:chicken", $entities[0]->getName());
        $hawk->deleteEntity($entities[0]);
        $hawk->save();

        $hawk = new Hawk(blockFiles: $blockFiles, entitiesFiles: $entitiesFiles);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Entity not found.");
        $hawk->getEntities("minecraft:chicken", $this->getEntityCoords());
    }

    /**
     * @dataProvider provideNegativeSingleBlockFile
     * @param array $blockFiles
     * @param array $entitiesFiles
     * @return void
     * @throws Exception
     */
    public function testDeleteNegativeEntity(array $blockFiles, array $entitiesFiles): void
    {
        $hawk = new Hawk(blockFiles: $blockFiles, entitiesFiles: $entitiesFiles);
        $entities = $hawk->getEntities("minecraft:chicken", $this->getNegativeEntityCoords());
        $this->assertEquals("minecraft:chicken", $entities[0]->getName());
        $hawk->deleteEntity($entities[0]);
        $hawk->save();

        $hawk = new Hawk(blockFiles: $blockFiles, entitiesFiles: $entitiesFiles);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Entity not found.");
        $hawk->getEntities("minecraft:chicken", $this->getNegativeEntityCoords());
    }
}