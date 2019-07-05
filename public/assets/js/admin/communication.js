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
    var wallet = $('#form-wallet');
    var cert = $('#form-cert');
    var location = $('#form-project_location');

    var _show_success_msg = function() {
        $("#alert-success").fadeTo(1000, 500).slideUp(1500, function(){
            $("#alert-success").alert('close');
        });
    }


    function changeForm(role, newFilter){
        if (!newFilter) {
            $('#form-admin-filters-dependent').hide(400);
            $('body,html').animate({scrollTop : $('#form-admin-filters-dependent').height()}, 500);
        }
        if (role == 0) {
            projects.show();
            calls.show();
            status.show();
            typeofdonor.show();
            typeofdonor.show();
            cert.show();
            wallet.show();
            location.show();
            $('#form-admin-filters-dependent').show(400);
        }
        else if (role == 1) { 
            projects.show();
            calls.show();
            status.show();
            typeofdonor.hide();
            typeofdonor.hide();
            cert.hide();
            wallet.hide();
            location.show();
            $('#form-admin-filters-dependent').show(400);
        }
        else if (role == 2) {
            projects.hide();
            calls.hide();
            status.hide();
            typeofdonor.hide();
            cert.hide();
            wallet.hide();
            location.hide();
            $('#form-admin-filters-dependent').show(400);
        }
        else if (role == 3) {
            projects.hide();
            calls.hide();
            status.hide();
            typeofdonor.hide();
            cert.hide();
            location.hide();
            wallet.hide();
            matchers.hide();
            $('#form-admin-filters-dependent').hide();
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
            document.getElementById('autoform_enddate').value = moment().format('DD/MM/YYYY');
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

    document.getElementById('filter-edit').onclick = function(){
        
        if (document.getElementById('filter-select').value > 0){
            newFilter = false;
            $.ajax({
                url: '/api/filter/'+document.getElementById('filter-select').value,
                dataType: 'json',
                type: 'get',
                success: function(resp, status, jqXHR) {
                    filter = resp;
                    document.getElementById('autoform_name').value = resp.name;
                    document.getElementById('autoform_startdate').value = ( moment(resp.startdate).isValid() ) ? moment(resp.startdate).format('DD/MM/YYYY'): null;
                    document.getElementById('autoform_enddate').value = ( moment(resp.enddate).isValid() ) ? moment(resp.enddate).format('DD/MM/YYYY') : null;
                    document.getElementById('autoform_roles').value = resp.role;
                    document.getElementById('autoform_admin-filters-dependent_projects').value = resp.projects;
                    document.getElementById('autoform_admin-filters-dependent_calls').value = resp.calls;
                    document.getElementById('autoform_admin-filters-dependent_matchers').value = resp.matchers;
                    document.getElementById('autoform_admin-filters-dependent_status').value = resp.state;
                    document.getElementById('autoform_admin-filters-dependent_typeofdonor').value = resp.typeofdonor;
                    document.getElementById('autoform_admin-filters-dependent_wallet').value = resp.wallet;
                    document.getElementById('autoform_admin-filters-dependent_cert').value = resp.cert;
                    document.getElementById('autoform_admin-filters-dependent_wallet').checked = parseInt(resp.wallet);
                    document.getElementById('autoform_admin-filters-dependent_cert').checked = parseInt(resp.cert);
                    document.getElementById('autoform_admin-filters-dependent_project_location_location').value = resp.project_location;
                    changeForm(resp.role, true);
                    form.removeClass("hidden");
                    form.show();
                }
            });
        }
    }

    document.getElementById('autoform_roles').onchange = 
        function(){ 
            changeForm(this.value, false); 
        };

    document.getElementById('autoform_predefineddata').onchange =
        function(){
            changeDates(this.value);
        };

    document.getElementById('form_submit').onclick = function(event) {
        $.ajax({
            url: '/api/filter',
            dataType: 'json',
            type: 'POST',
            data: {
                id: filter.id,
                name: document.getElementById('autoform_name').value, 
                startdate: ( moment(document.getElementById('autoform_startdate').value, 'DD/MM/YYYY').isValid() ) ? document.getElementById('autoform_startdate').value: null,
                enddate: ( moment(document.getElementById('autoform_enddate').value, 'DD/MM/YYYY').isValid() ) ? document.getElementById('autoform_enddate').value: null,
                role: document.getElementById('autoform_roles').value,
                projects: document.getElementById('autoform_admin-filters-dependent_projects').value,
                calls: document.getElementById('autoform_admin-filters-dependent_calls').value,
                matchers: document.getElementById('autoform_admin-filters-dependent_matchers').value,
                state: document.getElementById('autoform_admin-filters-dependent_status').value,
                typeofdonor: document.getElementById('autoform_admin-filters-dependent_typeofdonor').value,
                wallet: document.getElementById('autoform_admin-filters-dependent_wallet').checked,
                cert: document.getElementById('autoform_admin-filters-dependent_cert').checked,
                project_location: document.getElementById('autoform_admin-filters-dependent_project_location_location').value
            },
            success: function(resp, status, jqXHR) {
                if (newFilter) {
                    $('#filter-select').append('<option selected="selected" value="'+resp.id+'"> ' + resp.name + ' </option>');
                } else {
                    var filterSelect = document.getElementById('filter-select');
                    filterSelect.options[filterSelect.selectedIndex].innerHTML = resp.name;
                }
                newFilter = false;
                filter = resp;
                form.fadeOut();
                $('body,html').animate({scrollTop : 0}, 500);
                _show_success_msg();
            }
        });
    };

    document.getElementById('filter-create').onclick = function() {
        filter = [];
        document.forms.autoform.reset();
        form.removeClass("hidden");
        form.fadeIn();
        changeForm(0, true);
        newFilter = true;
    }

    document.getElementById('form_close').onclick = function() {
        form.fadeOut();
        $('body,html').animate({scrollTop : 0}, 500);
    }

    document.getElementById('templates').onchange = function() {
        if (this.value == 1) document.getElementById('dropzone-image').classList.remove('hidden');
        else document.getElementById('dropzone-image').classList.add('hidden');
    }

    var mdeditor = [];
    var summernote = [];
    var summernote_config = {
        toolbar: [
            ['tag', ['style']],
            ['style', ['bold', 'italic', 'underline', 'clear']],
            // ['font', ['strikethrough', 'superscript', 'subscript']],
            // ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            // ['height', ['height']],
            ['insert', ['link', 'picture', 'video', 'hr', 'table']],
            ['misc', ['fullscreen', 'codeview', 'help']]
        ],
        popatmouse: false,
        callbacks: {
            onFocus: function() {
            // console.log('Editable area is focused');
            $(this).closest('.summernote').addClass('focused');
            },
            onBlur: function() {
            // console.log('Editable area loses focus');
            $(this).closest('.summernote').removeClass('focused');
            }
        }
    };

    $('#text').change(function(){
        if (this.value === "html") {
            $('textarea.editor').each(function(i) {
                mdeditor[i].toTextArea();
                summernote[i] = $(this).summernote(summernote_config);
            });
        }
        else if (this.value === "md"){
            $('textarea.editor').each(function(i) {
                summernote[i].summernote('destroy');
                mdeditor[i] = new SimpleMDE({
                    element: this,
                    spellChecker: false,
                    promptURLs: true,
                    forceSync: true
                });
              });
        }
    });

    $('textarea.editor').each(function(i) {
        var el = this;
        mdeditor[i] = new SimpleMDE({
            element: this,
            spellChecker: false,
            promptURLs: true,
            forceSync: true
        });
  
        // Tweak codemirror to accept drag&drop any file
        mdeditor[i].codemirror.setOption("allowDropFileTypes", null);
  
        mdeditor[i].codemirror.on('drop', function(codemirror, event) {
            // console.log('codemirror',codemirror,'event',event);
  
            var loading_text = '![](loading image...)';
  
            if(event.dataTransfer && event.dataTransfer.files && event.dataTransfer.files.length) {
              var images = $.grep(event.dataTransfer.files, function(file,i) {
                if(file && file.type && file.type.startsWith('image/')) {
                  return true;
                }
                return false;
              });
              // console.log('images', images);
              if(images.length) {
                // Do not allow predefined codemirror behaviour if are images
                event.preventDefault();
                event.stopPropagation();
                var $cm = $(el).closest('.form-group').find('.CodeMirror.CodeMirror-wrap');
                var $up = $('<div class="uploading">');
                $cm.prepend($up);
  
                var coords = codemirror.coordsChar({
                  left: event.pageX,
                  top: event.pageY
                });
  
                codemirror.replaceRange("\n" + loading_text + "\n", coords);
                coords.line++;
                coords.ch = 0;
                codemirror.setCursor(coords);
                // console.log('codemirror',codemirror,'coords',coords);
  
                
            }
          }
        });
        mdeditor[i].render();
      });

    var dropzone = new Dropzone('div.dropzone', {
        url: '/communication',
        uploadMultiple: false,
        createImageThumbnails: true,
        maxFiles:1,
        maxFilesize: MAX_FILE_SIZE,
        autoProcessQueue: true,
        dictDefaultMessage: '<i style="font-size:2em" class="fa fa-plus"></i><br><br>'
    });
    
    dropzone.on('error', function(file, error) {
        $error.html(error.error);
        $error.removeClass('hidden');
        console.log('error', error);
    });
    dropzone.on('addedfile', function(file, response) {
        console.log(this.hiddenFileInput.files);
    });
    dropzone.on("complete", function(file) {
        dropzone.removeFile(file);
    });
    dropzone.on("sending", function(file, xhr, formData) {
        // Will send the section value along with the file as POST data.
        // formData.append("section", $zone.data('section'));
        // formData.append("add_to_gallery", 'project_image');
    });
      
});
