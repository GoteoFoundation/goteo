/*
@licstart  The following is the entire license notice for the
JavaScript code in this page.

Copyright (C) 2010  Goteo Foundation

The JavaScript code in this page is free software: you can
redistribute it and/or modify it under the terms of the GNU
General Public License (GNU GPL) as published by the Free Software
Foundation, either version 3 of the License, or (at your option)
any later version.  The code is distributed WITHOUT ANY WARRANTY;
without even the implied warranty of MERCHANTABILITY or FITNESS
FOR A PARTICULAR PURPOSE.  See the GNU GPL for more details.

As additional permission under GNU GPL version 3 section 7, you
may distribute non-source (e.g., minimized or compacted) forms of
that code without the copy of the GNU GPL normally required by
section 4, provided you include this license notice and a URL
through which recipients can access the Corresponding Source.


@licend  The above is the entire license notice
for the JavaScript code in this page.
*/

$(function(){

    var query = {
        view: 'list_projects',
        page: 0,
        limit: 9,
        sdg: ''
    };

    function resetQuery() {
        query = {
            page: 0,
            limit: 9,
            sdg: ''
        };
    }

    function setQuery(new_query) {
        query = {
            view: "view" in new_query? new_query.view: query.view,
            page: "page" in new_query? new_query.page : query.page,
            limit: "limit" in new_query? new_query.limit : query.limit,
            sdg: "sdg" in new_query? new_query.sdg : query.sdg
        }
    }
    
    let sdgList = [];
    let view = 'list_projects';

    fetch('/api/categories/sdg')
        .then((response) => {
            if (response.ok)
                return response.json();
            else
                return [];  
        })
        .then(data => sdgList = data);

    function activateMosaic(event) {
        if (view != 'mosaic') {
            view = 'mosaic';

            document.getElementById('impact-discover-mosaic').classList.add('active');
            document.getElementById('impact-discover-projects').classList.remove('active');
            document.getElementById('impact-discover-map').classList.remove('active');

            document.getElementById('activate-mosaic').classList.add('active');
            document.getElementById('activate-projects').classList.remove('active');
            document.getElementById('activate-map').classList.remove('active');

            refreshSDG()
        }
    }

    function activateProjects(event) {

        if (view != 'list_projects') {
            view = 'list_projects';

            document.getElementById('impact-discover-mosaic').classList.remove('active');
            document.getElementById('impact-discover-projects').classList.add('active');
            document.getElementById('impact-discover-map').classList.remove('active');

            document.getElementById('activate-mosaic').classList.remove('active');
            document.getElementById('activate-projects').classList.add('active');
            document.getElementById('activate-map').classList.remove('active');

            refreshSDG() 
        }
    }

    function activateMap(event) {

        if (view != 'map') {
            view = 'map';

            document.getElementById('impact-discover-mosaic').classList.remove('active');
            document.getElementById('impact-discover-projects').classList.remove('active');
            document.getElementById('impact-discover-map').classList.add('active');

            document.getElementById('activate-mosaic').classList.remove('active');
            document.getElementById('activate-projects').classList.remove('active');
            document.getElementById('activate-map').classList.add('active');

            $(".more-projects-button").addClass('hidden');
            refreshSDG()
        }
    }

    document.getElementById('activate-mosaic').onclick = activateMosaic;
    document.getElementById('activate-projects').onclick = activateProjects;
    document.getElementById('activate-map').onclick = activateMap;

    //reset SDG JSON
    function resetSDG() {
        $.each(sdgList, function(key, value){
            sdgList[key]["active"] = false
        })
    }

    // reset SDG select
    function resetSDGSelect() {
        $(".impact-discover-filters select option").remove();
        $(".impact-discover-filters select").append("<option>Filtra por Objetivos de Desarrollo sostenible</option");
    }

    // check if SDG option has selected footprint
    function hasFootprint (sdg, footprint) {
        var footprints = sdg.footprints;
        return footprints.find(element => element.id == footprint) != undefined;
    }

    // add SDG to SDG select
    function addSDGToSelect(sdg) {
        $(".impact-discover-filters select").append('<option data-footprints="'+sdg.footprints.map(footprint => footprint.id).join(',')+'" value="' + sdg.id + '">'+sdg.name+'</option>');
    }

    // reset SDG icons
    function resetSDGIcons() {
        $('.impact-discover-sdg-list h2 list-projects').removeClass('active');
        $('.impact-discover-sdg-list h2 search-projects').addClass('active');
        $("#sdg-icons .col").html("");
    }

    // add SDG to icon list
    function addSDGIcon(sdg) {
        $('.impact-discover-sdg-list h2 list-projects').addClass('active');
        $('.impact-discover-sdg-list h2 search-projects').removeClass('active');
        var icon = '<img src="/assets/img/sdg/sdg'+sdg.id+'.svg" />';
        var removeIcon = '<a class="close flip" href="#"><i class="icon icon-close"></i></a>';
        $("#sdg-icons .col").append('<div class="sdgicon" data-sdg="'+sdg.id+'">'+icon+removeIcon+'</div>');
    }

    // activate SDG on JSON
    function activateSDG(sdg){
        $.each(sdgList, function(key, value){
            if (value.id == sdg.id) sdgList[key]["active"] = true
        })
        addSDGToSelect(sdg);
    }

    // remove SDG
    function removeSDG(sdg){
        $.each(sdgList, function(key, value){
            if (value.id == sdg) sdgList[key]["active"] = false
        })
        refreshSDG();
    }

    function isSDGActive(sdg) {
        return sdg.active;
    }

    // check SDG option for mobile list
    function checkSDGoption(sdg){
        $('input[name="'+sdg.id+'"]').prop("checked",true);
    }


    // reset SDG options for mobile list
    function resetSDGcheckboxes() {
        $('#filters-sdg-list input[type=checkbox]').prop("checked",false);
    }

    // get active SDG
    function getActiveSDG(){
        var sdgActive = sdgList.filter(function(sdg) {
            return isSDGActive(sdg) ? sdg.name : false ;
        });
        return sdgActive;
    }

    // refresh SDG icons
    function refreshSDG() {
        resetSDGIcons();
        resetSDGcheckboxes();
        var sdgActive = getActiveSDG();

        var sdgArray = [];

        $.each(sdgActive, function(key,sdg){
            addSDGIcon(sdg);
            checkSDGoption(sdg);
            sdgArray.push(sdg.id);
        });

        resetQuery();
        resetProjects();

        loadProjects(sdgArray);
    }

    function resetListProjects() {
        $('.impact-discover-projects > div.container > div').remove();

        $(".more-projects-button").removeClass('hidden');
    }

    function resetMosaic() {
        $('.impact-discover-projects > div.container > div').remove();

        $(".more-projects-button").removeClass('hidden');
    }

    function resetProjects() {
        if (view == 'mosaic')
            resetMosaic();
        else if (view == 'list_projects')
            resetListProjects();
    }

    function loadProjects(sdg) {

        if ( view == 'map' )
            loadMapProjects(sdg);
        else if ( view == 'list_projects' )
            loadListProjects(sdg)
        else
            loadMosaicProjects(sdg)
    }

    function loadMosaicProjects(sdg) {
        $('.impact-discover-mosaic > div.container').after('<div class="loading-container"></div>');
        var url = "/api/projects_by_sdg";

        setQuery({
            view: view,
            sdg: sdg.join(",")
        })

        $.get( url, query, function( data ) {
            $('.impact-discover-mosaic > div.container').append( data.html );   
        })
        .done(function(){
            $('.loading-container').remove();
        });
    }

    function addProjectsToMap(projects) {

        $('#map').attr('data-projects', JSON.stringify({projects: projects}));
        let event = new Event('goteo_map_add_projects', { bubbles: true});
        document.getElementById('map').dispatchEvent(event);
    }

    function loadMapProjects(sdg) {
        $('.impact-discover-map > div.container').before('<div class="loading-container"></div>');
        var url = "/api/projects_by_sdg";

        setQuery({
            view: 'json',
            sdg: sdg.join(","),
            limit: 0
        })

        $.get( url, query, function( data ) {
            addProjectsToMap(data.projects);
        })
        .done(function(){
            $('.loading-container').remove();
        });
    }


    function loadListProjects(sdg) {
        $('.impact-discover-projects > div.container').after('<div class="loading-container"></div>');
        var url = "/api/projects_by_sdg";

        setQuery({
            view: view,
            sdg: sdg.join(",")
        })

        $.get( url, query, function( data ) {
            $('.impact-discover-projects > div.container').append( data.html );
            if (data.total == data.page * data.limit + data.result_total)
                $(".more-projects-button").addClass('hidden');
        })
        .done(function(){
            $('.loading-container').remove();
        });

    }

    // reset footprints active status
    function resetFootprints() {
        $("a[data-footprint]").removeClass("active");
    }

    // activate footprints by sdg selected
    function activateFootprints(footprints) {
        $.each(footprints, function(key, footprint){
            $('a[data-footprint="'+footprint+'"]').addClass("active");
        })
    }

    // filter SDG select options by footprint
    function filterSDGByFootprint (footprint) {
        resetSDGSelect();
        resetSDG();
        var options = sdgList.filter(function(sdg) {
            return hasFootprint(sdg,footprint) ? sdg.name : false ;
        });
        $.each(options,function(key,option){
            if (option) {
                activateSDG(option);
            }
        });
        refreshSDG();
    }

    // filter footprints by SDG
    function filterFootprintBySDG (footprint) {
        resetFootprints();
        resetSDGIcons();
        activateFootprints(footprint);
    }

    function expandMobileFilter() {
        $("#filters-footprints").slideDown();
        $("#filters-sdg-list").slideDown();
        $("#filters-mobile").css({"width":"100%"});
        $("#filters-mobile .close").show();
    }

    function contractMobileFilter() {
        $("#filters-footprints").slideUp();
        $("#filters-sdg-list").slideUp();
        $("#filters-mobile .close").hide();
        $("#filters-mobile").css({"width":"50%"});
    }

    function needsScroll() {
        if (view == 'mosaic')
            var $last = $("#impact-discover-mosaic div:last");
        if (view == 'list_projects')
            var $last = $("#impact-discover-projects div.row:last");
        else
            return false;

        return $(window).scrollTop() >= $last.offset().top - $last.height()
    }

    /*
    $(window).on('resize scroll', function() {
        if(needsScroll() && !$('.loading-container').length) {
            sdg = [];
            //TODO: find selected SDG
            var sdgActive = getActiveSDG();

            var sdgArray = [];

            $.each(sdgActive, function(key,sdg){
                sdgArray.push(sdg.id);
            });

            setQuery({
                page: query.page + 1
            });
        
            loadProjects(sdgArray);
        };
    }); */

    // bind click on footprint
    $("#filters-footprints").on("click","a", function(e){
        e.preventDefault();
        resetFootprints();
        footprint = $(this).attr("data-footprint");
        filterSDGByFootprint(footprint);
        $(this).addClass("active");
        if ($(window).width()<721) {
            $("#filters-footprints").slideUp();
            contractMobileFilter();
        }
        $("#filters-sdg-list").slideUp();
    });

    // bind Footprints select change
    $(".impact-discover-filters").on("change","select", function(e){
        footprints = $(this).find("option:selected").attr("data-footprints").split(",");
        filterFootprintBySDG(footprints);
    });

    // bind SDG close icon
    $("#sdg-icons").on("click",".close", function(e){
        e.preventDefault();
        sdg = $(this).parent().attr("data-sdg");
        removeSDG(sdg);
    })

    // bind filter mobile
    $("#filters-mobile").on("click","a", function(e){
        e.preventDefault();
        expandMobileFilter();
    })

    // bind reset sdg on mobile list
    $("#filters-sdg-list").on("click","a#reset-sdg", function(e){
        e.preventDefault();
        resetSDGcheckboxes();
    });

    // bind submit button on sdg list (mobile)
    $("#filters-sdg-list").on("click","button",function(e){
        $('#filters-sdg-list input').each(function(key,checkbox){
            if (checkbox.checked) {
                var sdg = {}
                sdg.id = checkbox.name;
                activateSDG(sdg);
            }
        });
        refreshSDG();
        contractMobileFilter();
    });

    // bind close icon at mobile filters
    $("#filters-mobile").on("click","a.close",function(e){
        e.preventDefault();
        contractMobileFilter();
    });

    $(".more-projects-button").on('click', function (e) {
        e.preventDefault();
        var $button = $(this);

        var sdgActive = getActiveSDG();
        var sdgArray = [];
        $.each(sdgActive, function(key,sdg){
            sdgArray.push(sdg.id);
        });

        setQuery({
            page: query.page + 1
        });
        loadProjects(sdgArray);
    });

});
