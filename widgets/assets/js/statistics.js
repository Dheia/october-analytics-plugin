jQuery(function($) {
    "use strict";

    /**
     * Extended ChartLine Functions
     * @param {jQuery} element 
     * @param {jQuery} legend 
     * @param {Flot} plot 
     */
    function ChartLineExtended(chart, chartLegend, plot) {
        this.chart = chart;
        this.chartLegend = chartLegend;
        this.flot = plot;

        this.datasets = { };
        let datasets = JSON.parse(JSON.stringify(this.flot.getData()));
        for (let i = 0; i < datasets.length; i++) {
            let key = datasets[i].name.toLowerCase();
            this.datasets[key] = datasets[i];
            this.datasets[key].visible = true;
        }

        this.init();
    };

    /**
     * Prototype
     */
    ChartLineExtended.prototype = {
        /**
         * Initialize
         */
        init: function () {
            let toggleData = this.toggleData.bind(this);

            this.chartLegend.find('[data-toggle]').on('click', function(event) {
                this.dataset.visible = true;
                toggleData(this.dataset.toggle);
            }); 
        },

        /**
         * Toggle Data Line
         * @param {string} type The data type you want to toggle.
         */
        toggleData: function(type) {
            if (!(type in this.datasets)) {
                return;
            }
            this.datasets[type].visible = !this.datasets[type].visible;
            this.chartLegend.find(`[data-toggle="${type}"]`)[0].dataset.visible = this.datasets[type].visible;
            
            let datasets = [];
            for (let set of Object.values(this.datasets)) {
                if (set.visible) {
                    datasets.push(set);
                }
            }
            this.flot.setData(datasets);
            this.flot.draw();
        }
    };

    /**
     * Initialize
     */
    $(window).on('render', () => {
        $('[data-synder-analytics="systems"]').each(function(i, widget) {
            let element = $(widget);

            new ChartLineExtended(
                element,
                element.next(),
                element.chartLine().data().plot
            );
        });
    });
});
