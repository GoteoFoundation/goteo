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

// ----------------------
//
// Render chart for funds gathered by day
//
// ----------------------

GOTEO.visualizers.renderFunds = function(dates){
    // Get dates and date formatting    
    var formatXaxis = GOTEO.formats.formatXaxis,
        formatYaxis = GOTEO.formats.formatYaxis,
        format = GOTEO.formats.format,
        start_date = GOTEO.dates.start_date,
        deadline = GOTEO.dates.deadline,
        passed_minimum = GOTEO.dates.passed_minimum,
        leftTip = GOTEO.utilities.leftTip;

    // Make local variables for performance
    var data = this.getData(),
        size = this.getSize(),
        margin = this.getMargin(),
        t = this.getTimeScale(),
        y = this.getYScale(),
        minimum_data = this.getMinimumData(),
        minimum = this.getMinimum(),
        optimum = this.getOptimum(),
        current = this.getCurrent().value,
        current_time = this.getCurrent().time;

    var chart, area, line, xAxis, yAxis, filter;

    // Funding column
    var columnW = 10,
        line_width = 26;
    
    // Info box
    var tip_box_len = 37,
        tip,
        tipW = 5,
        tipH = 10,
        boxW = 225,
        boxH = 50;
        pos = { x: 10, y: 20 },
        mouse_padd = 20,
        text_padd = { x : 20, y : 6 };

    // Define scales
    var yScaleMax = (passed_minimum) ? optimum : minimum;

    t.domain([start_date, deadline])
        .range([margin.left,size.w + margin.left]),
    y.domain([0,d3.max([yScaleMax, current])])
        .range([size.h + margin.top, margin.top]);

    // Create SVG
    chart = d3.select("#funds").append("svg:svg")
        .attr("id", "svg_funds")
        .attr("width", size.w + margin.left + margin.right)
        .attr("height", size.h + margin.top + margin.bottom)
        .on("mouseover", GOTEO.utilities.onHover)
        .on("mousemove", GOTEO.utilities.onHover)
        .on("mouseout", GOTEO.utilities.offHover);

    // Area function
    area = d3.svg.area()
        .x(function(d) { return t(format.parse(d.date)); })
        .y0(function(d) { return y(0); })
        .y1(function(d) { return y(d.value); })

    // Line function
    line = d3.svg.line()
        .x(function(d) { return t(format.parse(d.date)); })
        .y(function(d) { return y(d.value); });

    // Axes
    xAxis = d3.svg.axis()
        .orient("bottom")
        .scale(t)
        .ticks(10)
        .tickSize(10, 10, 0)
        .tickFormat(formatXaxis)
        .tickSubdivide(true)

    yAxis = d3.svg.axis()
        .orient("left")
        .scale(y)
        .ticks(2)
        .tickPadding(5)
        .tickSize(size.w, size.w, 0)
        .tickSubdivide(true)
        .tickFormat(function(d){
            return (+d).toLocaleString("de-DE");
        });

    chart.append("g")
        .attr("class", "x axis")
        .attr("transform", "translate(0," + (margin.top + size.h) + ")")
        .call(xAxis)
    
    chart.append("g")
        .attr("class", "y axis")
        .attr("transform", "translate(" + (margin.left + size.w) + ",0)")
        .call(yAxis)
   
    // Render areas and lines
    // Minimum required, in the background
    chart.append("path") 
        .attr("class", "area")
        .attr("d", area(minimum_data))
        .style("fill", "#dfbddd")
        .style("opacity", .3);

    // Funded so far
    chart.append("path") 
        .attr("class", "area funds_area")
        .attr("d", area(data))

    chart.append("path")
        .attr("d", line(data))
        .attr("class", "funds_line")

    // ---
    // Column on right side, filling up with funds
    // ---
    chart.append("rect")
        .attr("x", size.w + margin.left)
        .attr("y", y(current))
        .attr("height", size.h + margin.top - y(current))
        .attr("width", columnW)
        .style("fill", "#20B3B2");
    
    // Only draw minimum if not reached 
    if (current < minimum){ 
        chart.append("rect")
            .attr("x", size.w + margin.left)
            .attr("y", y(minimum))
            .attr("height", -y(minimum) + y(current))
            .attr("width", columnW)
            .style("fill", "#96238f");
    }
    
    // Only draw optimum if past minimum and optimum is not reached 
    if (current > minimum && current < optimum){ 
        chart.append("rect")
            .attr("x", size.w + margin.left)
            .attr("y", y(optimum))
            .attr("height", -y(optimum) + y(current))
            .attr("width", columnW)
            .style("fill", "#BB70B6");
    }
    
    // Level marks on column for minimum and funded 
    chart.append("line")
        .attr("x1", size.w + margin.left - line_width + columnW)
        .attr("x2", size.w + margin.left + columnW)
        .attr("y1", y(minimum))
        .attr("y2", y(minimum))
        .style("stroke", "#96238f")
        .style("stroke-width", 2);

    chart.append("line")
        .attr("x1", size.w + margin.left - line_width + columnW)
        .attr("x2", size.w + margin.left + columnW)
        .attr("y1", y(0))
        .attr("y2", y(0))
        .style("stroke", "#20b3b2")
        .style("stroke-width", 2);

    if (current > minimum) {
        chart.append("line")
            .attr("x1", size.w + margin.left - line_width + columnW)
            .attr("x2", size.w + margin.left + columnW)
            .attr("y1", y(optimum))
            .attr("y2", y(optimum))
            .style("stroke", "#bb70b6")
            .style("stroke-width", 2);
    }

    // Draw circle for last day with data or hovered day 
    chart.append("circle")
        .attr("class", "day_circle_funds day_circle")
        .attr("cx", t(format.parse(data[data.length - 1].date)))
        .attr("cy", y(current))
        .attr("r", 5)

    // -------------------
    //
    // Hover infobox
    //
    // ------------------

    // Polygon for tip
    tip = {
        topleft : (size.w + margin.left - tip_box_len) + "," + (y(current) - tipH),
        bottomleft : (size.w + margin.left - tip_box_len) + "," + (y(current) + tipH),
        bottomright : (size.w + margin.left - 10) + "," + (y(current) + tipH),
        tip : (size.w + margin.left) + "," + (y(current)),
        topright : (size.w + margin.left - 10) + "," + (y(current) - tipH)
    };

    // Draw arrow tip of infobox as a polygon
    chart.append("polygon")
        .attr("points", tip.topleft + " " + tip.topright + " " + 
                tip.tip + " " + tip.bottomright + " " + tip.bottomleft)
        .style("fill", "#58595b")

    chart.append("text")
        .attr("x", size.w + margin.left - text_padd.x)
        .attr("y", y(current) + tipH - text_padd.y)
        .attr("class", "arrow_funded_height")
        .text((current / minimum * 100).toFixed(0) + "%");

    // Filter function
    filter = chart.append("svg:defs")
        .append("svg:filter")
        .attr("id", "blur")
        .append("svg:feGaussianBlur")
        .attr("stdDeviation", 5);

    // Infobox tip shadow
    chart.selectAll(".infofunds_tip_bg")
        .data([pos])
        .enter().append("path")
        .attr("d", function(d){ return leftTip(d, boxW, true); })
        .attr("class", "infobox infobox_funds_bg infofunds_tip_bg")
        .attr("filter", "url(#blur)")
    
    // Infobox rect shadow
    chart.selectAll(".infofunds_bg")
        .data([pos])
        .enter().append("rect")
        .attr("class", "infobox infobox_funds_bg infofunds_bg")
        .attr("x", function(d){ return d.x + tipW + mouse_padd; })
        .attr("y", function(d){ return d.y - boxH / 2; })
        .attr("width", boxW)
        .attr("height", boxH)
        .attr("filter", "url(#blur)");

    // Infobox tip
    chart.selectAll(".infofunds_tip")
        .data([pos])
        .enter().append("path")
        .attr("d", function(d){ return leftTip(d, boxW, true); })
        .attr("class", "infobox infofunds_tip")
    
    // Infobox rect
    chart.selectAll(".infofunds")
        .data([pos])
        .enter().append("rect")
        .attr("class", "infobox infofunds")
        .attr("x", function(d){ return d.x + tipW + mouse_padd; })
        .attr("y", function(d){ return d.y - boxH / 2; })
        .attr("width", boxW)
        .attr("height", boxH)

    // Infobox text top
    chart.selectAll(".infofunds_text")
        .data([pos])
        .enter().append("text")
        .attr("class", "infofunds_text infobox")
        .attr("x", 0)
        .attr("y", 0)
        .text('');

    // Infobox text bottom
    chart.selectAll(".infofunds_text2")
        .data([pos])
        .enter().append("text")
        .attr("class", "infofunds_text2 infobox")
        .attr("x", 0)
        .attr("y", 0)
        .text('');
};

