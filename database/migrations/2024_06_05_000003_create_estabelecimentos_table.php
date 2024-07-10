<?php



use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEstabelecimentosTable extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $tableName = 'estabelecimentos';

    /**
     * Run the migrations.
     * @table estabelecimentos
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
            $table->string('phone', 12)->nullable()->default(null);
            $table->string('endereco')->nullable()->default(null);
            $table->decimal('latitude', 10, 2)->nullable()->default(null);
            $table->decimal('longitude', 10, 2)->nullable()->default(null);
            $table->string('instagram')->nullable()->default(null);
            $table->string('status', 1)->nullable()->default(null);
            $table->softDeletes();
            $table->timestamps();
            $table->unsignedInteger('categorias_estabelecimentos_id');

            $table->index(["categorias_estabelecimentos_id"], 'fk_estabelecimentos_categorias_estabelecimentos_idx');


            $table->foreign('categorias_estabelecimentos_id', 'fk_estabelecimentos_categorias_estabelecimentos_idx')
                ->references('id')->on('categorias_estabelecimentos')
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
