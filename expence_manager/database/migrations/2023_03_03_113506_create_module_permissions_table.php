<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module_permissions', function (Blueprint $table) {
            $table->id();
            $table->boolean('add_access')->default(false);
            $table->boolean('edit_access')->default(false);
            $table->boolean('view_access')->default(false);
            $table->boolean('delete_access')->default(false);
            $table->foreignId('module_code')->constrained('modules')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('permission_id')->constrained('permissions')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('module_permissions');
    }
};