// ----------------------
//
// Draw tip of infobox when hovering, update position
//
// ----------------------

GOTEO.utilities.leftTip = function(d, box_length, point_left){
    var tipW = 5,
        tipH = 10,
        mouse_padd = 20;
    if (point_left){
        return "M" + (d.x + mouse_padd) + "," + (d.y)
        + "L" + (d.x + mouse_padd + tipW) + "," + (d.y - tipH / 2) 
        + "L" + (d.x + mouse_padd + tipW) + "," + (d.y + tipH / 2) + "Z";
    }
    return "M" + (d.x + mouse_padd + box_length + 2 * tipW) + "," + (d.y)
        + "L" + (d.x + mouse_padd + box_length + tipW) + "," + (d.y - tipH / 2) 
        + "L" + (d.x + mouse_padd + box_length + tipW) + "," + (d.y + tipH / 2) + "Z";
}

// ----------------------
//
// Render chart for number of donations by day
//
// ----------------------

GOTEO.visualizers.renderCofunders = function(){

    // Make local variables for performance
    var data = this.getData(),
        size = this.getSize(),
        margin = this.getMargin(),
        t = this.getTimeScale(),
        y = this.getYScale(),
        minimum_data = this.getMinimumData(),
        minimum = this.getMinimum(),
        current = this.getCurrent().value,
        current_time = this.getCurrent().time,
        data_for_hover = this.getHoverData();

    var formatXaxis = GOTEO.formats.formatXaxis,
        formatYaxis = GOTEO.formats.formatYaxis,
        format = GOTEO.formats.format;

    var onHover = GOTEO.utilities.onHover,
        offHover = GOTEO.utilities.offHover,
        leftTip = GOTEO.utilities.leftTip;

    var start_date = GOTEO.dates.start_date,
        deadline = GOTEO.dates.deadline,
        passed_minimum = GOTEO.dates.passed_minimum;

    var chart, area, line, xAxis, yAxis, filter;

    // Create scales
    t.domain([start_date, deadline])
        .range([margin.left,size.w + margin.left]);
    y.domain([0,d3.max(data, function(d){ return d.value; })])
        .range([size.h + margin.top, margin.top]);

    // Generate SVG
    chart = d3.select("#cofund").append("svg:svg")
        .attr("id", "svg_funders")
        .attr("width", size.w + margin.left + margin.right)
        .attr("height", size.h + margin.top + margin.bottom)
        .on("mouseover", onHover)
        .on("mousemove", onHover)
        .on("mouseout", offHover);

    // Area and line functions
    area = d3.svg.area()
        .x(function(d) { return t(format.parse(d.date)); })
        .y0(function(d) { return y(0); })
        .y1(function(d) { return y(d.value); });

    line = d3.svg.line()
        .x(function(d) { return t(format.parse(d.date)); })
        .y(function(d) { return y(d.value); });

    // Axes
    xAxis = d3.svg.axis()
        .orient("bottom")
        .scale(t)
        .ticks(10)
        .tickSize(10, 10, 0)
        .tickFormat(formatXaxis)
        .tickSubdivide(true);

    yAxis = d3.svg.axis()
        .orient("left")
        .scale(y)
        .ticks(2)
        .tickPadding(5)
        .tickSize(size.w, size.w, 0)
        .tickFormat(function(d){
            return (+d).toLocaleString("de-DE");
        });

    chart.append("g")
        .attr("class", "x axis")
        .attr("transform", "translate(0," + (margin.top + size.h) + ")")
        .call(xAxis);
    
    chart.append("g")
        .attr("class", "y axis")
        .attr("transform", "translate(" + (margin.left + size.w) + ",0)")
        .call(yAxis);
    
    // Add areas and lines
    chart.append("path") 
        .attr("class", "area funders_area")
        .attr("d", area(data));

    chart.append("path")
        .attr("d", line(data))
        .attr("class", "funders_line");

    // Circle for current or hovered day
    chart.append("circle")
        .attr("class", "day_circle_funders day_circle")
        .attr("cx", t(format.parse(data[data.length - 1].date)))
        .attr("cy", y(data[data.length - 1].value))
        .attr("r", 5)
        .style("stroke", "#313B96");

    // Info box
    var box_length = 150,
        narrowness = 36,
        pos = { x: 10, y: 20 },
        tipW = 5,
        tipH = 10,
        mouse_padd = 20;

    // Blur function for shadow
    filter = chart.append("svg:defs")
        .append("svg:filter")
        .attr("id", "blur")
        .append("svg:feGaussianBlur")
        .attr("stdDeviation", 5);

    // Tip of shadow
    chart.selectAll(".infofunders_tip_bg")
        .data([pos])
        .enter().append("path")
        .attr("d", function(d){ return leftTip(d, length, true); })
        .attr("class", "infobox infofunders_tip_bg")
        .style("fill", "#808184")
        .attr("filter", "url(#blur)");
   
    // Infobox rect shadow 
    chart.selectAll(".infofunders_bg")
        .data([pos])
        .enter().append("rect")
        .attr("class", "infobox infofunders_bg")
        .attr("x", function(d){ return d.x + tipW + mouse_padd; })
        .attr("y", function(d){ return d.y - narrowness / 2; })
        .attr("width", box_length)
        .attr("height", narrowness)
        .style("fill", "#808184")
        .attr("filter", "url(#blur)");

    // Infobox tip
    chart.selectAll(".infofunders_tip")
        .data([pos])
        .enter().append("path")
        .attr("d", function(d){ return leftTip(d, box_length, true); })
        .attr("class", "infobox infofunders_tip")
        .style("fill", "#92AFD1");
    
    // Infobox rect
    chart.selectAll(".infofunders")
        .data([pos])
        .enter().append("rect")
        .attr("class", "infobox infofunders")
        .attr("x", function(d){ return d.x + tipW + mouse_padd; })
        .attr("y", function(d){ return d.y - narrowness / 2; })
        .attr("width", box_length)
        .attr("height", narrowness)
        .style("fill", "#92AFD1");

    // Infobox text
    chart.selectAll(".infofunders_text")
        .data([pos])
        .enter().append("text")
        .attr("class", "infofunders_text infobox")
        .attr("x", 0)
        .attr("y", 0)
        .text();
}

