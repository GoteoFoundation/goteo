/**
 * Created by auipga on 18.08.14.
 */

jQuery(document).ready(function($){

    $('head').append('<style type="text/css">\
    #bsdebug {\
        position: fixed;\
        top: 10px;\
        left: 10px;\
        background-color: rgba(255, 255, 255, 0.80);\
        color: #00000;\
        z-index: 9999;\
    }\
    #bsdebug table {\
        border: 1px;\
    }\
    #bsdebug td,\
    #bsdebug th {\
        border: 1px solid black;\
        padding:2px;\
    }\
    #bsdebug td {\
        text-align: center;\
    }\
    #bsdebug .bold {\
            font-weight: bold;\
    }\
    #bsdebug .highlighted {\
        background-color: rgba(15, 230, 0, 0.67);\
    }\
    </style>');

    $('body').append('<div id="bsdebug">\
        <table>\
            <tr id="bsdebug-size">\
                <td colspan="7" style="text-align: left">\
                    <span class="bold hidden-tn hidden-xxs"><a href="https://github.com/auipga/bootstrap-xxs/">BS-Debug</a></span>\
                    | w<span class="hidden-tn">idth</span>:\
                    <span class="bold" id="bsdebug-width">?</span>px\
                    <span title="ScrollbarWidth" class="small" style="border-bottom: 1px black dotted; cursor: help;">(-'+getScrollbarWidth()+'px)</span>\
                    | h<span class="hidden-tn">eigth</span>:\
                    <span class="bold" id="bsdebug-height">?</span>px\
                </td>\
            </tr>\
            <tr id="bsdebug-breakpoint">\
                <th></th>\
                <td><span class="visible-tn-block  highlighted">TN</span><span class="hidden-tn">TN</span></td>\
                <td><span class="visible-xxs-block highlighted">XXS</span><span class="hidden-xxs">XXS</span></td>\
                <td><span class="visible-xs-block  highlighted">XS</span><span class="hidden-xs">XS</span></td>\
                <td class="hidden-tn"><span class="visible-sm-block  highlighted">SM</span><span class="hidden-sm">SM</span></td>\
                <td class="hidden-tn hidden-xxs"><span class="visible-md-block  highlighted">MD</span><span class="hidden-md">MD</span></td>\
                <td class="hidden-tn hidden-xxs"><span class="visible-lg-block  highlighted">LG</span><span class="hidden-lg">LG</span></td>\
            </tr>\
            <tr id="bsdebug-min" class="small"><th>Min</th><td>0px</td><td>≥384px</td><td>≥480px</td><td class="hidden-tn">≥768px</td><td class="hidden-tn hidden-xxs">≥992px</td><td class="hidden-tn hidden-xxs">≥1200px</td></tr>\
            <tr id="bsdebug-max" class="small"><th>Max</th><td>&lt;383px</td><td>&lt;479px</td><td>&lt;767px</td><td class="hidden-tn">&lt;991px</td><td class="hidden-tn hidden-xxs">&lt;1199px</td><td class="hidden-tn hidden-xxs">&infin;</td></tr>\
            <tr id="bsdebug-diff"><th>Diff</th><td></td><td></td><td></td><td class="hidden-tn"></td><td class="hidden-tn hidden-xxs"></td><td class="hidden-tn hidden-xxs"></td></tr>\
        </table>\
    </div>');

    var ScrollbarWidth = getScrollbarWidth();

    $(window).on('resize', function (e) {
        var $window = $(window);
        var width = $window.width() + ScrollbarWidth;
        var height = $window.height();
        $('#bsdebug-width').text(width);
        $('#bsdebug-height').text(height);

        var breakpoints = [0, 384, 480, 768, 992, 1200, 9999];
        $('#bsdebug-diff>td').each(function (i) {
            var diff = width - breakpoints[i];
            if (diff >= 0) {
                $("#bsdebug-min td").eq(i).css({ 'background-color':'rgba(127, 219, 124, 0.67)', 'font-weight': 'bold' });
            } else {
                $("#bsdebug-min td").eq(i).css({ 'background-color':'', 'font-weight': '' });
            }
            if (breakpoints[i + 1] > width) {
                $("#bsdebug-max td").eq(i).css({ 'background-color':'rgba(127, 219, 124, 0.67)', 'font-weight': 'bold' });
            } else {
                $("#bsdebug-max td").eq(i).css({ 'background-color':'', 'font-weight': '' });
            }
            if (diff > 0) {
                diff = "+" + diff;
            }
            $(this).text(diff);
        });
    });
});

/*
source: http://stackoverflow.com/a/13382873/816362
*/
function getScrollbarWidth() {
    var outer = document.createElement("div");
    outer.style.visibility = "hidden";
    outer.style.width = "100px";
    document.body.appendChild(outer);

    var widthNoScroll = outer.offsetWidth;
    // force scrollbars
    outer.style.overflow = "scroll";

    // add innerdiv
    var inner = document.createElement("div");
    inner.style.width = "100%";
    outer.appendChild(inner);

    var widthWithScroll = inner.offsetWidth;

    // remove divs
    outer.parentNode.removeChild(outer);

    return widthNoScroll - widthWithScroll;
}
