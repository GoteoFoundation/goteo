// **************
//
// Goteo Analytics
// By Franc Camps-Febrer
// 2013
//
// **************

// Make sure the namespace is defined
if (typeof GOTEO === 'undefined') {
    var GOTEO = {
        charts : {},
        visualizers : {},
        utilities : {},
        dates : {},
        formats : {
            format : d3.time.format("%Y-%m-%d"),
            formatXaxis : d3.time.format("%b %d"),
            formatLabel : d3.time.format("%d %B"),
            formatYaxis : function (d) {
                return (+d).toLocaleString("de-DE");
            }
        }
    };
};

GOTEO.getDates = function(dates) {
    // ---------------
    // Determine deadlines and whether project is finished
    // ---------------
    var format = GOTEO.formats.format;

        console.log(dates.published)
        console.log(GOTEO.dates)
    GOTEO.dates.start_date = d3.time.day(format.parse(dates.published));
    GOTEO.dates.finished = (dates.success || dates.closed) ? true : false;

    // Possible combinations of states, returns deadline
    if (!dates.passed && !dates.success && !dates.closed) {
        // Active project
        GOTEO.dates.deadline = d3.time.day.offset(GOTEO.dates.start_date, dates.days_round1);
    } else if (dates.passed && !dates.success) {
        // Active project, after first deadline
        GOTEO.dates.passed_minimum = true;
        GOTEO.dates.first_deadline = format.parse(dates.passed);
        GOTEO.dates.deadline = d3.time.day.offset(GOTEO.dates.start_date, dates.days_total);
    } else if (dates.success) {
        // Succeeded, not active any more
        GOTEO.dates.passed_minimum = (dates.passed) ? true : false;
        GOTEO.dates.first_deadline = (dates.passed) ? format.parse(dates.passed) : undefined;
        GOTEO.dates.deadline = d3.time.day(format.parse(dates.success));
    } else {
        // Unsuccessful project, finished
        GOTEO.dates.deadline = format.parse(dates.closed);
    }
    GOTEO.dates.today = d3.min([d3.time.day.offset(GOTEO.dates.deadline, 1), d3.time.day(new Date())]);
    GOTEO.dates.day_number = Math.floor((GOTEO.dates.today - GOTEO.dates.start_date) / 86400000);
    GOTEO.dates.total_days = (dates.passed) ? dates.days_total : dates.days_round1;
    GOTEO.dates.days_left = (GOTEO.dates.finished || GOTEO.dates.total_days <= GOTEO.dates.day_number) ? 0 : GOTEO.dates.total_days - GOTEO.dates.day_number;
}

// ----------------------
//
// Get cumulative funding by day
//
// ----------------------

GOTEO.getFundingByDay = function(invests){
    var funded_data = [],
        funders_data = [],
        funded = 0,
        funders = 0,
        all_days = d3.time.day.range(GOTEO.dates.start_date, GOTEO.dates.today),
        format = GOTEO.formats.format;

    // Rollup number of funders per day, and amount of funding per day
    all_days.forEach(function(g){
        funders = 0;
        invests.forEach(function(d){
            if (d.date === format(g)){
                funded += +d.amount;
                funders += 1;
            }
        });
        funded_data.push({value: funded, 'date' : format(g) });
        funders_data.push({value: funders, 'date' : format(g) });
    });
    return {'funded_data' : funded_data, 'funders_data' : funders_data};
};

// ----------------------
//
// Create and initialize chart objects
//
// ----------------------

GOTEO.initializeGraph = function(raw_data) {

        var fundsChart, fundersChart,
            invests, data,
            dates = GOTEO.dates,
            format = GOTEO.formats.format,
            minimum = raw_data.minimum,
            optimum = parseInt(raw_data.minimum) + parseInt(raw_data.optimum),
        funds_options = {size : {'w' : $("#funds").width() - 50, 'h' : 250}},
        funders_options = {size : {'w' : $("#funds").width() - 50, 'h' : 40}};

        // Get deadline, day_number and other time flags
        GOTEO.getDates(raw_data.dates);
        // Generate array of invest objects
        invests = [];
        raw_data.invests.forEach(function(d) {
            invests.push({'amount' : d.amount,
                            'user' : d.user,
                            'date' : d.invested});
        });
        // Generate daily data, funding and cofunders
        data = GOTEO.getFundingByDay(invests);

        // --------------
        // Create chart Object for funds
        // --------------

        GOTEO.charts.fundsChart = new GOTEO.ChartObject(funds_options);
        fundsChart = GOTEO.charts.fundsChart;

        // Set data for display into chart object
        fundsChart.setData(data.funded_data);
        fundsChart.setCurrent({'value' : _.last(data.funded_data).value,
                                'time' : _.last(data.funded_data).date });
        fundsChart.setMinimum(minimum);
        fundsChart.setOptimum(optimum);
        fundsChart.setHoverData(_.groupBy(data.funded_data, function(d){ return d.date; }));

        if (dates.passed_minimum){
            fundsChart.setMinimumData([{ 'date' : format(dates.start_date), 'value' : 0},
                        { 'date' : format(dates.first_deadline), 'value' : minimum },
                        { 'date' : format(dates.deadline), 'value' : minimum}]);
        } else {
            fundsChart.setMinimumData([{ 'date' : format(dates.start_date), 'value' : 0},
                        { 'date' : format(dates.deadline), 'value' : minimum}]);
        }

        // Attach visualizer to funds object
        fundsChart.setRenderFn(GOTEO.visualizers.renderFunds);
        fundsChart.render();

        // --------------
        // Create chart object for funders
        // --------------

        GOTEO.charts.fundersChart = new GOTEO.ChartObject(funders_options);
        fundersChart = GOTEO.charts.fundersChart;

        // Set data for display into chart object
        fundersChart.setData(data.funders_data);
        fundersChart.setCurrent({'value' : _.last(data.funders_data).value,
                                'time' : _.last(data.funders_data).date });
        fundersChart.setHoverData(_.groupBy(data.funders_data, function(d){ return d.date; }))

        // Attach visualizer to funders object
        fundersChart.setRenderFn(GOTEO.visualizers.renderCofunders);
        fundersChart.render();

        // Render charts and info
        // GOTEO.updateTitles();
        // now with php
}

// ----------------------
//
// Update funds and days left in subtitle
//
// ----------------------
/*
 * Estos datos los pintamos en la vista con php
 *
GOTEO.updateTitles = function() {
    var fundsChart = GOTEO.charts.fundsChart,
        current = fundsChart.getCurrent().value,
        dates = GOTEO.dates,
        text = "de euros.";

    if (dates.passed_minimum) {
        text = text + " (<div style='color: #bb70b6; display: inline'>"
                + (+fundsChart.getOptimum()).toLocaleString("de-DE") + "</div> &oacuteptimo)";
    }
    if (current) {
            $("#funded").html((+current).toLocaleString("de-DE"));
            $("#de").html('de');
            $("#minimum").html((+fundsChart.getMinimum()).toLocaleString("de-DE"));
            $("#euros").html(text);
    } else {
        $("#funded").html('No hay donaciones.');
            $("#de").html('');
            $("#minimum").html('');
            $("#euros").html('');
    }
    $("#dias").html(dates.days_left).css("margin", "0px 5px");
};
*/
