<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLinkedCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('linked_cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_holder_id');
            $table->string('type', 100);
            $table->string('number', 50);
            $table->string('name', 50);
            $table->string('expirationDate',10);
            $table->foreign('account_holder_id', 'account_holder_fk')
                ->references('id')
                ->on('account_holders');
            $table->boolean('active')->default(true);
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
        Schema::dropIfExists('linked_cards');
    }
}
