<?php



use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersSorteioTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'users_sorteio';

    /**
     * Run the migrations.
     * @table users_sorteio
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            
            $table->increments('id');
            $table->string('status', 1)->nullable()->default(null);
            $table->unsignedInteger('users_id');
            $table->unsignedInteger('sorteios_id');
            $table->unsignedInteger('pesquisa_estabelecimento_id');

            $table->index(["users_id"], 'fk_users_sorteio_users1_idx');

            $table->index(["sorteios_id"], 'fk_users_sorteio_sorteios1_idx');

            $table->index(["pesquisa_estabelecimento_id"], 'fk_users_sorteio_pesquisa_estabelecimento1_idx');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->tableName);
    }
}
