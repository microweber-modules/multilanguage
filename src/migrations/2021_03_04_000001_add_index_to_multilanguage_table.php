<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexToMultilanguageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            Schema::table('multilanguage_translations', function (Blueprint $table) {
                $table->index('rel_id');
                $table->string('rel_type')->unique()->change();
                $table->string('field_name')->unique()->change();
            });
        } catch (Exception $e) {

        }

        try {
            Schema::table('multilanguage_supported_locales', function (Blueprint $table) {
                $table->string('locale')->unique()->change();
                $table->string('language')->unique()->change();
                $table->string('is_active')->unique()->change();
            });
        } catch (Exception $e) {

        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}