GOTEO.utilities.onHover = function(){
    var fundsChart = GOTEO.charts.fundsChart,
        fundersChart = GOTEO.charts.fundersChart,
        dates = GOTEO.dates;

    var start_date = dates.start_date,
        passed_minimum = dates.passsed_minimum;

    var formatXaxis = GOTEO.formats.formatXaxis,
        format = GOTEO.formats.format,
        formatLabel = GOTEO.formats.formatLabel,
        leftTip = GOTEO.utilities.leftTip;

    // Get local data
    var t = fundsChart.getTimeScale(),
        y = fundsChart.getYScale(),
        yFunders = fundersChart.getYScale(),
        xMouse = d3.mouse(this)[0],
        yMouse = d3.mouse(this)[1],
        now = t.invert(xMouse),
        day = d3.time.day(now),
        size = fundsChart.getSize(),
        minimum = fundsChart.getMinimum(),
        optimum = fundsChart.getOptimum();
       
    if (xMouse > fundsChart.getMargin().left && xMouse < fundsChart.getSize().w + fundsChart.getMargin().left){
        // Get size of box
        var boxH_funds = +d3.select(".infofunds").attr("height"),
            boxH_funders = +d3.select(".infofunders").attr("height"),
            boxW_funds = +d3.select(".infofunds").attr("width"),
            boxW_funders = +d3.select(".infofunders").attr("width"),
            data_for_hover = fundsChart.getHoverData();

        var today_funds = data_for_hover[format(day)][0],
            tomorrow = data_for_hover[format(d3.time.day.offset(day, 1))][0],
            this_minute = (now - day) / 1000,
            total_days = dates.total_days,
            day_number = Math.floor((format.parse(today_funds.date) - (start_date)) / 86400000),
            today_minimum = (day_number > PRIMERA_RONDA) ? minimum : Math.floor((+minimum / PRIMERA_RONDA) * +day_number),
            y_today_funds = y(today_funds.value),
            y_tomorrow_funds = y(tomorrow.value);
            if (!passed_minimum) {
                text_today_minimum = (+today_funds.value).toLocaleString("de-DE") + " de " + (+minimum).toLocaleString("de-DE") + " (" + (+today_minimum).toLocaleString("de-DE") + " recomendado)"; 
            } else {
                text_today_minimum = (+today_funds.value).toLocaleString("de-DE") + " de " + (+minimum).toLocaleString("de-DE") + "(&ocuteptimo: " + (+optimum).toLocaleString("de-DE") + ")";
            }
        
        // Get coordinates of funds circle
        var cx = t(format.parse(today_funds.date));
        var cy = y_today_funds + (y_tomorrow_funds - y_today_funds) * this_minute / 86400,
            cy_box = cy;
        if (cy - boxH_funds / 2 < fundsChart.getMargin().top) {
            cy_box += boxH_funds / 2;
        }

        data_for_hover = fundersChart.getHoverData();
        var total_funders = d3.sum(_.map(data_for_hover, function(d){ return _.values(d)[0].value; }))
            today_funders = data_for_hover[format(day)][0].value,
            tomorrow_funders = data_for_hover[format(d3.time.day.offset(day, 1))][0].value,
            y_today_funders = yFunders(today_funders),
            y_tomorrow_funders = yFunders(tomorrow_funders);

        // Get Y coordinate of funders circle
        var cy_funders = y_today_funders + (y_tomorrow_funders - y_today_funders) * this_minute / 86400;
            cy_box_funders = cy_funders;
        if (cy_funders - boxH_funds / 2 < fundersChart.getMargin().top) {
            cy_box_funders += boxH_funders / 2;
        }

        var box_margin = 25,
            box_margin_when_left = 60,
            text_padd = { x : 10, y : 6 },
            point_right = (cx > size.w/2),
            tooltip_to_right = (point_right) ? (-boxW_funds - box_margin_when_left) : 0,
            tooltip_to_right_funders = (point_right) ? (-boxW_funders - box_margin_when_left) : 0;

        // Infobox for funds, tip shadow
        d3.selectAll(".infofunds_tip_bg")
            .data([{x: (xMouse + tooltip_to_right), y: cy}])
            .transition(10)
            .attr("d", function(d){ return leftTip(d, boxW_funds, !point_right); })
            .style("opacity", 1)

        // Infobox for funds, rect shadow
        d3.selectAll(".infofunds_bg")
            .data([{x: xMouse, y: cy_box}])
            .transition(10)
            .attr("x", function(d){ return d.x + box_margin + tooltip_to_right; })
            .attr("y", function(d){ return d.y - (boxH_funds / 2); })
            .style("opacity", 1)

        // Infobox for funds
        d3.selectAll(".infofunds")
            .data([{x: xMouse, y: cy_box}])
            .transition(10)
            .attr("x", function(d){ return d.x + box_margin + tooltip_to_right; })
            .attr("y", function(d){ return d.y - (boxH_funds / 2); })
            .style("opacity", 1)

        // Infobox for funds, tip
        d3.selectAll(".infofunds_tip")
            .data([{x: (xMouse + tooltip_to_right), y: cy}])
            .transition(10)
            .attr("d", function(d){ return leftTip(d, boxW_funds, !point_right); })
            .style("opacity", 1)

        // Infobox for funds, text top
        d3.selectAll(".infofunds_text")
            .data([{x: xMouse, y: cy_box}])
            .transition(10)
            .attr("x", function(d){ return d.x + box_margin + text_padd.x + tooltip_to_right; })
            .attr("y", function(d){ return d.y - text_padd.y / 2;})
            .style("opacity", 1)
            .text(formatLabel(format.parse(today_funds.date)) + " (dia " + day_number +")");        

        // Infobox for funds, text bottom
        d3.selectAll(".infofunds_text2")
            .data([{x: xMouse, y: cy_box}])
            .transition(10)
            .attr("x", function(d){ return d.x + box_margin + text_padd.x + tooltip_to_right; })
            .attr("y", function(d){ return d.y + 12;})
            .style("opacity", 1)
            .text(text_today_minimum);

        // Infobox for funders, tip shadow
        d3.selectAll(".infofunders_tip_bg")
            .data([{x: (xMouse + tooltip_to_right_funders), y: cy_funders}])
            .transition(10)
            .attr("d", function(d){ return leftTip(d, boxW_funders, point_right); })
            .style("opacity", 1)

        // Infobox for funders, rect shadow
        d3.selectAll(".infofunders_bg")
            .data([{x: xMouse, y: cy_box_funders}])
            .transition(10)
            .attr("x", function(d){ return  d.x + box_margin + tooltip_to_right_funders; })
            .attr("y", function(d){ return d.y - (boxH_funders / 2); })
            .style("opacity", 1)

        // Infobox for funders, tip
        d3.selectAll(".infofunders_tip")
            .data([{x: (xMouse + tooltip_to_right_funders), y: cy_funders}])
            .transition(10)
            .attr("d", function(d){ return leftTip(d, boxW_funders, !point_right); })
            .style("opacity", 1)

        // Infobox for funders, rect
        d3.selectAll(".infofunders")
            .data([{x: xMouse, y: cy_box_funders}])
            .transition(10)
            .attr("x", function(d){ return  d.x + box_margin + tooltip_to_right_funders; })
            .attr("y", function(d){ return d.y - (boxH_funders / 2); })
            .style("opacity", 1)

        // Infobox for funders, text
        d3.selectAll(".infofunders_text")
            .data([{x: xMouse, y: cy_box_funders}])
            .transition(10)
            .attr("x", function(d){ return d.x + box_margin + text_padd.x + tooltip_to_right_funders; })
            .attr("y", function(d){ return d.y + text_padd.y / 2;})
            .style("opacity", 1)
            .text((+today_funders).toLocaleString("de-DE") + " donaciones" + " | Total: " + (+total_funders).toLocaleString("de-DE"));        

        // Circles for hovered day
        d3.selectAll(".day_circle_funds")
            .attr("cx", xMouse)
            .attr("cy", cy)

        d3.selectAll(".day_circle_funders")
            .attr("cx", xMouse)
            .attr("cy", cy_funders)
        }
}

GOTEO.utilities.offHover = function(){
    var fundsChart = GOTEO.charts.fundsChart,
        fundersChart = GOTEO.charts.fundersChart,
        format = GOTEO.formats.format,
        funds_current = fundsChart.getCurrent().value,
        funders_current = fundsChart.getCurrent().value,
        current_time = format.parse(fundsChart.getCurrent().time);

    d3.selectAll(".infobox")
        .style("opacity", 0);
    d3.selectAll(".day_circle_funds")
        .attr("cx", fundsChart.getTimeScale()(current_time))
        .attr("cy", fundsChart.getYScale()(funds_current));
    d3.selectAll(".day_circle_funders")
        .attr("cx", fundersChart.getTimeScale()(current_time))
        .attr("cy", fundersChart.getYScale()(funders_current));
}
