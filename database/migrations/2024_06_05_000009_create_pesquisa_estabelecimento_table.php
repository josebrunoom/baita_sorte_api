<?php



use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePesquisaEstabelecimentoTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'pesquisa_estabelecimento';

    /**
     * Run the migrations.
     * @table pesquisa_estabelecimento
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            
            $table->increments('id');
            $table->unsignedInteger('perguntas_estabelecimento_id');
            $table->unsignedInteger('estabelecimentos_id');

            $table->index(["perguntas_estabelecimento_id"], 'fk_pesquisa_estabelecimento_perguntas_estabelecimento1_idx');

            $table->index(["estabelecimentos_id"], 'fk_pesquisa_estabelecimento_estabelecimentos1_idx');


            $table->foreign('perguntas_estabelecimento_id', 'fk_pesquisa_estabelecimento_perguntas_estabelecimento1_idx')
                ->references('id')->on('perguntas_estabelecimento')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('estabelecimentos_id', 'fk_pesquisa_estabelecimento_estabelecimentos1_idx')
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
