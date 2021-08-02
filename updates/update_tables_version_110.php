<?php 

namespace Synder\Analytics;

use Schema;
use Illuminate\Support\Facades\DB;
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
        
        // Convert Existing Visitors
        $items = DB::table('synder_analytics_visitors')->get()->all();
        foreach ($items AS $item) {
            if (empty($item->agent)) {
                continue;
            }
            if (strpos($item->agent, '{') !== 0) {
                continue;
            }

            $agent = json_decode($item->agent, true);
            if (!empty($agent['client'])) {
                $browser = $agent['client']['name'] . ' ' . $agent['client']['version'];
            }
            if (!empty($agent['os'])) {
                $os = $agent['os']['name'] . ' ' . $agent['os']['version'];
            }

            DB::table('synder_analytics_visitors')->where('id', $item->id)->update([
                'agent' => $agent['agent'],
                'agent_details' => json_encode($agent),
                'browser' => $browser ?? '',
                'os' => $os ?? ''
            ]);
        }
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
