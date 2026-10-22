<?php

namespace Betalabs\Engine\Supports;

use Illuminate\Support\Str;

class ActionMenuTestsFileGenerator
{
    public function generate(
        string $tableName,
        array $fields
    ) : void  {
        $entity = Str::studly($tableName);
        $route = str_replace('_', '-', $tableName);

        $filePath = "tests/Feature/Api/{$entity}/";

        if (!is_dir($filePath)) {
            // Cria o diretório e também cria os diretórios "pais" necessários, se eles não existirem
            mkdir($filePath, 0777, true);
        }


        $MultipleRecordActionMenuTest = <<<EOD
<?php

namespace Tests\Feature\Api\$entity;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\CreatesTenantAndAuthenticate;
use Tests\TestCase;


class MultipleRecordActionMenuTest extends TestCase
{

    use CreatesTenantAndAuthenticate;
    use WithFaker;

    public function testCanRespondMultipleActionMenuStructure()
    {
        \$this->withoutExceptionHandling();
        \$response = \$this->json('POST', '/api/$route/action-menu');
        \$response->assertJsonStructure([
            "data" => [
                [
                    "label",
                    "complete_endpoint",
                    "endpoint",
                    "http_method",
                    "type"
                ]
            ]
        ]);
    }
}


EOD;

        file_put_contents($filePath . 'MultipleRecordActionMenuTest.php', $MultipleRecordActionMenuTest);

        $SingleRecordActionMenuTest = <<<EOD
<?php

namespace Tests\Feature\Api\$entity;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\CreatesTenantAndAuthenticate;
use Tests\TestCase;


class SingleRecordActionMenuTest extends TestCase
{

    use CreatesTenantAndAuthenticate;
    use WithFaker;

    public function testCanRespondSingleActionMenuStructure()
    {
        \$response = \$this->json('GET', '/api/$route/1/action-menu');

        \$response->assertJsonStructure([
            "data" => [
                [
                    "label",
                    "complete_endpoint",
                    "endpoint",
                    "http_method",
                    "type"
                ]
            ]
        ]);
    }
}

EOD;

        file_put_contents($filePath . 'MultipleRecordActionMenuTest.php', $MultipleRecordActionMenuTest);







    }
}
