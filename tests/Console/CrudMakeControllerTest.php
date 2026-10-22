<?php

namespace Betalabs\Engine\Tests\Console;

use Betalabs\Engine\Console\Commands\CrudMakeController;
use Betalabs\Engine\Supports\ActionMenuFileGenerator;
use Betalabs\Engine\Supports\ControllerFileGenerator;
use Betalabs\Engine\Supports\IndexHandlerFileGenerator;
use Betalabs\Engine\Supports\MigrationFileGenerator;
use Betalabs\Engine\Supports\ModelFileGenerator;
use Betalabs\Engine\Supports\ResourceFileGenerator;
use Betalabs\Engine\Supports\RoutesGenerator;
use Betalabs\Engine\Supports\StructureFileGenerator;
use Betalabs\Engine\Tests\TestCase;
use Illuminate\Container\Container;
use Illuminate\Foundation\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Runs the crud-maker command with simulated input; generators are mocked so
 * no file is written
 */
class CrudMakeControllerTest extends TestCase
{

    const GENERATORS = [
        MigrationFileGenerator::class,
        ModelFileGenerator::class,
        ControllerFileGenerator::class,
        StructureFileGenerator::class,
        IndexHandlerFileGenerator::class,
        ActionMenuFileGenerator::class,
        ResourceFileGenerator::class,
        RoutesGenerator::class,
    ];

    public function tearDown(): void
    {
        // new Application() registers itself as the global container
        Container::setInstance(null);
        parent::tearDown();
    }

    public function testValidFieldsAreSentToEveryGenerator()
    {

        $app = $this->appWithGenerators(1, 'orders', [['name', 'string'], ['total', 'integer']]);

        $tester = $this->runCommand($app, ['orders', 'name,string', ' total , integer ', 'sair']);

        $this->assertEquals(0, $tester->getStatusCode());
        $this->assertStringContainsString('name, string', $tester->getDisplay());
        $this->assertStringContainsString('total, integer', $tester->getDisplay());
        $this->assertStringContainsString('Add new routes in api.php finished', $tester->getDisplay());

    }

    public function testInvalidFieldIsRejectedAndAskedAgain()
    {

        $app = $this->appWithGenerators(1, 'orders', [['name', 'string']]);

        $tester = $this->runCommand($app, ['orders', 'name', 'name,string,extra', ',string', 'name,string', 'sair']);

        $this->assertEquals(
            3,
            substr_count($tester->getDisplay(), 'Formato inválido')
        );

    }

    public function testExitWithoutFieldsSendsEmptyFieldList()
    {

        $app = $this->appWithGenerators(1, 'orders', []);

        $tester = $this->runCommand($app, ['orders', 'SAIR']);

        $this->assertEquals(0, $tester->getStatusCode());

    }

    public function testEmptyTableNameStopsWithoutGenerating()
    {

        $app = $this->appWithGenerators(0);

        $tester = $this->runCommand($app, ['   ']);

        $this->assertStringContainsString('O nome da tabela é obrigatório', $tester->getDisplay());

    }

    protected function appWithGenerators($times, $tableName = null, $fields = null)
    {

        // Laravel 12 commands need the full Application (runningUnitTests(), prompts)
        $app = new Application(sys_get_temp_dir());

        foreach (self::GENERATORS as $class) {
            $generator = \Mockery::mock($class);
            $expectation = $generator->shouldReceive('generate')->times($times);

            if ($times > 0) {
                $expectation->with($tableName, $fields);
            }

            $app->instance($class, $generator);
        }

        return $app;

    }

    protected function runCommand(Container $app, array $inputs)
    {

        $command = new CrudMakeController();
        $command->setLaravel($app);

        $tester = new CommandTester($command);
        $tester->setInputs($inputs);
        $tester->execute([]);

        return $tester;

    }

}
