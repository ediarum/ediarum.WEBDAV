<?php

namespace Tests\Unit\App\Helpers;

use App\Helpers\FolderContents;
use PHPUnit\Framework\TestCase;

class ExistFolderContentsTest extends TestCase
{
    public function test_existdb_contents_are_parsed(): void
    {
        $filePath = __DIR__ . '/../../../resources/example_folder_contents.xml';
        $xml = file_get_contents($filePath);

        $folderContents = new FolderContents();
        $folderContents->parseExistDBCollectionContents($xml);

        $this->assertEquals(5, $folderContents->getFolders()->count());
        $this->assertEquals(1, $folderContents->getFiles()->count());

        $this->assertEquals('An', $folderContents->getFolders()[0]);
        $this->assertEquals('Blemmydes-Aposemeioseis-Bon-3637.xml', $folderContents->getFiles()->first());

    }

    public function test_file_system_contents_are_parsed():void
    {
        $filePath = __DIR__ . '/../../../resources/cagb_data';
        $folderContents = new FolderContents();
        $folderContents->parseFileSystemFolderContents($filePath);

        $this->assertEquals("example.xml", $folderContents->getFiles()->first());
        $this->assertEquals(2, $folderContents->getFolders()->count());

    }
}
