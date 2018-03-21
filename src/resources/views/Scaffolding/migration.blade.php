

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Create{{$model}}Table extends Migration
{
    public function up() {
        Schema::create('{{$table}}', function (Blueprint $table) {
            $table->uuid('id');
            $table->primary('id');

            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('{{$table}}');
    }
}
