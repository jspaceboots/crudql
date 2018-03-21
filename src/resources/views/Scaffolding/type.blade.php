

namespace {{ltrim(rtrim(config('crudql.namespaces.types'), '\\'), '\\')}};

use jspaceboots\crudql\GraphQL\Types\AbstractType;

class {{$model}}Type extends AbstractType {
    protected $attributes = [
        'name' => '',
        'description' => ''
    ];
}