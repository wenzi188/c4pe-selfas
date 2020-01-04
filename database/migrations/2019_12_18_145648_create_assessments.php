<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssessments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger("questionnaire_id")->index();
            $table->bigInteger("period_id")->index();
            $table->datetime("started");
            $table->string("selections", 4000)->comment("json: {'1':[1,3,3,3,3],'2':[1,2,2,3,3],... {criteria_id:[score,weights1, pointss1,weights2, pointss2");
            $table->string("results", 512)->comment("json: {[sumS1, sumS2, tendency(from -2 to 2)]");
            $table->softDeletes();

            $table->timestamps();
            $table->index('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assessments');
    }
}
