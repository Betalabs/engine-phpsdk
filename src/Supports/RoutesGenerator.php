<?php

namespace Betalabs\Engine\Supports;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;


class RoutesGenerator
{
    public function generate(string $tableName, array $fields)
    {

        $className = ucfirst(Str::camel($tableName));
        $route = str_replace('_', '-', $tableName);
        $path = base_path('routes/api.php');
        $fileContents = File::get($path);

        // Novas rotas a serem adicionadas
        $newRoutes = "\n    // $className";
        $newRoutes .= "\n    Route::get('/$route', [\\App\\Http\\Controllers\\{$className}Controller::class, 'index']);";
        $newRoutes .= "\n    Route::get('/$route/structure', [\\App\\Http\\Controllers\\{$className}Controller::class, 'structure']);";
        $newRoutes .= "\n    Route::post('/$route', [\\App\\Http\\Controllers\\{$className}Controller::class, 'store']);";
        $newRoutes .= "\n    Route::get('/$route/{id}', [\\App\\Http\\Controllers\\{$className}Controller::class, 'show']);";
        $newRoutes .= "\n    Route::put('/$route/{id}', [\\App\\Http\\Controllers\\{$className}Controller::class, 'update']);";
        $newRoutes .= "\n    Route::delete('/$route/{id}', [\\App\\Http\\Controllers\\{$className}Controller::class, 'destroy']);";
        $newRoutes .= "\n    Route::get('/$route/{id}/action-menu', [\\App\\Http\\Controllers\\{$className}Controller::class, 'singleRecordActionMenu']);";
        $newRoutes .= "\n    Route::post('/$route/action-menu', [\\App\\Http\\Controllers\\{$className}Controller::class, 'multipleRecordsActionMenu']);";

        // Localiza o final do grupo de middleware 'auth:api'
        $pattern = "/(Route::middleware\('auth:api'\)->group\(function \(\) {)/";
        $replacement = "$1$newRoutes";

        // Atualiza o conteúdo do arquivo
        $updatedContents = preg_replace($pattern, $replacement, $fileContents);

        // Escreve de volta no arquivo
        File::put($path, $updatedContents);


    }
}
