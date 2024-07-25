<?php



use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAtracoesEstabelecimentoTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'atracoes_estabelecimento';

    /**
     * Run the migrations.
     * @table atracoes_estabelecimento
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            
            $table->increments('id');
            $table->string('nome', 45)->nullable()->default(null);
            $table->string('descricao')->nullable()->default(null);
            $table->dateTime('data_atracao')->nullable()->default(null);
            $table->dateTime('inicio_divulgacao')->nullable()->default(null);
            $table->dateTime('fim_divulgacao')->nullable()->default(null);
            $table->string('foto')->nullable()->default(null);
            $table->unsignedInteger('estabelecimentos_id');

            $table->index(["estabelecimentos_id"], 'fk_atracoes_estabelecimento_estabelecimentos1_idx');


            $table->foreign('estabelecimentos_id', 'fk_atracoes_estabelecimento_estabelecimentos1_idx')
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
