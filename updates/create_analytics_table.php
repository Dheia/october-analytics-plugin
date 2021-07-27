<?php 

namespace Synder\Analytics;

use Schema;
use October\Rain\Database\Updates\Migration;
use October\Rain\Database\Schema\Blueprint;


class CreateAnalyticsTable extends Migration 
{

    /**
     * Install
     *
     * @return void
     */
    public function up()
    {
        Schema::create('synder_analytics', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->bigIncrements('id');
            $table->string('hash', 64)->unique();
            $table->string('method', 16);
            $table->text('path', 16);
            $table->integer('views')->unsigned()->default(0);
            $table->integer('unique')->unsigned()->default(0);
            $table->timestamps();
        });

        Schema::create('synder_analytics_visitors', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->bigIncrements('id');
            $table->string('hash', 64);
            $table->float('bot')->default(0.0);
            $table->text('agent')->nullable();
            $table->integer('visits')->unsigned()->default(0);
            $table->timestamp('first_visit');
            $table->timestamp('last_seen');
        });

        Schema::create('synder_analytics_views', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->bigIncrements('id');
            $table->bigInteger('analytics_id')->unsigned();
            $table->bigInteger('visitor_id')->unsigned();
            $table->enum('type', ['entrypoint', 'followup', 'untracked'])->default('untracked');
            $table->integer('order')->default(0);
            $table->integer('visits')->unsigned()->default(0);
            $table->text('request')->nullable();
            $table->text('referer')->nullable();
            $table->text('response')->nullable();
            $table->integer('response_status')->nullable();
            $table->timestamps();

            $table->foreign('analytics_id')->references('id')->on('synder_analytics')->onDelete('cascade');
            $table->foreign('visitor_id')->references('id')->on('synder_analytics_visitors')->onDelete('cascade');
        });

        Schema::create('synder_analytics_referrers', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->bigIncrements('id');
            $table->string('hash', 64)->unique();
            $table->string('host', 255);
            $table->text('url');
            $table->integer('visits')->unsigned()->default(0);
            $table->timestamp('first');
            $table->timestamp('last');
        });
    }

    /**
     * Uninstall
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('synder_analytics_referrers');
        Schema::dropIfExists('synder_analytics_views');
        Schema::dropIfExists('synder_analytics_visitors');
        Schema::dropIfExists('synder_analytics');
    }

}
