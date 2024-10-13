<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddParentSubsectionIdToSubSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sub_sections', function (Blueprint $table) {
            $table->foreignId('parent_subsection_id')->nullable()->constrained('sub_sections')->onDelete('cascade'); // Recursive relationship
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sub_sections', function (Blueprint $table) {
            $table->dropForeign('parent_subsection_id');
            $table->dropColumn('parent_subsection_id');
        });
    }
}
