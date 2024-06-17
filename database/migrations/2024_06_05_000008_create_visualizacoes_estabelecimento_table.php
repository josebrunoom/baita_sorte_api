<?php



use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisualizacoesEstabelecimentoTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'visualizacoes_estabelecimento';

    /**
     * Run the migrations.
     * @table visualizacoes_estabelecimento
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            
            $table->increments('id');
            $table->unsignedInteger('users_id');
            $table->unsignedInteger('estabelecimentos_id');

            $table->index(["users_id"], 'fk_users_sorteio_users1_idx');

            $table->index(["estabelecimentos_id"], 'fk_visualizacoes_estabelecimento_estabelecimentos1_idx');


            $table->foreign('users_id', 'fk_users_sorteio_users1_idx')
                ->references('id')->on('users')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('estabelecimentos_id', 'fk_visualizacoes_estabelecimento_estabelecimentos1_idx')
                ->references('id')->on('estabelecimentos')
                ->onDelete('no action')
                ->onUpdate('no action');
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
