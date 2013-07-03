$(document).ready(function(){

    var optionsVisit = {
        width : 137,
        height : 35,
        margin : { top : 0, bottom : 0, left : 0, right : 0 },
        color : "#808184"
        };

    var optionsDonation = {
        width : 137,
        height : 35,
        margin : { top : 0, bottom : 0, left : 0, right : 0 },
        color : "#313B96"
        };

    var optionsMeanDonation = {
        width : 137,
        height : 35,
        margin : { top : 0, bottom : 0, left : 0, right : 0 },
        color : "#1DB3B2"
        };

    var format = d3.time.format("%Y-%m-%d");

    var maxMean = 25;

    d3.json('http://goteo:gotagota@beta.goteo.org/view/js/project/visitors_data.json', function(json){
        createTable(json);
        renderTable(json);
    });

    // -----------------
    // 
    // Render charts on table
    // 
    // -----------------

    function renderTable(json){
        json.forEach(function(d){
            var source = d.source,
                visitors_data = [],
                donations_data = [],
                mean_donation_data = [];

            var pledge = 0,
                number_of_donations = 0;

            d.data.forEach(function(d){
                var visitors = _.values(d)[0].visitors,
                    donations = _.values(d)[0].donations,
                    date = _.keys(d)[0];

                visitors_data.push({ 'date' : date, 'value' : visitors});
                donations_data.push({ 'date' : date, 'value' : donations});

                pledge += d3.sum(donations);
                number_of_donations += donations.length;
            });

            mean_donation_data = {'pledge' : pledge, 
                                    'number_of_donations' : number_of_donations};

            if (pledge / number_of_donations > maxMean){
                maxMean = pledge / number_of_donations;
            }

            var visitorsChart = new tinyChart(optionsVisit);
            visitorsChart.source = source;
            visitorsChart.setData(visitors_data);
            visitorsChart.setRenderFn(renderVisitors);
            visitorsChart.render();

            var donationsChart = new tinyChart(optionsDonation);
            donationsChart.source = source;
            donationsChart.setData(donations_data);
            donationsChart.setRenderFn(renderDonations);
            donationsChart.render();

            var meanDonationsChart = new tinyChart(optionsMeanDonation);
            meanDonationsChart.source = source;
            meanDonationsChart.setData(mean_donation_data);
            meanDonationsChart.setRenderFn(renderMeanDonation);
            meanDonationsChart.render();
        });
    }

    // -----------------
    // 
    // Module tinyChart
    // 
    // -----------------

    var tinyChart = function(options){
        this.source = options.source,
        this.data,
        this.color = options.color,
        this.size = { width : options.width,
                    height : options.height, 
                    margin : options.margin};

        this.setData = function(newData){
            this.data = newData;
        },
        this.setRenderFn = function(renderFn){
            render = renderFn;
        },
        this.render = function(){
            return render(this);
        };
    };

    // -----------------
    //
    // Render visitors
    //
    // -----------------

    function renderVisitors(options){
        var size = options.size,
            div_id = 'tinyVisitors_' + options.source,
            svg_id = 'svg_tinyVisitors_' + options.source,
            data = options.data;

        var date0 = format.parse(data[0].date),
            dateF = format.parse(_.last(data).date);

        var y = d3.scale.linear().domain([20, 0]).range([0, size.height]),
            t = d3.time.scale().domain([date0, dateF]).rangeRound([0, size.width]);

        d3.select("#" + svg_id).remove();

        var chart = d3.select("#" + div_id)
            .append("svg")
            .attr("id", "#" + svg_id)
            .attr("width", size.width)
            .attr("height", size.height);

        var area = d3.svg.area()
            .x(function(d) { return t(format.parse(d.date)); })
            .y0(function(d) { return y(0); })
            .y1(function(d) { return y(d.value); })
    
        chart.append("path") 
            .attr("class", "area")
            .attr("d", area(data))
            .style("fill", options.color);
    }

    // -----------------
    //
    // Render donations
    //
    // -----------------

    function renderDonations(options){
        var size = options.size,
            div_id = 'tinyDonations_' + options.source,
            svg_id = 'svg_tinyDonations_' + options.source,
            data = options.data;
        
        var date0 = format.parse(data[0].date),
            dateF = format.parse(_.last(data).date);

        var y = d3.scale.linear().domain([20, 0]).range([0, size.height]),
            t = d3.time.scale().domain([date0, dateF]).rangeRound([0, size.width]);

        var chart = d3.select("#" + div_id)
            .append("svg")
            .attr("width", size.width)
            .attr("height", size.height);

        var area = d3.svg.area()
            .x(function(d) { return t(format.parse(d.date)); })
            .y0(function(d) { return y(0); })
            .y1(function(d) { return y(d.value.length); })
    
        chart.append("path") 
            .attr("class", "area")
            .attr("d", area(data))
            .style("fill", options.color);
    }

    // -----------------
    //
    // Render mean donation
    //
    // -----------------

    function renderMeanDonation(options){
        var size = options.size,
            div_id = 'tinyMeanDonation_' + options.source,
            svg_id = 'svg_tinyMeanDonation_' + options.source,
            data = options.data;

        var x = d3.scale.linear().domain([0, maxMean]).range([0, size.width]);

        var chart = d3.select("#" + div_id)
            .append("svg")
            .attr("width", size.width)
            .attr("height", size.height);

        chart.selectAll(".meanBar")
            .data([data])
            .enter().append("rect") 
            .attr("class", "meanBar")
            .attr("x", 0)
            .attr("y", size.height/2 - 10)
            .attr("width", function(d){ return x(d.pledge/d.number_of_donations); })
            .attr("height", 10)
            .style("fill", options.color);
    }

    // -----------------
    //
    // Layout table rows and fill them with empty divs
    //
    // -----------------

    function createTable(json){
        json.forEach(function (d) {
            var row = '<div class="row">'
            var source = d.source,
                pledged = d.pledged,
                number_of_donations = 0,
                visitors = 0;

            d.data.forEach(function(d){
                visitors += _.values(d)[0].visitors;
                number_of_donations += _.values(d)[0].donations.length;
            });

            var mean_donation = pledged / number_of_donations;

            var source_div = '<div class="cell"><div id="sourceTitle_' + source +
                            '" class="source_name metric_in_cell">' + source.toUpperCase() +
                            '</div><div id="source_total_' + source + 
                            '" class="source_total">' + pledged + '<img src="./euro.png" width="12px"/></div></div>';

            var visitors_chart = '<div class="cell"><div class="metric_in_cell">' + visitors + '</div>' +
                                '<div id="tinyVisitors_' + source + '" class="visitors chart_in_cell"></div></div>';

            var donations_chart = '<div class="cell"><div class="metric_in_cell">' + number_of_donations + '</div>' +
                                    '<div id="tinyDonations_' + source + '" class="donations chart_in_cell"></div></div>';

            var mean_donation_chart = '<div class="cell final_cell"><div class="metric_in_cell">' + mean_donation.toFixed(2) + 
                                        '</div><div id="tinyMeanDonation_' + source + '"class="mean_donation chart_in_cell"></div></div>';

            row = row.concat(source_div, visitors_chart, donations_chart, mean_donation_chart);
            row = row.concat("</div>")
            $("#source_table_body").append(row);
        });
    };
});
