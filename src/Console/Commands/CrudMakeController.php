<?php

namespace Betalabs\Engine\Console\Commands;

use Illuminate\Console\Command;

use Betalabs\Engine\Supports\MigrationFileGenerator;
use Betalabs\Engine\Supports\ModelFileGenerator;
use Betalabs\Engine\Supports\ControllerFileGenerator;

class CrudMakeController extends Command
{
    protected $signature = 'crud-maker:command';
    protected $description = 'Create crud controller / model / tests / routes / requests';

    public function handle(
        MigrationFileGenerator $migrationFileGenerator,
        ModelFileGenerator $modelFileGenerator,
        ControllerFileGenerator $controllerGenerator
    ) : void {
        $this->info('Iniciando comando de criação de arquivos...');

        $tableName = $this->ask('Informe o nome da tabela:');

        $fields = [];

        // Loop até que o usuário dê um "comando final"
        while (true) {
            // Pergunte ao usuário por uma entrada
            $input = $this->ask('Por favor, insira o nome da tabela e o tipo, separado por virgula (Exemplos: "name,string" ou ).
            Tipos tratados:
            -> integer
            -> unsignedInteger
            -> date
            -> dateTime
            -> string
            -> text
            (ou digite "sair" para terminar):');

            // Verifique se o usuário quer sair
            if (strtolower($input) === 'sair') {
                break;
            }

            // Caso contrário, armazene a entrada
            $fields[] = explode(',', $input);
        }

        // Aqui você pode processar os dados
        $this->info('Você inseriu os seguintes dados:');
        foreach ($fields as $data) {
            $this->line($data);
        }

        $migrationFileGenerator->generate($tableName, $fields);
        $this->info('Migration finished');
        $modelFileGenerator->generate($tableName, $fields);
        $this->info('Model finished');
        $controllerGenerator->generate($tableName, $fields);
        $this->info('Controller finished');

    }













}