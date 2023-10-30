<?php

namespace Betalabs\Engine\Supports;

use Illuminate\Support\Str;

class ActionMenuFileGenerator
{
    public function generate(
        string $tableName,
        array $fields
    ) : void  {
        $modelName = Str::studly(Str::singular($tableName));
        $className = Str::studly($tableName);
        $lower = Str::lower($tableName);
        $fileName = $className . '.php';
        $filePath = app_path("Services/{$className}");

        if (!is_dir($filePath)) {
            // Cria o diretório e também cria os diretórios "pais" necessários, se eles não existirem
            mkdir($filePath, 0777, true);
        }

        $template = <<<EOD
<?php

namespace App\\Services\\$className;

use App\\Models\\$modelName as Model$modelName;
use App\\Structures\ActionMenu\\Action\\$className\\Delete;
use App\\Structures\ActionMenu\\Action\\$className\\BatchDelete;
use App\\Structures\ActionMenu\\Action\\$className\\Update;
use App\\Structures\ActionMenu\\Menu\\$className;
use Betalabs\\StructureHelper\\Entities\\EntityCollection;

class ActionMenu
{
   /**
     * @param string|null \$Id$lower
     * @return \Betalabs\StructureHelper\Entities\EntityCollection
     */
    public function retrieve(string \$Id$lower = null): EntityCollection
    {
        if (\$Id$lower === null) {
            return \$this->multiple();
        }

        return \$this->single(\$Id$lower);
    }

    /**
     * @return \Betalabs\StructureHelper\Entities\EntityCollection
     */
    private function multiple()
    {
        return (new $className)
            ->add(new BatchDelete("/$lower/delete"));
    }

    /**
     * @param string \$Id$lower
     * @return \Betalabs\StructureHelper\Entities\EntityCollection
     */
    private function single(string \$Id$lower)
    {
        return (new $className)
            ->add(new Update("/$lower/update/{\$Id$lower}"))
            ->add(new Delete("/$lower/{\$Id$lower}"));
    }

}

EOD;


        file_put_contents($filePath.'/'.$fileName, $template);
        $filePathStructure = app_path("Structures/ActionMenu/Action/{$className}");
        if (!is_dir($filePathStructure)) {
            // Cria o diretório e também cria os diretórios "pais" necessários, se eles não existirem
            mkdir($filePathStructure, 0777, true);
        }
        $templateDelete = <<<EOD
<?php

namespace App\Structures\ActionMenu\Action\\$className;

use App\Structures\ActionMenu\Action\AbstractStructure;
use Betalabs\StructureHelper\Enums\ActionType;
use Betalabs\StructureHelper\Enums\HttpMethod;

class Delete extends AbstractStructure
{
    /**
     * Menu label
     *
     * @return string
     */
    public function label(): string
    {
        return trans('app/Structures/ActionMenu/Action/$className.delete');
    }

    /**
     * @return \Betalabs\StructureHelper\Enums\HttpMethod
     */
    public function httpMethod(): HttpMethod
    {
        return new HttpMethod(HttpMethod::DELETE);
    }

    /**
     * @return \Betalabs\StructureHelper\Enums\ActionType
     */
    public function type(): ActionType
    {
        return new ActionType(ActionType::AJAX);
    }

    /**
     * @return string|null
     */
    public function redirectUrl(): ?string
    {
        return '';
    }

}
EOD;

        file_put_contents($filePathStructure.'/Delete.php', $templateDelete);


        $templateUpdate = <<<EOD
<?php

namespace App\Structures\ActionMenu\Action\\$className;

use App\Structures\ActionMenu\Action\AbstractStructure;
use Betalabs\StructureHelper\Enums\ActionType;
use Betalabs\StructureHelper\Enums\HttpMethod;

class Update extends AbstractStructure
{
    /**
     * Menu label
     *
     * @return string
     */
    public function label(): string
    {
        return trans('app/Structures/ActionMenu/Action/$className.update');
    }


    /**
     * @return \Betalabs\StructureHelper\Enums\HttpMethod
     */
    public function httpMethod(): HttpMethod
    {
        return new HttpMethod(HttpMethod::PUT);
    }

    /**
     * @return \Betalabs\StructureHelper\Enums\ActionType
     */
    public function type(): ActionType
    {
        return new ActionType(ActionType::UPDATE);
    }

    /**
     * @return string|null
     */
    public function redirectUrl(): ?string
    {
        return '';
    }

}
EOD;

        file_put_contents($filePathStructure.'/Update.php', $templateUpdate);


        $templateBatchDelete = <<<EOD
<?php

namespace App\Structures\ActionMenu\Action\\$className;

use App\Structures\ActionMenu\Action\AbstractStructure;
use Betalabs\StructureHelper\Enums\ActionType;
use Betalabs\StructureHelper\Enums\HttpMethod;

class BatchDelete extends AbstractStructure
{
    /**
     * Menu label
     *
     * @return string
     */
    public function label(): string
    {
        return trans('app/Structures/ActionMenu/Action/$className.delete');
    }

    /**
     * @return \Betalabs\StructureHelper\Enums\HttpMethod
     */
    public function httpMethod(): HttpMethod
    {
        return new HttpMethod(HttpMethod::POST);
    }

    /**
     * @return \Betalabs\StructureHelper\Enums\ActionType
     */
    public function type(): ActionType
    {
        return new ActionType(ActionType::AJAX);
    }

    /**
     * @return string|null
     */
    public function redirectUrl(): ?string
    {
        return '';
    }

}
EOD;

        file_put_contents($filePathStructure.'/BatchDelete.php', $templateBatchDelete);




    }
}
