<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcessTrackersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('process_trackers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('file_path')->nullable();
            $table->integer('total');
            $table->integer('completed_count');
            $table->enum('status',['new','initiated','running','paused','cancelled','completed'])->default('new');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('process_trackers');
    }
}
