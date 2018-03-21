

namespace {{ltrim(rtrim(config('crudql.namespaces.repositories'), '\\'), '\\')}};

use jspaceboots\crudql\Repositories\AbstractRepository;

class {{$model}}Repository extends AbstractRepository {

}