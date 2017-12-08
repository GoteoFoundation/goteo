<style type="text/css">
    #g_profiler {
        position: fixed;
        bottom: 0;
        right: 0;
        height: 35px;
        width: 100%;
        font-family: monospace;
        font-size:10px;
        color:#000;
        z-index: 1000;
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

    #g_profiler_info .error_backtrace .type {
        border-radius: 3px;
        padding:2px 4px;
        background: #ccc;
    }
    #g_profiler_info .error_backtrace .type.warning {
        background: #FF983F;
    }
    #g_profiler_info .error_backtrace .type.fatal {
        background: #DC2A00;
    }
    #g_profiler_info>div h2{
        font-size: 14px;
        text-transform: uppercase;
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
    #g_profiler_info>div .code, #g_profiler_info>div .code * {
        font-family: monospace;
        font-size:10px;
    }
    #g_profiler a {
        color:#000;
        text-decoration: none;
    }
    #g_profiler a.active {
        opacity: 0.5;
        text-decoration: underline;
    }

    #g_profiler>ul {
        padding: 0;
        margin: 0;
        position: relative;
        /*display: table;*/
    }
    #g_profiler>ul>li {
        /*display: table-cell;*/
        float:left;
        padding: 5px 10px;
        margin: 5px;
        height:22px;
        overflow: hidden;
        list-style: none;
        border-radius:10px;
        background: #aaa;
    }
    #g_profiler>ul>li.right {
        position:absolute;
        padding:0;
        margin:0;
        width: 20px;
        height: 35px;
        top:0;
        right:0;
        border-radius: 0;
        background: #2FB6B5;
        background: #B5DADA;
    }
    #g_profiler>ul>li.right a{
        font-size: 20px;
        font-weight: bold;
        height:30px;
        padding:10px 5px;
    }
    #g_profiler>ul>li.warn {
        background: #FF983F;
    }
    #g_profiler>ul>li.ok {
        background: #66C171;
    }
    #g_profiler>ul>li.ko {
        background: #DC2A00;
    }

</style>
