<?php

namespace jspaceboots\crudql\Commands;

use Illuminate\Console\Command;
use jspaceboots\crudql\Helpers\CrudHelper;
use Illuminate\Support\Pluralizer;

class ScaffoldCommand extends Command
{
    protected $signature = 'crudql:scaffold';
    protected $description = 'Walks you through the process of scaffolding a new entity';
    private $fields = [];
    private $validators = [];
    private $relations = [];

    public function __construct()
    {
        parent::__construct();
        $this->helper = new CrudHelper();
    }

    public function handle()
    {

        $namespaces = config('crudql.namespaces');
        $entity = ucfirst($this->ask('What is the name of your entity? (Singular, UpperCaseWord, CamelCase)'));
        $createGuiCrud = false;
        $createGraphQlCrud = false;

        if (config('crudql.interfaces.html.enabled')) {
            $createGuiCrud = $this->confirm('Would you like to expose your entity through the admin GUI?');
        }
        if (config('crudql.interfaces.graphql.enabled')) {
            $createGraphQlCrud = $this->confirm('Would you like to expose your entity through GraphQL?');
        }
        $createUnitTest = $this->confirm("You need a unit test?");
        $createIntegrationTest = $this->confirm("How about an integration test boss?");

        $model = $entity;
        $table = $this->helper->getTableNameFromModelName($entity);
        $timestamp = (new \DateTime())->format('Y_m_d_u');
        $this->info('This will create:');
        $this->output->newLine(1);

        $this->info('Scaffolding');
        $this->table(['Type', 'Path'], [
            ['Model', "{$namespaces['models']}\\$entity"],
            ['Repository', "{$namespaces['repositories']}\\{$entity}Repository"],
            ['Transformer', "{$namespaces['transformers']}\\{$entity}Transformer"],
            ['Migration', "database/migrations/{$timestamp}_create_{$table}_table.php"],
            ['Unit test', $createUnitTest ? "tests/Unit/{$entity}Test.php" : "N/A"],
            ['Integration test', $createIntegrationTest ? "tests/Integration/{$entity}Test.php" : "N/A"]
        ]);

        if ($createGraphQlCrud) {
            $this->output->newLine(1);
            $this->info('GraphQL CRUD Scaffolding');
            $this->table(['Type', 'Path'], [
                ['GraphQL Type', "{$namespaces['types']}\\{$entity}Type"],
                ['GraphQL Query', "{$namespaces['queries']}\\{$entity}Query"],
                ['GraphQL Mutation', "{$namespaces['mutations']}\\{$entity}Mutation"],
            ]);
        }

        if ($createGuiCrud) {
            $this->output->newLine(1);
            $this->info('Admin UI Routes');
            $this->table(['Verb', 'Path', 'Description'], [
                ['GET', "/crud/{$table}", "Read {$entity}"],
                ['GET', "/crud/{$table}/new", "Return a form to POST a new $entity"],
                ['GET', "/crud/{$table}/{id}", "Return a form to PATCH an existing $entity"],
                ['POST', "/crud/{$table}", "Creates a {$entity} on form submission"],
                ['PATCH', "/crud/{$table}/{id}", "Updates a {$entity} on form submission"],
                ['DELETE', "/crud/{$table}/{id}", "Deletes a {$entity} on form submission"]
            ]);
        }

        $continue = $this->confirm('Look good?');

        if ($continue) {
            $steps = 6;
            if ($createGraphQlCrud) { $steps = $steps + 1; }
            if ($createUnitTest) { $steps = $steps + 1; }
            if ($createIntegrationTest) { $steps = $steps + 1; }
            if ($createGuiCrud) { $steps = $steps + 1; }

            $bar = $this->barSetup($this->output->createProgressBar($steps));
            $bar->start();
            $model = $entity;

            $this->info('Creating model...');
            $modelContents = '<?php ' . view('CrudQL::Scaffolding.model', ['model' => $model, 'table' => $table]);
            $modelDir = $this->getPathFromNamespace(config('crudql.namespaces.models'));
            if (!file_exists($modelDir)) {
                mkdir($modelDir);
            }
            $modelFile = $modelDir . "/{$model}.php";
            file_put_contents($modelFile, $modelContents);
            $bar->advance();

            $this->info('Creating model repository ...');
            $repo = '<?php ' . view('CrudQL::Scaffolding.repository', ['model' => $model]);
            $repoDir = $this->getPathFromNamespace(config('crudql.namespaces.repositories'));
            if (!file_exists($repoDir)) {
                mkdir($repoDir);
            }
            file_put_contents($repoDir . "/{$model}Repository.php", $repo);
            $bar->advance();

            $this->info('Creating model transformer ...');
            $repo = '<?php ' . view('CrudQL::Scaffolding.transformer', ['model' => $model]);
            $repoDir = $this->getPathFromNamespace(config('crudql.namespaces.transformers'));
            if (!file_exists($repoDir)) {
                mkdir($repoDir);
            }
            file_put_contents($repoDir . "/{$model}Transformer.php", $repo);
            $bar->advance();

            if ($createUnitTest) {
                $this->info("Creating unit test...");
                $unitTest = '<?php' . PHP_EOL . 'namespace Tests\\Unit;' . PHP_EOL . view('CrudQL::Scaffolding.test', ['model' => $model]);
                $unitTestDir = lcfirst($this->getPathFromNamespace(config('crudql.namespaces.unittests')));
                if (!file_exists($unitTestDir)) {
                    mkdir($unitTestDir, 0777, true);
                }
                file_put_contents($unitTestDir . "/{$model}Test.php", $unitTest);
                $bar->advance();
            }

            if ($createIntegrationTest) {
                $this->info("Creating integration test...");
                $intTest = '<?php' . PHP_EOL . 'namespace Tests\\Integration;' . PHP_EOL . view('CrudQL::Scaffolding.test', ['model' => $model]);
                $intTestDir = $this->getPathFromNamespace(config('crudql.namespaces.integrationtests'));
                if (!file_exists($intTestDir)) {
                    mkdir($intTestDir, 0777, true);
                }
                file_put_contents($intTestDir . "/{$model}Test.php", $intTest);
                $bar->advance();
            }

            $datetime = (new \DateTime())->format('Y_m_d_u');
            $migrationFile = "database/migrations/{$datetime}_create_" . strtolower($table) . "_table.php";
            $this->info('Creating migration...');
            $migrationContents = '<?php ' . view('CrudQL::Scaffolding.migration', ['model' => Pluralizer::plural($model), 'table' => $table]);
            file_put_contents($migrationFile, $migrationContents);
            $bar->advance();

            if ($createGraphQlCrud) {
                $this->info("Creating GraphQL Type...");
                $type = '<?php ' . view('CrudQL::Scaffolding.type', ['model' => $model]);
                $typeDir = $this->getPathFromNamespace(config('crudql.namespaces.types'));
                if (!file_exists($typeDir)) {
                    mkdir($typeDir, 0777, true);
                }
                file_put_contents($typeDir . "/{$model}Type.php", $type);
                $bar->advance();

                $this->info("Creating GraphQL Query...");
                $query = '<?php ' . view('CrudQL::Scaffolding.query', ['model' => $model]);
                $queryDir = $this->getPathFromNamespace(config('crudql.namespaces.queries'));
                if (!file_exists($queryDir)) {
                    mkdir($queryDir);
                }
                file_put_contents($queryDir . "/{$model}Query.php", $query);
                $bar->advance();

                $this->info("Creating GraphQL Mutation...");
                $query = '<?php ' . view('CrudQL::Scaffolding.mutation', ['model' => $model]);
                $queryDir = $this->getPathFromNamespace(config('crudql.namespaces.mutations'));
                if (!file_exists($queryDir)) {
                    mkdir($queryDir);
                }
                file_put_contents($queryDir . "/{$model}Mutation.php", $query);
                $bar->advance();

                $this->info('Exposing Type, Mutation & Query to GraphQL...');
                $config = file_get_contents("config/graphql.php");
                $configArray = explode(PHP_EOL, $config);
                $queryPos = array_search("            'query' => [", $configArray);
                $mutationPos = array_search("            'mutation' => [", $configArray);;
                $typePos = array_search("    'types' => [", $configArray);

                $newQueryRow = "'$model' => '" . config('crudql.namespaces.queries') . "\\{$model}Query',";
                $newMutationRow = "'$model' => '" . config('crudql.namespaces.mutations') . "\\{$model}Mutation',";
                $newTypeRow = "'$model' => '" . config('crudql.namespaces.types') . "\\{$model}Type',";

                array_splice($configArray, $queryPos + 1, 0, $newQueryRow);
                array_splice($configArray, $mutationPos + 2, 0, $newMutationRow);
                array_splice($configArray, $typePos + 3, 0, $newTypeRow);
                file_put_contents('config/graphql.php', implode(PHP_EOL, $configArray));

                $config = file_get_contents("config/crudql.php");
                $configArray = explode(PHP_EOL, $config);
                $result = array_search("    'routing' => [", $configArray);
                $newRow = "        '" . strtolower($model) . "' => [],";
                array_splice($configArray, $result + 1, 0, $newRow);
                file_put_contents('config/crudql.php', implode(PHP_EOL, $configArray));
            }

            $bar->advance();
            $bar->finish();
            $this->info('Entity scaffolded successfully!');
            $this->output->newLine(2);
            $bar = null;

            $this->askAboutMigrationFields();

            if (count($this->fields)) {
                $this->writeFieldsToMigration($migrationFile);
            }

            /* TODO: complete validation and relation scaffolding
            if (count($this->validators)) {
                $this->writeValidatorsToModel($modelFile);
            }

            if (count($this->relations)) {
                $this->writeRelationsToModel($modelFile);
            }
            */
        }
    }

