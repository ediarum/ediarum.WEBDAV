<?php

namespace Tests\Feature;

use App\Jobs\PushToExistDb;
use Tests\TestCase;

class PushToExistDbTest extends TestCase
{

    protected string $stubPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stubPath = base_path('tests/Feature/test_files');
    }

    public function test_text_xml_is_translated_to_application_xml()
    {
        $path = "{$this->stubPath}/example.xml";
        $result = PushToExistDb::setMimeType($path);

        $this->assertEquals('application/xml', $result);
    }

    public function test_application_json_remains_unchanged()
    {
        $path = "{$this->stubPath}/example.json";
        $result = PushToExistDb::setMimeType($path);

        $this->assertEquals('application/json', $result);
    }
}
