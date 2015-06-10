<style type="text/css">
    #g_profiler {
        position: fixed;
        bottom: 0;
        height: 35px;
        width: 100%;
        font-family: monospace;
        font-size:10px;
        color:#000;
        border-top: 5px solid #2FB6B5;
        background: #B5DADA;
    }
    body {
        margin-bottom: 40px;
    }
    #g_profiler_info {
        position: fixed;
        bottom: 35px;
        height: 500px;
        width: 100%;
        z-index: 1000;
        display: none;
    }
    #g_profiler_info>div {
        position: absolute;
        top:0;
        left: 0;
        height: 470px;
        width: 90%;
        padding: 10px;
        border: 5px solid #2FB6B5;
        background: #fff;
        overflow: auto;
    }
    #g_profiler_info>div ul {
        padding:0;
        margin: 0 20px;
    }
    #g_profiler_info>div ul>li{
        padding:0;
        margin: 0 0 10px 0;
        list-style: none;
    }
    #g_profiler_info>div .pre, #g_profiler_info>div .pre * {
        font-family: monospace;
        white-space: pre-wrap;
        font-size:10px;
    }
    #g_profiler a {
        color:#000;
    }
    #g_profiler>ul {
        padding: 0;
        margin: 0;
        /*display: table;*/
    }
    #g_profiler>ul>li {
        /*display: table-cell;*/
        float:left;
        padding: 5px 10px;
        margin: 5px;
        height:15px;
        list-style: none;
        border-radius:10px;
        background: #aaa;
    }
    #g_profiler>ul>li.ok {
        background: #66C171;
    }
    #g_profiler>ul>li.ko {
        background: #DC2A00;
    }

</style>

<script>
$(function() {
    $('#g_profiler a').click(function(e){
        e.preventDefault();
        $('#g_profiler_info>div').hide();
        if($('#g_profiler_info').is(':visible')) {
            $('#g_profiler_info').hide();
        }
        else {
            $('#g_profiler_info').show();
            $('#g_profiler_info>.' + $(this).attr('href')).show();
        }
    });
});
</script>
