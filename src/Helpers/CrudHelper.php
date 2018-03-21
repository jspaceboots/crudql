<?php

namespace jspaceboots\crudql\Helpers;

use Illuminate\Support\Pluralizer;

class CrudHelper {

    /*
    public function getGraphQlTypeFromDbType() {
        $types
    }
    */

    public function getModelFromClass($classname) {
        $rtrims = ['Repository', 'Factory'];
        $modelClass = substr($classname, strrpos($classname, '\\') + 1);

        foreach($rtrims as $trim) {
            $modelClass = str_replace($trim, '', $modelClass);
        }

        return $modelClass;
    }

    public function getTableNameFromModelName($modelName) {
        $normalizationExceptions = config('crudql.tableNormalizationExceptions');
        if (in_array($modelName, $normalizationExceptions)) {
            return $normalizationExceptions[array_search($modelName, $normalizationExceptions)];
        }

        $modelTable = ltrim(strtolower(join('_', preg_split('/(?=[A-Z])/', $modelName))), '_');
        $modelTable = Pluralizer::plural($modelTable);

        return $modelTable;
    }

    public function getModelNameFromRouteName($routeName) {
        $model = str_replace(' ', '', ucwords(str_replace('_', ' ', $routeName)));
        $model = Pluralizer::singular($model);

        return $model;
    }

    public function extractClassName($fullyQualifiedName) {
        $rtrims = ['Query', 'Type', 'Mutation'];
        $class = substr($fullyQualifiedName, strrpos($fullyQualifiedName, '\\') + 1);

        foreach($rtrims as $trim) {
            $class = str_replace($trim, '', $class);
        }

        return $class;
    }

    public function getGraphQLTypeFromSchemaType($graphQlType) {
        switch($graphQlType) {
            case 'integer':  return 'int';
            case 'string':   return 'string';
            case 'datetime': return 'datetime';
            default: return false;
        }
    }

    public function toCamelCase($string) {
        return str_replace(' ', '', ucwords(str_replace('_', '', $string)));
    }
}