<?php 

namespace Synder\Analytics;

use Schema;
use October\Rain\Database\Updates\Migration;
use October\Rain\Database\Schema\Blueprint;


class UpdateTablesVersion110 extends Migration 
{

    /**
     * Install
     *
     * @return void
     */
    public function up()
    {
        Schema::table('synder_analytics', function (Blueprint $table) {
            $table->boolean('hide')->default(false)->after('path');
        });
        
        Schema::table('synder_analytics_visitors', function (Blueprint $table) {
            $table->text('bot_details')->nullable()->after('bot');
            $table->string('os', 255)->nullable()->after('agent');
            $table->string('browser', 255)->nullable()->after('agent');
            $table->text('agent_details')->nullable()->after('agent');
        });
    }

    /**
     * Uninstall
     *
     * @return void
     */
    public function down()
    {
        Schema::table('synder_analytics', function (Blueprint $table) {
            $table->dropColumn('hide');
        });

        Schema::table('synder_analytics_visitors', function (Blueprint $table) {
            $table->dropColumn('browser');
            $table->dropColumn('os');
        });
    }
}
