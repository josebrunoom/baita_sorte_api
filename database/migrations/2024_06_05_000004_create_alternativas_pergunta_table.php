<?php



use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlternativasPerguntaTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'alternativas_pergunta';

    /**
     * Run the migrations.
     * @table alternativas_pergunta
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            
            $table->increments('id');
            $table->string('texto')->nullable()->default(null);
            $table->string('status', 1)->nullable()->default(null);
            $table->string('certa_errada', 1)->nullable()->default(null);
            $table->unsignedInteger('perguntas_estabelecimento_id');

            $table->index(["perguntas_estabelecimento_id"], 'fk_alternativas_pergunta_perguntas_estabelecimento1_idx');


            $table->foreign('perguntas_estabelecimento_id', 'fk_alternativas_pergunta_perguntas_estabelecimento1_idx')
                ->references('id')->on('perguntas_estabelecimento')
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
