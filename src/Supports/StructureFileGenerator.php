<?php

namespace Betalabs\Engine\Supports;

use Illuminate\Support\Str;

class StructureFileGenerator
{
    public function generate(
        string $tableName,
        array $fields
    ) : void  {
        $className = Str::studly($tableName);
        $fileName = $className . '.php';
        $filePath = app_path("Structures/{$fileName}");
        $rules = '';
        $columns = '';
        $labels = '';
        $boxFields = '';

        foreach ($fields as [$name, $type]) {
            $rules .= "new Rule('".$name."', ['".$type."']),\n          ";
            $columns .= "new Column('".$name."', true, true, true, true, true),\n           ";
            $labels .= "new Label('".$name."', trans(self::TRANS_PATH . '".$name."')),\n            ";
            $boxFields .= "'".$name."',\n                   ";
        }

        $template = <<<EOD
<?php

namespace App\\Structures;

use Betalabs\\StructureHelper\\Enums\\ExhibitionType;
use Betalabs\\StructureHelper\\Structures\\Component\Box;
use Betalabs\\StructureHelper\\Structures\\Component\Column;
use Betalabs\\StructureHelper\\Structures\\Component\Format;
use Betalabs\\StructureHelper\\Structures\\Component\Label;
use Betalabs\\StructureHelper\\Structures\\Component\Rule;
use Betalabs\\StructureHelper\\Structures\\Structure;
use Illuminate\\Contracts\Support\\Arrayable;

class $className extends Structure
{
   const TRANS_PATH = 'app/Structures/$className.';

     /**
     * @return array
     */
    public function rules(): array
    {
        return [
            $rules
        ];
    }


    /* Formats */
    public function formats(): array
    {
        return  [];
    }

    /**
     * @return array
     */
    public function columns(): array
    {
        return [
            $columns
        ];
    }

    /**
     * @return array
     */
    public function labels(): array
    {
        return [
            $labels
        ];
    }

    /**
     * @return array
     */
    public function boxes(): array
    {
        return [
            new Box(
                trans(self::TRANS_PATH . 'boxName'),
                trans(self::TRANS_PATH . 'boxName'),
                [
                    $boxFields
                ]
            )
        ];
    }

}

EOD;

        file_put_contents($filePath, $template);

    }
}
