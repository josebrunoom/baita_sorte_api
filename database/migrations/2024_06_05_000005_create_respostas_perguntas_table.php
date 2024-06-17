<?php



use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRespostasPerguntasTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'respostas_perguntas';

    /**
     * Run the migrations.
     * @table respostas_perguntas
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            
            $table->increments('id');
            $table->unsignedInteger('perguntas_estabelecimento_id');
            $table->unsignedInteger('users_id');
            $table->unsignedInteger('alternativas_pergunta_id');

            $table->index(["perguntas_estabelecimento_id"], 'fk_respostas_perguntas_perguntas_estabelecimento1_idx');

            $table->index(["users_id"], 'fk_respostas_perguntas_users1_idx');

            $table->index(["alternativas_pergunta_id"], 'fk_respostas_perguntas_alternativas_pergunta1_idx');


            $table->foreign('perguntas_estabelecimento_id', 'fk_respostas_perguntas_perguntas_estabelecimento1_idx')
                ->references('id')->on('perguntas_estabelecimento')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('users_id', 'fk_respostas_perguntas_users1_idx')
                ->references('id')->on('users')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('alternativas_pergunta_id', 'fk_respostas_perguntas_alternativas_pergunta1_idx')
                ->references('id')->on('alternativas_pergunta')
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
