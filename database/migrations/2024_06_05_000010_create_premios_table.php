<?php



use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePremiosTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'premios';

    /**
     * Run the migrations.
     * @table premios
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            
            $table->increments('id');
            $table->string('nome', 45)->nullable()->default(null);
            $table->string('descricao')->nullable()->default(null);
            $table->string('foto')->nullable()->default(null);
            $table->unsignedInteger('sorteios_id');
            $table->unsignedInteger('estabelecimentos_id');

            $table->index(["sorteios_id"], 'fk_premios_sorteios1_idx');

            $table->index(["estabelecimentos_id"], 'fk_premios_estabelecimentos1_idx');


            $table->foreign('sorteios_id', 'fk_premios_sorteios1_idx')
                ->references('id')->on('sorteios')
                ->onDelete('no action')
                ->onUpdate('no action');

            $table->foreign('estabelecimentos_id', 'fk_premios_estabelecimentos1_idx')
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
