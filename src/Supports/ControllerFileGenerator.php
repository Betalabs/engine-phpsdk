<?php

namespace Betalabs\Engine\Supports;

use Illuminate\Support\Str;

class ControllerFileGenerator
{
    public function generate(string $tableName, array $fields)
    {

        $className = ucfirst(Str::camel($tableName)) . 'Controller';
        $model =  Str::studly(Str::singular($tableName));
        $entity = Str::studly(($tableName));

        $template = <<<EOD
<?php

namespace App\Http\Controllers;

use App\Models\\$model;
use App\\Structures\\$tableName as Structure$model;
use App\\Services\\$entity\\IndexHandler\\$className as IndexHandler;
use Illuminate\Http\Request;

class $className extends Controller
{
    /**
     * @param IndexHandler \$indexHandler
     */
    public function index(IndexHandler \$indexHandler)
    {
        return \$indexHandler->execute();
    }
    /**
     * @param Structure$model \$structure
     * @return Structure$model
     */
    public function structure(Structure$model \$structure) : Structure$model
    {
        return \$structure;
    }


    public function store(Request \$request)
    {
        return $model::create(\$request->all());
    }

    public function show($model \$id)
    {
        return $model::findOrFail(\$id);
    }

    public function update(Request \$request, $model \$id)
    {
        \$record = $model::findOrFail(\$id);
        \$record->fill(\$request->all());
        \$record->save();

        return \$record;
    }

    public function destroy($model \$id)
    {
        \$record = $model::findOrFail(\$id);
        \$record->delete();

        return response()->json(['message' => 'Registro deletado com sucesso']);
    }
}

EOD;

        // Especifica o caminho para a pasta de controllers
        $filePath = app_path("Http/Controllers/{$className}.php");

        file_put_contents($filePath, $template);

    }
}
