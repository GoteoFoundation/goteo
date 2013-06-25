//
// Goteo Analytics
// By Franc Camps-Febrer 
// 2013
// 
// Modificado por Julián Cánaves para implementarlo en beta
// 
//

    function updateGraph(project_id){
        // Properties for funding chart
        var funds_options = {
            size : {'w' : 540, 'h' : 250},
            margin : {'left' : 40, 'right' : 10, 'top' : 20, 'bottom' : 40},
            minimum_data : '',
            current : {value : '', time : ''},
            data_for_hover : '',
            t : d3.time.scale(),
            y : d3.scale.linear()}

        // Properties for cofunders chart
         var funders_options = {
            size : {'w' : 540, 'h' : 40},
            margin : {'left' : 40, 'right' : 10, 'top' : 5, 'bottom' : 20},
            minimum_data : '',
            data_for_hover : '',
            current : {value : '', time : ''},
            t : d3.time.scale(),
            y : d3.scale.linear()}

        var fundsChart, fundersChart;

        var start_date,
            deadline,
            day_number;

        var format = d3.time.format("%Y-%m-%d"),
            formatXaxis = d3.time.format("%b %d"),
            formatLabel = d3.time.format("%d %B");

        // Load fund and cofunders data for selected project
        function loadData(project_id){
            $.ajax({
                url: 'http://goteo:gotagota@beta.goteo.org/json/invests/'+project_id,
//                data: {id: project_id},  // 
                dataType: 'json'
            }).done(function(raw_data){
                initializeCharts(raw_data); 
            });
        }
 
        // ----------------------
        //
        // Create chart objects and render them
        //
        // ----------------------

        function initializeCharts(raw_data){
                    // Date when the project was published
                    start_date = format.parse(raw_data.dates[0].published);

                    // Deadline should be 40 days after that
                    // THIS SHOULD COME FROM DB TOO
                    deadline = d3.time.day.offset(start_date, 40);

                    // Generate array of invest objects
                    var invests = [];
                    raw_data['invests'].forEach(function(d){
                        invests.push({'amount' : d.amount, 
                                        'user' : d.user, 
                                        'date' : d.invested});
                    });

                    // Required minimum to achieve
                    var minimum = d3.sum(raw_data.minimum, function(d){ return +d.amount; });

                    // Generate daily data, funding and cofunders
                    var data = getFundingByDay(invests);
                   
                    // If project is active, get number of day after being published 
                    day_number = Math.floor((format.parse(_.last(data.funded_data).date) - (start_date))/86400000),

                    // Create chart Object for funds
                    fundsChart = new Chart(funds_options);
                    fundsChart.data = data.funded_data;
                    fundsChart.current = {'value' : _.last(data.funded_data).value,
                                            'time' : _.last(data.funded_data).date },
                    fundsChart.minimum_data = [{ 'date' : format(start_date), 'value' : 0},
                                    { 'date' : format(deadline), 'value' : minimum}];
                    fundsChart.minimum = minimum;
                    fundsChart.data_for_hover = _.groupBy(data.funded_data, function(d){ return d.date; });

                    // Create chart Object for funders
                    fundersChart = new Chart(funders_options);
                    fundersChart.data = data.funders_data; 
                    fundersChart.current = {'value' : _.last(data.funders_data).value,
                                            'time' : _.last(data.funders_data).date },
                    fundersChart.data_for_hover = _.groupBy(data.funders_data, function(d){ return d.date; });
                    
                    // Render charts and info
                    updateTitles();
                    renderFunds();
                    renderCofunders();
        }
    
        // ----------------------
        //
        // Get cumulative funding by day
        //
        // ----------------------

        function getFundingByDay(invests){ 
            // ---
            // Only add funding for days until deadline or last day of records
            // This is temporary. When on production, 
            // this must change to until deadline or current day.
            // ---

            // Last day of records only needed for local, static DB
            var last_day_of_db_records = new Date("November 16, 2012 00:00:00");

            // Today will always be current day when live
            var today = d3.min([d3.time.day.offset(deadline,1), last_day_of_db_records]);
                all_days = d3.time.day.range(start_date, today);
                
            var funded_data = [],
                funders_data = [],
                funded = 0;

            // Rollup number of funders per day, and amount of funding per day
            all_days.forEach(function(g){
                var funders = 0;
                invests.forEach(function(d){
                    // Dateformat needs to be exactly the same
                    // Alternative: come up with a function based on time
                    if (d.date === format(g)){
                        funded += +d.amount
                        funders += 1;
                    }
                });
                funded_data.push({value: funded, 'date' : format(g) })
                funders_data.push({value: funders, 'date' : format(g) })
            });

            return {'funded_data' : funded_data, 'funders_data' : funders_data};
        };

        // ----------------------
        //
        // Chart object
        //
        // ----------------------

        var Chart = function(options){
            this.size = options.size;
            this.margin = options.margin;
            this.minimum_data = options.minimum_data;
            this.current = options.current;
            this.t = options.t;
            this.y = options.y;
        }
   
        // ----------------------
        //
        // Update funds and days left in subtitle
        //
        // ----------------------

        function updateTitles(){
            $("#dias").html(40 - day_number);
            if (fundsChart.current.value){
                    $("#funded").html(fundsChart.current.value);
                    $("#de").html('de');
                    $("#minimum").html(fundsChart.minimum);
                    $("#euros").html("de euros.");
            } else {
                $("#funded").html('No hay donaciones.');
                    $("#de").html('');
                    $("#minimum").html('');
                    $("#euros").html('');
            }
        }
   
        // ----------------------
        //
        // Render chart for funds gathered by day
        //
        // ----------------------

        function renderFunds(){
            var columnW = 10;

            // Make local variables for performance
            var Chart = fundsChart,
                data = Chart.data,
                size = Chart.size,
                margin = Chart.margin,
                t = Chart.t,
                y = Chart.y,
                minimum_data = Chart.minimum_data,
                minimum = Chart.minimum,
                current = Chart.current.value,
                current_time = Chart.current.time;

            // Define scales
            t.domain([start_date, deadline])
                .range([margin.left,size.w + margin.left]),
            y.domain([0,d3.max([minimum, current])])
                .range([size.h + margin.top, margin.top]);

            // Create SVG
            var chart = d3.select("#funds").append("svg:svg")
                .attr("id", "svg_funds")
                .attr("width", size.w + margin.left + margin.right)
                .attr("height", size.h + margin.top + margin.bottom)
    
            // Area function
            var area = d3.svg.area()
                .x(function(d) { return t(format.parse(d.date)); })
                .y0(function(d) { return y(0); })
                .y1(function(d) { return y(d.value); })
    
            // Line function
            var line = d3.svg.line()
                .x(function(d) { return t(format.parse(d.date)); })
                .y(function(d) { return y(d.value); });
    
            // Axes
            var xAxis = d3.svg.axis()
                .orient("bottom")
                .scale(t)
                .ticks(10)
                .tickSize(10, 10, 0)
                .tickFormat(formatXaxis)
                .tickSubdivide(true)
    
            var yAxis = d3.svg.axis()
                .orient("left")
                .scale(y)
                .ticks(2)
                .tickPadding(5)
                .tickSize(size.w, size.w, 0)
                .tickSubdivide(true)
    
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
                .on("mouseover", onHover)
                .on("mousemove", onHover)
                .on("mouseout", offHover)
        
            chart.append("path")
                .attr("d", line(data))
                .attr("class", "funds_line")
                .on("mouseover", onHover)
                .on("mousemove", onHover)
                .on("mouseout", offHover)
   
            // Draw circle for last day with data or hovered day 
            chart.append("circle")
                .attr("class", "day_circle_funds day_circle")
                .attr("cx", t(format.parse(data[data.length - 1].date)))
                .attr("cy", y(current))
                .attr("r", 5)
                .on("mouseover", onHover)
                .on("mousemove", onHover)
                .on("mouseout", offHover)
   
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
                    .attr("height", - y(minimum) + y(current))
                    .attr("width", columnW)
                    .style("fill", "#96238f");
            }
            
            // Level marks on column for minimum and funded 
            var line_width = 26;
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
   
            // -------------------
            //
            // Hover infobox
            //
            // ------------------
 
            // Draw arrow tip of infobox as a polygon
            var length = 37
            var tipH = 20;
            var p = {topleft : (size.w + margin.left - length) + "," + (y(current) - tipH/2),
                        bottomleft : (size.w + margin.left - length) + "," + (y(current) + tipH/2),
                        bottomright : (size.w + margin.left - 10) + "," + (y(current) + tipH/2),
                        tip : (size.w + margin.left) + "," + (y(current)),
                        topright : (size.w + margin.left - 10) + "," + (y(current) - tipH/2)}
    
            chart.append("polygon")
                .attr("points", p.topleft + " " + p.topright + " " + 
                        p.tip + " " + p.bottomright + " " + p.bottomleft)
                .style("fill", "#58595b")
    
            chart.append("text")
                .attr("x", size.w + margin.left - 20)
                .attr("y", y(current) + tipH/2 - 6)
                .attr("class", "arrow_funded_height")
                .text(((current)/(minimum)*100).toFixed(0) + "%")
    
            // Info box
            boxW = 225,
            boxH = 50;
            var pos = { x: 10, y: 20 };
            var tipW = 5,
                tipH = 10,
                mouse_padd = 20;

            // Filter function
            var filter = chart.append("svg:defs")
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
                .on("mouseover", onHover)
                .on("mousemove", onHover)
                .on("mouseout", offHover);
            
            // Infobox rect shadow
            chart.selectAll(".infofunds_bg")
                .data([pos])
                .enter().append("rect")
                .attr("class", "infobox infobox_funds_bg infofunds_bg")
                .attr("x", function(d){ return d.x + tipW + mouse_padd; })
                .attr("y", function(d){ return d.y - boxH/2; })
                .attr("width", boxW)
                .attr("height", boxH)
                .on("mouseover", onHover)
                .on("mousemove", onHover)
                .on("mouseout", offHover)
                .attr("filter", "url(#blur)");

            // Infobox tip
            chart.selectAll(".infofunds_tip")
                .data([pos])
                .enter().append("path")
                .attr("d", function(d){ return leftTip(d, boxW, true); })
                .attr("class", "infobox infofunds_tip")
                .on("mouseover", onHover)
                .on("mousemove", onHover)
                .on("mouseout", offHover)
            
            // Infobox rect
            chart.selectAll(".infofunds")
                .data([pos])
                .enter().append("rect")
                .attr("class", "infobox infofunds")
                .attr("x", function(d){ return d.x + tipW + mouse_padd; })
                .attr("y", function(d){ return d.y - boxH/2; })
                .attr("width", boxW)
                .attr("height", boxH)
                .on("mouseover", onHover)
                .on("mousemove", onHover)
                .on("mouseout", offHover)
    
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

        function leftTip(d, box_length, point_left){
                var tipW = 5,
                    tipH = 10,
                    mouse_padd = 20;
                if (point_left){
                    return "M" + (d.x + mouse_padd) + "," + (d.y)
                    + "L" + (d.x + mouse_padd + tipW) + "," + (d.y - tipH/2) 
                    + "L" + (d.x + mouse_padd + tipW) + "," + (d.y + tipH/2) + "Z";
                }
                return "M" + (d.x + mouse_padd + box_length + 2*tipW) + "," + (d.y)
                    + "L" + (d.x + mouse_padd + box_length + tipW) + "," + (d.y - tipH/2) 
                    + "L" + (d.x + mouse_padd + box_length + tipW) + "," + (d.y + tipH/2) + "Z";
        }
    
        // ----------------------
        //
        // Render chart for number of donations by day
        //
        // ----------------------

        function renderCofunders(){
            // Make local variables for performance
            var Chart = fundersChart,
                data = Chart.data,
                size = Chart.size,
                margin = Chart.margin,
                t = Chart.t,
                y = Chart.y,
                minimum_data = Chart.minimum_data,
                minimum = Chart.minimum,
                current = Chart.current.value,
                current_time = Chart.current.time,
                data_for_hover = Chart.data_for_hover;

            // Create scales
            t.domain([start_date, deadline])
                .range([margin.left,size.w + margin.left]);
            y.domain([0,d3.max(data, function(d){ return d.value; })])
                .range([size.h + margin.top, margin.top]);
    
            // Generate SVG
            var chart = d3.select("#cofund").append("svg:svg")
                .attr("id", "svg_funders")
                .attr("width", size.w + margin.left + margin.right)
                .attr("height", size.h + margin.top + margin.bottom)
    
            // Area and line functions
            var area = d3.svg.area()
                .x(function(d) { return t(format.parse(d.date)); })
                .y0(function(d) { return y(0); })
                .y1(function(d) { return y(d.value); })
    
            var line = d3.svg.line()
                .x(function(d) { return t(format.parse(d.date)); })
                .y(function(d) { return y(d.value); });
    
            // Axes
            var xAxis = d3.svg.axis()
                .orient("bottom")
                .scale(t)
                .ticks(10)
                .tickSize(10, 10, 0)
                .tickFormat(formatXaxis)
                .tickSubdivide(true)
    
            var yAxis = d3.svg.axis()
                .orient("left")
                .scale(y)
                .ticks(2)
                .tickPadding(5)
                .tickSize(size.w, size.w, 0)
    
            chart.append("g")
                .attr("class", "x axis")
                .attr("transform", "translate(0," + (margin.top + size.h) + ")")
                .call(xAxis)
            
            chart.append("g")
                .attr("class", "y axis")
                .attr("transform", "translate(" + (margin.left + size.w) + ",0)")
                .call(yAxis)
            
            // Add areas and lines
            chart.append("path") 
                .attr("class", "area funders_area")
                .attr("d", area(data))
                .on("mouseover", onHover)
                .on("mousemove", onHover)
                .on("mouseout", offHover)
    
            chart.append("path")
                .attr("d", line(data))
                .attr("class", "funders_line")
                .on("mouseover", onHover)
                .on("mousemove", onHover)
                .on("mouseout", offHover)
  
            // Circle for current or hovered day
            chart.append("circle")
                .attr("class", "day_circle_funders day_circle")
                .attr("cx", t(format.parse(data[data.length - 1].date)))
                .attr("cy", y(data[data.length - 1].value))
                .attr("r", 5)
                .style("stroke", "#313B96")
                .on("mouseover", onHover)
                .on("mousemove", onHover)
                .on("mouseout", offHover)

            // Info box
            box_length = 150,
            narrowness = 36;
            var pos = { x: 10, y: 20 };
            var tipW = 5,
                tipH = 10,
                mouse_padd = 20;

            // Blur function for shadow
            var filter = chart.append("svg:defs")
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
                .on("mouseover", onHover)
                .on("mousemove", onHover)
                .on("mouseout", offHover)
                .attr("filter", "url(#blur)");
           
            // Infobox rect shadow 
            chart.selectAll(".infofunders_bg")
                .data([pos])
                .enter().append("rect")
                .attr("class", "infobox infofunders_bg")
                .attr("x", function(d){ return d.x + tipW + mouse_padd; })
                .attr("y", function(d){ return d.y - narrowness/2; })
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
                .attr("y", function(d){ return d.y - narrowness/2; })
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

        function onHover(){
            // Get local data
            var t = fundsChart.t,
                y = fundsChart.y,
                yFunders = fundersChart.y,
                xMouse = d3.mouse(this)[0],
                yMouse = d3.mouse(this)[1],
                now = t.invert(xMouse),
                day = d3.time.day(now),
                size = fundsChart.size,
                minimum = fundsChart.minimum;
               
            // Get size of box
            var boxH_funds = +d3.select(".infofunds").attr("height"),
                boxH_funders = +d3.select(".infofunders").attr("height"),
                boxW_funds = +d3.select(".infofunds").attr("width"),
                boxW_funders = +d3.select(".infofunders").attr("width"),
                data_for_hover = fundsChart.data_for_hover;

            var today_funds = fundsChart.data_for_hover[format(day)][0],
                tomorrow = fundsChart.data_for_hover[format(d3.time.day.offset(day, 1))][0],
                this_minute = (now - day)/1000,
                day_number = Math.floor((format.parse(today_funds.date) - (start_date))/86400000),
                today_minimum = Math.floor((minimum / 40)*day_number),
                y_today_funds = y(today_funds.value),
                y_tomorrow_funds = y(tomorrow.value);
            
            // Get coordinates of funds circle
            var cx = t(format.parse(today_funds.date));
            var cy = y_today_funds + (y_tomorrow_funds- y_today_funds)*this_minute/86400;

            var total_funders = d3.sum(_.pluck(fundersChart.data_for_hover, function(d){ return d.value; })),
                today_funders = fundersChart.data_for_hover[format(day)][0].value,
                tomorrow_funders = fundersChart.data_for_hover[format(d3.time.day.offset(day, 1))][0].value,
                y_today_funders = yFunders(today_funders),
                y_tommorrow_funders = yFunders(tomorrow_funders);

            // Get Y coordinate of funders circle
            var cy_funders = y_today_funders + (tomorrow_funders - today_funders)*this_minute/86400;

            var point_right = (cx > size.w/2);
                tooltip_to_right = (point_right) ? (- boxW_funds - 60) : 0,
                tooltip_to_right_funders = (point_right) ? (- boxW_funders - 60) : 0;

            // Infobox for funds, tip shadow
            d3.selectAll(".infofunds_tip_bg")
                .data([{x: (xMouse + tooltip_to_right), y: cy}])
                .transition(10)
                .attr("d", function(d){ return leftTip(d, boxW_funds, !point_right); })
                .style("opacity", 1)
    
            // Infobox for funds, rect shadow
            d3.selectAll(".infofunds_bg")
                .data([{x: xMouse, y: cy}])
                .transition(10)
                .attr("x", function(d){ return d.x + 5 + 20 + tooltip_to_right; })
                .attr("y", function(d){ return d.y - (boxH_funds/2); })
                .style("opacity", 1)
    
            // Infobox for funds
            d3.selectAll(".infofunds")
                .data([{x: xMouse, y: cy}])
                .transition(10)
                .attr("x", function(d){ return d.x + 5 + 20 + tooltip_to_right; })
                .attr("y", function(d){ return d.y - (boxH_funds/2); })
                .style("opacity", 1)
    
            // Infobox for funds, tip
            d3.selectAll(".infofunds_tip")
                .data([{x: (xMouse + tooltip_to_right), y: cy}])
                .transition(10)
                .attr("d", function(d){ return leftTip(d, boxW_funds, !point_right); })
                .style("opacity", 1)
    
            // Infobox for funds, text top
            d3.selectAll(".infofunds_text")
                .data([{x: xMouse, y: cy}])
                .transition(10)
                .attr("x", function(d){ return d.x + 35 + tooltip_to_right; })
                .attr("y", function(d){ return d.y - 3;})
                .style("opacity", 1)
                .text(formatLabel(format.parse(today_funds.date)) + " (dia " + day_number +")");        

            // Infobox for funds, text bottom
            d3.selectAll(".infofunds_text2")
                .data([{x: xMouse, y: cy}])
                .transition(10)
                .attr("x", function(d){ return d.x + 35 + tooltip_to_right; })
                .attr("y", function(d){ return d.y + 12;})
                .style("opacity", 1)
                .text(today_funds.value + " de " + minimum + " (" + today_minimum + " recomendado)");        

            // Infobox for funders, tip shadow
            d3.selectAll(".infofunders_tip_bg")
                .data([{x: (xMouse + tooltip_to_right_funders), y: cy_funders}])
                .transition(10)
                .attr("d", function(d){ return leftTip(d, boxW_funders, point_right); })
                .style("opacity", 1)
    
            // Infobox for funders, rect shadow
            d3.selectAll(".infofunders_bg")
                .data([{x: xMouse, y: cy_funders}])
                .transition(10)
                .attr("x", function(d){ return  d.x + 5 + 20 + tooltip_to_right_funders; })
                .attr("y", function(d){ return d.y - (boxH_funders/2); })
                .style("opacity", 1)

            // Infobox for funders, tip
            d3.selectAll(".infofunders_tip")
                .data([{x: (xMouse + tooltip_to_right_funders), y: cy_funders}])
                .transition(10)
                .attr("d", function(d){ return leftTip(d, boxW_funders, !point_right); })
                .style("opacity", 1)
    
            // Infobox for funders, rect
            d3.selectAll(".infofunders")
                .data([{x: xMouse, y: cy_funders}])
                .transition(10)
                .attr("x", function(d){ return  d.x + 5 + 20 + tooltip_to_right_funders; })
                .attr("y", function(d){ return d.y - (boxH_funders/2); })
                .style("opacity", 1)

            // Infobox for funders, text
            d3.selectAll(".infofunders_text")
                .data([{x: xMouse, y: cy_funders}])
                .transition(10)
                .attr("x", function(d){ return d.x + 35 + tooltip_to_right_funders; })
                .attr("y", function(d){ return d.y + 3;})
                .style("opacity", 1)
                .text(today_funders + " donaciones" + " | Total: " + total_funders);        

            // Circles for hovered day
            d3.selectAll(".day_circle_funds")
                .attr("cx", xMouse)
                .attr("cy", cy)

            d3.selectAll(".day_circle_funders")
                .attr("cx", xMouse)
                .attr("cy", cy_funders)
        }
    
        function offHover(){
            d3.selectAll(".infobox")
                .style("opacity", 0)

            d3.selectAll(".day_circle_funds")
                .attr("cx", fundsChart.t(format.parse(fundsChart.current.time)))
                .attr("cy", fundsChart.y(fundsChart.current.value))

            d3.selectAll(".day_circle_funders")
                .attr("cx", fundersChart.t(format.parse(fundersChart.current.time)))
                .attr("cy", fundersChart.y(fundersChart.current.value))
        }
   
        // Initialize 
        loadData(project_id);
        };


//$(document).ready(function(){
    
    /*
     * Esto no se usa en beta, se hace la llamada a updateGraph con la id de proyecto desde la vista
     *
    // Load list of projects
    // This functionality will be removed from the final version
    $("#project_selection").load("projects.php", function(){
        $("#project_selector").change(function(){ 
            // Remove previous chart when selecting a new one
            // This functionality will be removed from the final version
            d3.selectAll(".svg_funds").remove();
            d3.selectAll(".svg_funders").remove();

            // Generate chart with id from selector
            var project_id = $("#project_selector").val();
            updateGraph(project_id); 
        });
    });
    */

//});
