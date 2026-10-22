<?php

namespace Betalabs\Engine\Tests\Supports;

use Betalabs\Engine\Supports\ModelFileGenerator;
use Betalabs\Engine\Tests\TestCase;
use Illuminate\Container\Container;
use Illuminate\Foundation\Application;

/**
 * Generates the model file inside a temporary Laravel base path
 */
class ModelFileGeneratorTest extends TestCase
{

    /** @var string */
    protected $basePath;

    protected function setUp(): void
    {
        $this->basePath = sys_get_temp_dir() . '/engine-phpsdk-' . uniqid();
        mkdir($this->basePath . '/app/Models', 0777, true);

        new Application($this->basePath);
    }

    public function tearDown(): void
    {
        array_map('unlink', glob($this->basePath . '/app/Models/*.php'));
        rmdir($this->basePath . '/app/Models');
        rmdir($this->basePath . '/app');
        rmdir($this->basePath);

        Container::setInstance(null);

        parent::tearDown();
    }

    public function testModelWithFieldsHasFillableAttributes()
    {

        (new ModelFileGenerator())->generate('order_items', [['name', 'string'], ['quantity', 'integer']]);

        $content = file_get_contents($this->basePath . '/app/Models/OrderItem.php');

        $this->assertStringContainsString('class OrderItem extends Model', $content);
        $this->assertStringContainsString("protected \$table = 'order_items';", $content);
        $this->assertStringContainsString('"name"', $content);
        $this->assertStringContainsString('"quantity"', $content);

    }

    public function testModelWithoutFieldsIsGeneratedWithEmptyFillable()
    {

        (new ModelFileGenerator())->generate('orders', []);

        $content = file_get_contents($this->basePath . '/app/Models/Order.php');

        $this->assertStringContainsString('class Order extends Model', $content);
        $this->assertMatchesRegularExpression('/protected \$fillable = \[\s*\];/', $content);

    }

}
