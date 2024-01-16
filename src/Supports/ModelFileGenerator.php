<?php

namespace Betalabs\Engine\Supports;

use Illuminate\Support\Str;

class ModelFileGenerator
{
    public function generate(
        string $tableName,
        array $fields
    ) : void  {
        $className = Str::studly(Str::singular($tableName));
        $fileName = $className . '.php';
        $filePath = app_path("Models/{$fileName}");


        foreach ($fields as [$name, $type]) {
            $fieldMigrations[] = '"'.$name.'"';
        }
        $fillableFields = implode(",\n     ", $fieldMigrations);



        $template = <<<EOD
<?php

namespace App\\Models;

use Illuminate\\Database\\Eloquent\\Model;

class $className extends Model
{
    protected \$table = '$tableName';

    protected \$fillable = [
        $fillableFields
    ];
}

EOD;

        file_put_contents($filePath, $template);

    }
}