    private function getPathFromNamespace($namespace)
    {
        return substr(str_replace('App', 'app', str_replace('\\', '/', $namespace)), 1);
    }

    private function barSetup($bar)
    {

        $bar->setBarCharacter('<comment>=</comment>');
        $bar->setEmptyBarCharacter('-');
        $bar->setProgressCharacter('>');
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% ');
        return $bar;
    }

    private function askAboutMigrationFields()
    {
        $continue = $this->confirm('Would you like to define fields for your entity?');
        $nextQuestion = $continue;
        while ($continue) {
            $fieldName = $this->ask("What's the fields name?");

            $fieldType = false;
            while (!$fieldType) {
                $fieldType = $this->anticipate("What's the fields type?", config('crudql.fieldTypes'));
            }

            $this->fields[$fieldName] = $fieldType;

            $continue = $this->confirm('Would you like to define another field?');
        }

        /*if ($nextQuestion) {
            $this->askAboutValidators();
        }*/
    }

    private function askAboutValidators()
    {
        $continue = $this->confirm('Would you like to define validators for your entity?');
        while($continue) {
            $field = $this->choice("For which field?", array_keys($this->fields));
            if (!array_key_exists($field, $this->validators)) {
                $this->validators[$field] = [];
            }

            $validator = $this->anticipate("Which kind of validator?", array_keys(config('crudql.validators')));
            $this->validators[$field][] = $validator;

            $continue = $this->confirm("Would you like to add another validator?");
        }
    }

