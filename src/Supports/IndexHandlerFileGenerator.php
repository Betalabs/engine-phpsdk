<?php

namespace Betalabs\Engine\Supports;

use Illuminate\Support\Str;

class IndexHandlerFileGenerator
{
    public function generate(
        string $tableName,
        array $fields
    ) : void  {
        $modelName = Str::studly(Str::singular($tableName));
        $className = Str::studly($tableName);
        $fileName = $className . '.php';
        $filePath = app_path("Services/{$className}/IndexHandler");

        if (!is_dir($filePath)) {
            // Cria o diretório e também cria os diretórios "pais" necessários, se eles não existirem
            mkdir($filePath, 0777, true);
        }


        $template = <<<EOD
<?php

namespace App\\Services\\$className\\IndexHandler;

use App\\Models\\$modelName as Model$modelName;
use Betalabs\\EngineApiHandler\\ApiHandler\\AbstractIndexHandler;
use Illuminate\\Database\Eloquent\\Builder;
use Illuminate\\Support\Facades\\Auth;

class $className extends AbstractIndexHandler
{
        /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function buildQuery(): Builder
    {
        return Model$modelName::query()
            ->whereNull('deleted_at')
            ->where('tenant_id', '=', Auth::id());
    }

}

EOD;

        file_put_contents($filePath.'/'.$fileName, $template);

    }
}
