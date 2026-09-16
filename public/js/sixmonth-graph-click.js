(function () {
    'use strict';

    /** Références aux instances Chart.js identifiées à leur connexion Stimulus */
    var barChart = null;
    var donutCategorie = null;
    var donutMagasin = null;

    /**
     * Symfony UX Chartjs émet "chartjs:connect" pour chaque graphe après sa création.
     * On identifie chaque graphe par son wrapper (id HTML) et on stocke la référence.
     */
    document.addEventListener('chartjs:connect', function (event) {
        var chart  = event.detail.chart;
        var canvas = chart.canvas;

        if (canvas.closest('#bar-six-month-wrapper')) {
            barChart = chart;
            // Ajout du handler de clic sur le bar chart
            chart.options.onClick = function (evt, elements) {
                if (!elements || elements.length === 0) {
                    return;
                }
                handleBarClick(elements[0].index);
            };
            // Curseur "pointer" pour indiquer que les barres sont cliquables
            chart.options.onHover = function (evt) {
                evt.native.target.style.cursor =
                    chart.getElementsAtEventForMode(evt.native, 'nearest', { intersect: true }, false).length
                        ? 'pointer'
                        : 'default';
            };
            chart.update();
        } else if (canvas.closest('#donut-categorie-wrapper')) {
            donutCategorie = chart;
        } else if (canvas.closest('#donut-magasin-wrapper')) {
            donutMagasin = chart;
        }
    });

    /**
     * Met à jour les deux donuts avec les données pré-calculées par PHP pour le mois cliqué.
     * @param {number} index – index de la barre cliquée (0 = il y a 5 mois, 5 = mois courant)
     */
    function handleBarClick(index) {
        var dataEl = document.getElementById('six-month-donut-data');
        if (!dataEl) {
            return;
        }

        var sixMonthData;
        try {
            sixMonthData = JSON.parse(dataEl.textContent);
        } catch (e) {
            console.error('sixmonth-graph-click : impossible de parser les données JSON', e);
            return;
        }

        var monthData = sixMonthData[index];
        if (!monthData) {
            return;
        }

        updateDonut(donutCategorie, monthData.categorie);
        updateDonut(donutMagasin, monthData.magasin);

        // Mise à jour du titre de la section donuts
        var title = document.getElementById('donuts-section-title');
        if (title) {
            title.textContent = 'Dépenses de ' + monthData.label;
        }
    }

    /**
     * Remplace les données d'un donut Chart.js et le rafraîchit.
     * Le formateur de pourcentages reste actif car il est déjà dans les options du graphe.
     * @param {Chart|null} chart
     * @param {{ labels: string[], data: number[], colors: string[] }} data
     */
    function updateDonut(chart, data) {
        if (!chart) {
            return;
        }
        chart.data.labels                              = data.labels;
        chart.data.datasets[0].data                   = data.data;
        chart.data.datasets[0].backgroundColor        = data.colors;
        chart.data.datasets[0].borderColor            = data.colors;
        chart.update();
    }
})();
