<?php

namespace jspaceboots\crudql\GraphQL\Mutations;
use GraphQL\Type\Definition\Type;
use GraphQL;
use Folklore\GraphQL\Support\Mutation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Pluralizer;

class AbstractMutation extends Mutation {
    protected $attributes = [];
    private $model = null;

    public function type() {
        return GraphQL::type($this->model);
    }

    public function args() {
        // TODO: Abstract into function, remove from here and abstract type
        $helper = app(\jspaceboots\crudql\Helpers\CrudHelper::class);
        $model = $helper->extractClassName(get_called_class());
        $table = $helper->getTableNameFromModelName($model);
        $columns = Schema::getColumnListing($helper->getTableNameFromModelName($model));
        $args = [];

        foreach($columns as $column) {
            $type = $helper->getGraphQLTypeFromSchemaType(Schema::getColumnType($table, $column));
            if (!$type) {
                dd(Schema::getColumnType($table, $column));
            }
            if ($type == 'datetime') { continue; }

            if ($column == 'id') {
                $pluralColumn = Pluralizer::plural($column);

                $args[$pluralColumn] = [
                    'name' => $pluralColumn,
                    'type' => Type::listOf(Type::{$type}())
                ];
            }

            $args[$column] = [
                'name' => $column,
                'type' => Type::{$type}()
            ];
        }

        return $args;
    }

    public function __construct() {
        $helper = app(\jspaceboots\crudql\Helpers\CrudHelper::class);
        $class = $helper->extractClassName(get_called_class());
        $this->attributes['name'] = $class;
        $this->model = $class;
    }

    public function __call($name, $arguments) {
        dd($name);
        var_dump($name);
        //var_dump($arguments);
        //dd('mutation');
    }
}