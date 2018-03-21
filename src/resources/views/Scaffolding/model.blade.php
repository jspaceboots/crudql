

namespace {{ltrim(rtrim(config('crudql.namespaces.models'), '\\'), '\\')}};

use Illuminate\Database\Eloquent\Model;
use jspaceboots\crudql\Traits\UuidTrait;

class {{$model}} extends Model
{

    use UuidTrait;
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = '{{$table}}';

    const validators = [
    ];
    const relations = [
    ];
}
