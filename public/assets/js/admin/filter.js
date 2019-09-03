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

$(function () {
    var filter;
    var newFilter;

    var form = $('#filter-form');
    var title = $('#form-title');
    var startdate = $('#form-startdate');
    var enddate = $('#form-enddate');
    var predefineddate = $('#form-predefineddate');
    var role = $('#form-role');
    var projects = $('#form-projects');
    var calls = $('#form-calls');
    var matchers = $('#form-matchers');
    var status = $('#form-status');
    var typeofdonor = $('#form-typeofdonor');
    var foundationdonor = $('#form-foundationdonor');
    var wallet = $('#form-wallet');
    var cert = $('#form-cert');
    var location = $('#form-project_location');

    function changeForm(role){
        $('#form-admin-filters-dependent').hide(400);
        $('body,html').animate({scrollTop : $('#form-admin-filters-dependent').height()}, 500);

        if (role == "donor") {
            projects.show();
            calls.show();
            matchers.show()
            status.show();
            typeofdonor.show();
            foundationdonor.show();
            cert.show();
            wallet.show();
            location.show();
            $('#form-admin-filters-dependent').show(400);
        }
        else if (role == "promoter") { 
            projects.show();
            calls.show();
            matchers.show();
            status.show();
            typeofdonor.hide();
            typeofdonor.val = '';
            foundationdonor.hide();
            foundationdonor.val = '';
            cert.hide();
            cert.val = '';
            wallet.hide();
            wallet.val = '';
            location.show();
            $('#form-admin-filters-dependent').show(400);
        }
        else if (role == "matcher") {
            projects.hide();
            projects.val = '';
            calls.hide();
            calls.val = '';
            status.hide();
            status.val = '';
            typeofdonor.hide();
            typeofdonor.val = '';
            foundationdonor.hide();
            foundationdonor.val = '';
            cert.hide();
            cert.val = '';
            wallet.hide();
            wallet.val = '';
            location.hide();
            location.val = '';
            matchers.show();
            $('#form-admin-filters-dependent').show(400);
        }
        else if (role == "test") {
            projects.hide();
            projects.val = '';
            calls.hide();
            calls.val = '';
            status.hide();
            status.val = '';
            typeofdonor.hide();
            typeofdonor.val = '';
            foundationdonor.hide();
            foundationdonor.val = '';
            cert.hide();
            cert.val = '';
            wallet.hide();
            wallet.val = '';
            location.hide();
            location.val = '';
            matchers.hide();
            matchers.val = '';
        }
    }

    function changeDates(dates){

        var today = new Date();

        if (dates == 0) 
        {
            document.getElementById('autoform_startdate').value = moment().subtract(7, 'days').format('DD/MM/YYYY'); 
            document.getElementById('autoform_enddate').value = moment().format('DD/MM/YYYY');
        }
        else if (dates == 1) 
        {
            document.getElementById('autoform_startdate').value = moment().subtract(30, 'days').format('DD/MM/YYYY');
            document.getElementById('autoform_enddate').value = moment().format('DD/MM/YYYY');
        } else if (dates == 2) 
        {
            document.getElementById('autoform_startdate').value = moment().subtract(365, 'days').format('DD/MM/YYYY'); 
            document.getElementById('autoform_enddate').value = moment().format('DD/MM/YYYY');
        } else if (dates == 3) 
        {
            document.getElementById('autoform_startdate').value = '01/01/' + today.getFullYear();
            document.getElementById('autoform_enddate').value = moment().format('DD/MM/YYYY');
        } else if (dates == 4) 
        {
            document.getElementById('autoform_startdate').value = '01/01/' + (today.getFullYear()-1);
            document.getElementById('autoform_enddate').value = '31/12/' + (today.getFullYear()-1);
        } else if (dates == 5) 
        {
            document.getElementById('autoform_startdate').value = '01/01/' + (today.getFullYear()-2);
            document.getElementById('autoform_enddate').value = '31/12/' + (today.getFullYear()-2);
        }
    }

    if (document.getElementById('autoform')){

        document.getElementById('autoform_role').onchange = 
            function(){ 
                changeForm(this.value); 
            };

        document.getElementById('autoform_predefineddata').onchange =
            function(){
                changeDates(this.value);
            };

        document.getElementById('autoform_role').onchange();
    }
});