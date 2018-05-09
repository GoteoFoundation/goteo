goteo.typeahead_engines = goteo.typeahead_engines || {};

goteo.typeahead_engines.call = function (settings) {
    var prefetch_statuses = settings && settings.prefetch_statuses || '3,4,5',
        remote_statuses = settings && settings.remote_statuses || '3,4,5,6',
        defaults = settings && settings.defaults || false;

    var engine = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('id', 'name', 'subtitle'),
        identify: function (o) { return o.id; },
        dupDetector: function (a, b) { return a.id === b.id; },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        prefetch: {
            url: '/api/calls?status=' + prefetch_statuses,
            filter: function (response) {
                // console.log('prefetch hit', response);
                return response.list;
            }
        },
        remote: {
            url: '/api/calls?status=' + remote_statuses + '&q=%QUERY',
            wildcard: '%QUERY',
            filter: function (response) {
                // console.log('remote hit', response);
                return response.list;
            }
        }
    });

    // initialize the bloodhound suggestion engine
    engine.initialize();

    var engineWithDefaults = function (q, sync, async) {
        if (q === '' && defaults) {
            // console.log('get defaults');
            sync(engine.index.all());
            async([]);
        }
        else {
            engine.search(q, sync, async);
        }
    };

    // Engine
    return {
        name: 'call',
        displayKey: 'name',
        source: engineWithDefaults,
        templates: {
            header: '<h3>' + (goteo.texts && goteo.texts['admin-calls'] || 'Calls') + '</h3>',
            suggestion: function (datum) {
                // console.log('call suggestion', datum);
                var label = 'default';
                if (datum.status === 2) label = 'warning';
                if (datum.status === 3) label = 'info';
                if (datum.status === 4) label = 'orange';
                if (datum.status === 5) label = 'success';
                if (datum.status === 6) label = 'danger';
                var t = '<div>';
                if(datum.image) t += '<img src="' + datum.image + '" class="img-circle"> ';
                t += '<span class="label label-' + label + '">' + datum.status_desc + '</span> ';
                t += datum.name + '</div>';
                return t;
            }
        }
    }
};
