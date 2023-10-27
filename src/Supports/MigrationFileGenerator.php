<?php

namespace Betalabs\Engine\Supports;

use Illuminate\Support\Str;

class MigrationFileGenerator
{
    public function generate(
        string $tableName,
        array $fields
    ) : void {
        $datePrefix = date('Y_m_d_His');
        $className = 'Create' . Str::studly($tableName) . 'Table';
        $fileName = $datePrefix . '_' . Str::snake($className) . '.php';
        $filePath = database_path('migrations/' . $fileName);

        $fieldMigrations = [];
        foreach ($fields as [$name, $type]) {
            $fieldMigrations[] = "\$table->$type('$name');";
        }
        $fieldMigrationsStr = implode("\n            ", $fieldMigrations);

        $template = <<<EOD
<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

class $className extends Migration
{
    public function up()
    {
        Schema::create('$tableName', function (Blueprint \$table) {
            \$table->id();
            $fieldMigrationsStr
            \$table->timestamps();
            \$table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('$tableName');
    }
}

EOD;

        file_put_contents($filePath, $template);
        
    }
}
