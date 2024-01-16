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
        $ucfirst = Str::ucfirst($lower);
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
     * @param string|null \$id$ucfirst
     * @return \Betalabs\StructureHelper\Entities\EntityCollection
     */
    public function retrieve(string \$id$ucfirst = null): EntityCollection
    {
        if (\$id$ucfirst === null) {
            return \$this->multiple();
        }

        return \$this->single(\$id$ucfirst);
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
     * @param string \$id$ucfirst
     * @return \Betalabs\StructureHelper\Entities\EntityCollection
     */
    private function single(string \$id$ucfirst)
    {
        return (new $className)
            ->add(new Update("/$lower/update/{\$id$ucfirst}"))
            ->add(new Delete("/$lower/{\$id$ucfirst}"));
    }

}

EOD;

        file_put_contents($filePath.'/ActionMenu.php', $template);

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


        $templateActionMenuMenu = <<<EOD
<?php

namespace App\Structures\ActionMenu\Menu;

use Betalabs\StructureHelper\Structures\ActionMenu\Menu\Structure;

class $className extends Structure
{
    
}

EOD;

        file_put_contents(app_path("Structures/ActionMenu/Action/Menu/").$className.'.php', $templateActionMenuMenu);


        $templateAbstractStructure = <<<EOD
<?php

namespace App\Structures\ActionMenu\Action;

use Betalabs\LaravelHelper\Helpers\Engine\Wormhole;
use Betalabs\LaravelHelper\Helpers\Engine\UrlMaker;
use Betalabs\StructureHelper\Structures\ActionMenu\Action\Structure;


abstract class AbstractStructure extends Structure
{
    /**
     * AbstractStructure constructor.
     *
     * @param string \$uri
     */
    public function __construct(
        readonly private string \$uri
    ) { }

    /**
     * App endpoint
     *
     * @return string
     */
    public function endpoint(): string
    {
        return Wormhole::makeEndpoint(\$this->uri, 'api');
    }

    /**
     * Returns the complete endpoint
     *
     * @return string
     */
    public function completeEndpoint(): string
    {
        return UrlMaker::makeUrl(\$this->endpoint());
    }
}


EOD;

        file_put_contents(app_path("Structures/ActionMenu/Action/").'AbstractStructure.php', $templateAbstractStructure);


    }
}
