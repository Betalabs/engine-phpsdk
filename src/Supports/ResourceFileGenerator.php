<?php

namespace Betalabs\Engine\Supports;

use Illuminate\Support\Str;

class ResourceFileGenerator
{
    public function generate(string $tableName, array $fields)
    {

        $className = ucfirst(Str::camel($tableName));
        $model =  Str::studly(Str::singular($tableName));
        $entity = Str::studly(($tableName));

        $list = '';
        foreach ($fields as [$name, $type]) {
            $list .= "'$name' => \$this->$name,\n            ";
        }

        $template = <<<EOD
<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class $className extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request \$request
     *
     * @return array
     */
    public function toArray(\$request)
    {
        return [
            $list
        ];
    }
    
}

EOD;

        // Especifica o caminho para a pasta de controllers
        $filePath = app_path("Http/Resources/{$className}.php");

        file_put_contents($filePath, $template);

    }
}
