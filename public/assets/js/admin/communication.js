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

    function changeForm(role){
        if (role == 0) {
            document.getElementById('form-projects').style.display = '';;
            document.getElementById('form-calls').style.display = '';;
            document.getElementById('form-status').style.display = '';;
            document.getElementById('form-typeofdonor').style.display = '';
            document.getElementById('form-typeofdonor').style.display = '';
            document.getElementById('form-cert').style.display = '';
            document.getElementById('form-project_location').style.display = '';
        }
        else if (role == 1) { 
            document.getElementById('form-projects').style.display = '';;
            document.getElementById('form-calls').style.display = '';;
            document.getElementById('form-status').style.display = '';;
            document.getElementById('form-typeofdonor').style.display = 'none';
            document.getElementById('form-typeofdonor').style.display = 'none';
            document.getElementById('form-cert').style.display = 'none';
            document.getElementById('form-project_location').style.display = '';
        }
        else if (role == 2) {
            document.getElementById('form-projects').style.display = 'none';;
            document.getElementById('form-calls').style.display = 'none';;
            document.getElementById('form-status').style.display = 'none';;
            document.getElementById('form-typeofdonor').style.display = 'none';
            document.getElementById('form-cert').style.display = 'none';
            document.getElementById('form-project_location').style.display = 'none';
        }
    }

    function changeDates(dates){

        var today = new Date();

        if (dates == 0) {
        }
        else if (dates == 1) 
        {
            document.getElementById('autoform_startdate').value = moment().subtract(7, 'days').format('DD/MM/YYYY'); 
            document.getElementById('autoform_enddate').value = moment().format('DD/MM/YYYY');
        }
        else if (dates == 2) 
        {
            document.getElementById('autoform_startdate').value = moment().subtract(30, 'days').format('DD/MM/YYYY');
            document.getElementById('autoform_enddate').value = moment().format('DD/MM/YYYY');
        } else if (dates == 3) 
        {
            document.getElementById('autoform_startdate').value = moment().subtract(365, 'days').format('DD/MM/YYYY'); 
            document.getElementById('autoform_enddate').value = moment().format('DD/MM/YYYY');
        } else if (dates == 4) 
        {
            document.getElementById('autoform_startdate').value = '01/01/' + today.getFullYear();
            document.getElementById('autoform_enddate').value = moment.format('DD/MM/YYYY');
        } else if (dates == 5) 
        {
            document.getElementById('autoform_startdate').value = '01/01/' + (today.getFullYear()-1);
            document.getElementById('autoform_enddate').value = '31/12/' + (today.getFullYear()-1);
        } else if (dates == 6) 
        {
            document.getElementById('autoform_startdate').value = '01/01/' + (today.getFullYear()-2);
            document.getElementById('autoform_enddate').value = '31/12/' + (today.getFullYear()-2);
        }
    }

    document.getElementById('autoform_roles').onchange = 
        function(){ 
            changeForm(this.value); 
        };

    document.getElementById('autoform_predefineddata').onchange =
        function(){
            changeDates(this.value);
        };

});
