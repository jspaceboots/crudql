<?php

namespace jspaceboots\crudql\GraphQL\Types;

use Folklore\GraphQL\Support\Type as GraphQLType;
use Illuminate\Support\Facades\Schema;
use GraphQL\Type\Definition\Type;

class AbstractType extends GraphQLType{


    public function fields() {
        $helper = app(\jspaceboots\crudql\Helpers\CrudHelper::class);
        $model = $helper->extractClassName(get_called_class());
        $table = $helper->getTableNameFromModelName($model);
        $columns = Schema::getColumnListing($helper->getTableNameFromModelName($model));
        $fields = [];
        foreach($columns as $column) {
            $type = $helper->getGraphQLTypeFromSchemaType(Schema::getColumnType($table, $column));
            if (!$type) {
                dd(Schema::getColumnType($table, $column));
            }
            if ($type == 'datetime') { continue; }
            $fields[$column] = [
                'type' => Type::{$type}(),
                'description' => ''
            ];
        }
        return $fields;
    }

    public function __call($name, $arguments) {
        var_dump($name);
        var_dump($arguments);
        dd('abstract resolver');
    }
}