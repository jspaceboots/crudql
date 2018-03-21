<?php

namespace jspaceboots\crudql\GraphQL\Queries;

use GraphQL;
use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Query;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Pluralizer;

class AbstractQuery extends Query {
    public function __construct() {

    }

    public function type() {
        $helper = app(\jspaceboots\crudql\Helpers\CrudHelper::class);
        return Type::listOf(GraphQL::type($helper->extractClassName(get_called_class())));
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
            if (substr($column, -3) == '_id') {
                //TODO: Complete relationship handling
                $column = substr($column, 0, strlen($column) - 3);
            }
            $args[$column] = [
                'name' => $column,
                'type' => Type::{$type}()
            ];
        }

        return $args;
        
    }

    public function resolve($root, $args) {
        $helper = app(\jspaceboots\crudql\Helpers\CrudHelper::class);
        $modelNamespace = config('crudql.namespaces.models');
        $model = "{$modelNamespace}\\{$helper->extractClassName(get_called_class())}";

        $qb = null;
        foreach($args as $field => $value) {
            if ($field == 'ids') { $field = 'id'; }

            if (is_array($value)) {
                foreach($value as $v) {
                    if ($qb == null) {
                        $qb = $model::where($field, $v);
                    } else {
                        $qb->orWhere($field, $v);
                    }
                }
            } else {
                if ($qb == null) {
                    $qb = $model::where($field, $value);
                } else {
                    $qb->where($field, $value);
                }
            }
        }

        return $qb->get();
    }

    public function __call($name, $arguments) {
        var_dump($name);
        var_dump($arguments);
        dd('abstract query resolver');
    }
}