    /*
    private function askAboutRelations() {
        $continue = $this->confirm("Would you like to define any relations for your entity?");
        while ($continue) {
            $field = $this->ask("What model does it relate to?");
            $type = $this->choice("Which type of relationship is it?", ['1:1', '1:M', 'M:N', 'M:1']);

        }
    }
    */

    private function writeFieldsToMigration($filename) {
        $migration = file_get_contents($filename);
        $migrationArray = explode(PHP_EOL, $migration);
        $pos = false;
        foreach($migrationArray as $index => $line) {
            if (strpos(trim($line), "('id');")) {
                $pos = $index;
                break;
            }
        }

        if ($pos) {
            foreach($this->fields as $type => $name) {
                array_splice($migrationArray, $pos + 1, 0, "\$table->{$name}('{$type}');");
                $pos = $pos + 1;
            }
        }
        file_put_contents($filename, implode($migrationArray, PHP_EOL));
    }

    private function writeValidatorsToModel($filename) {
        $model = file_get_contents($filename);
        $modelArray = explode(PHP_EOL, $model);
        $pos = false;

        foreach($modelArray as $index => $line) {
            if (strpos($line, "const validators = [") !== false) {
                $pos = $index;
                break;
            }
        }

        if ($pos) {
            foreach($this->validators as $field => $validators) {
                $strValidators = implode('|', $validators);
                array_splice($modelArray, $pos + 1, 0, "'{$field}' => '{$strValidators}'");
                $pos = $pos + 1;
            }
        }
        file_put_contents($filename, implode($modelArray, PHP_EOL));
    }

    private function writeRelationsToModel() {

    }
}
