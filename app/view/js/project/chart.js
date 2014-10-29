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
// Chart object
//
// ----------------------

GOTEO.ChartObject = (function() {
    var ChartObjectConstructor = function ChartObject(options) {
        var size = options.size,
            margin = options.margins || { 'left' : 40, 'right' : 10, 'top' : 5, 'bottom' : 30},
            minimum_data = options.minimum_data,
            data, data_for_hover, optimum, minimum,
            current = options.current,
            t = options.t || d3.time.scale(),
            y = options.y || d3.scale.linear();
        this.setData = function(newData) {
            data = newData;
        },
        this.getData = function() {
            return data;
        },
        this.setHoverData = function(newData) {
            data_for_hover = newData;
        },
        this.getHoverData = function() {
            return data_for_hover;
        },
        this.setWidth = function(newWidht) {
            size.w = newWidth;
        },
        this.setHeight = function(newHeight) {
            size.h = newHeight;
        },
        this.getSize = function() {
            return size;
        },
        this.setMargin = function(newMargins) {
            margin = newMargins;
        },
        this.getMargin = function() {
            return margin;
        },
        this.setMinimumData = function(newMinimumData) {
            minimum_data = newMinimumData;
        },
        this.getMinimumData = function() {
            return minimum_data;
        },
        this.setMinimum = function(newMinimum) {
            minimum = newMinimum;
        },
        this.getMinimum = function() {
            return minimum;
        },
        this.setOptimum = function(newOptimum) {
            optimum = newOptimum;
        },
        this.getOptimum = function() {
            return optimum;
        },
        this.setCurrent = function(newCurrent) {
            current = newCurrent;
        },
        this.getCurrent = function() {
            return current;
        },
        this.getTimeScale = function() {
            return t;
        },
        this.getYScale = function() {
            return y;
        },
        this.setRenderFn = function(renderFn) {
            this.render = renderFn;
        }
    }
    return ChartObjectConstructor;
}());
