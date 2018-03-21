

namespace App\GraphQL\Queries;

use jspaceboots\crudql\GraphQL\Queries\AbstractQuery;

class {{$model}}Query extends AbstractQuery {
    protected $attributes = [
        'name' => '{{strtolower($model)}}'
    ];


}