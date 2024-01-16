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
use App\\Structures\\$entity as Structure$model;
use App\\Services\\$entity\\IndexHandler\\$entity as IndexHandler;
use App\\Services\\$entity\\ActionMenu;
use App\\Http\\Resources\\$entity as Resource$entity;
use Illuminate\Http\Request;

class $className extends Controller
{
    /**
     * List all records from $tableName
     * @param IndexHandler \$indexHandler
     */
    public function index(IndexHandler \$indexHandler)
    {
        return \$indexHandler->execute();
    }
    
    /**
     * Load $tableName structure
     * @param Structure$model \$structure
     * @return Structure$model
     */
    public function structure(Structure$model \$structure) : Structure$model
    {
        return \$structure;
    }

    /**
     * Create a new $tableName record
     * @param Request \$request
     * @return $model
     */
    public function store(Request \$request) : $model
    {
        return $model::create(\$request->all());
    }

    /**
     * Show a $tableName record
     * @param $model \$id
     * @return $model
     */
    public function show($model \$id) : Resource$entity
    {
        return new Resource$entity(\$id);
    }

    /**
     * Update a $tableName record
     * @param Request \$request
     * @param $model \$id
     * @return $model
     */
    public function update(Request \$request, $model \$id) : $model
    {
        \$record = $model::findOrFail(\$id);
        \$record->fill(\$request->all());
        \$record->save();

        return \$record;
    }

    /**
     * Delete a $tableName record
     * @param $model \$id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($model \$id)
    {
        \$record = $model::findOrFail(\$id);
        \$record->delete();

        return response()->json(['message' => 'Registro deletado com sucesso']);
    }
    
    
    /**
     * @param ActionMenu \$service
     * @param int \$id
     * @return EntityCollection
     */
    public function singleRecordActionMenu(
        ActionMenu \$service,
        int \$id
    ) : EntityCollection {
        return \$service->retrieve(\$id);
    }

    /**
     * @param ActionMenu \$service
     * @return EntityCollection
     */
    public function multipleRecordsActionMenu(
        ActionMenu \$service,
    ) : EntityCollection {
        return $\service->retrieve();
    }    
    
    
}

EOD;

        // Especifica o caminho para a pasta de controllers
        $filePath = app_path("Http/Controllers/{$className}.php");

        file_put_contents($filePath, $template);

    }
}
