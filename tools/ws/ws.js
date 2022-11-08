$(() => {
    // global lets
    let cachedMethods = new Array;
    let ws_url = "http://";

    // automatic detection of ws_url
    match = document.location.toString().match(/^(https?.*\/)tools\/ws\.html?/);
    if (match == null) {
        askForUrl();
    }
    else {
        ws_url = match[1] + 'ws.php';
        getMethodList();
    }

    // manual set of ws_url
    $("#urlForm").submit(function () {
        ws_url = $(this).children("input[name='ws_url']").val();
        getMethodList();
        return false;
    });

    // invoke buttons
    $("#invokeMethod").click(function () {
        invokeMethod($("#methodName").html(), false);
        return false;
    });
    $("#invokeMethodBlank").click(function () {
        invokeMethod($("#methodName").html(), true);
        return false;
    });

    // resizable iframe
    $("#increaseIframe").click(function () {
        $("#resultWrapper").css('height', $("#resultWrapper").height() + 100);
    });
    $("#decreaseIframe").click(function () {
        if ($("#resultWrapper").height() > 200) {
            $("#resultWrapper").css('height', $("#resultWrapper").height() - 100);
        }
    });

    // mask all wrappers
    function resetDisplay() {
        $("#errorWrapper").hide();
        $("#methodWrapper").hide();
        $("#methodName").hide();
        $("#urlForm").hide();
        $("#methodDescription blockquote").empty();
        $("#methodDescription").hide();
        $("#requestURLDisplay").hide();
        $("#requestResultDisplay").hide();
        $("#invokeFrame").attr('src', '');
    }

    // display error wrapper
    function displayError(error) {
        resetDisplay();
        $("#errorWrapper").html("<b>Error:</b> " + error).show();
    }

    // display ws_url form
    function askForUrl() {
        displayError("can't contact web-services, please give absolute url to 'ws.php'");
        if ($("#urlForm input[name='ws_url']").val() == "") {
            $("#urlForm input[name='ws_url']").val(ws_url);
        }
        $("#urlForm").show();
    }

    // parse Piwigo JSON
    function parsePwgJSON(json) {
        try {
            resp = jQuery.parseJSON(json);
            if (resp == null | resp.result == null | resp.stat == null | resp.stat != 'ok') {
                throw new Error();
            }
        }
        catch (e) {
            displayError("unable to parse JSON string");
            resp = { "stat": "ko", "result": "null" };
        }

        return resp.result;
    }

    // fetch methods list
    function getMethodList() {
        resetDisplay();

        $.ajax({
            type: "GET",
            url: ws_url,
            data: { format: "json", method: "reflection.getMethodList" }
        }).done(function (result) {
            console.log(result);
            result = parsePwgJSON(result);

            if (result != null) {
                methods = result.methods;

                let methodTree = {};
                for (let i = 0; i < methods.length; i++) {
                    addMethodToNode(methodTree, methods[i].split('.'))
                }

                $("#methodsList").html(displayMethodNode(methodTree, [])).show();

                // trigger method selection
                $("#methodsList .method-link").click(function () {
                    selectMethod($(this).data('method'));
                });

                if ($.cookie('wse-menu-state')) {
                    $.cookie('wse-menu-state').split(',').forEach(id => $(`input[id="${id}"]`).attr('checked', true));
                }

                setStateMenu();

                $('.method-node-content').each((i, node) => {
                    let content = $(node);
                    let checkbox = content.parent().children('input');

                    checkbox.on('change', function() {

                        let id = checkbox.attr('id');

                        let menustate = $.cookie('wse-menu-state')?.split(',') ?? [];

                        if (this.checked) {
                            content.slideDown(200);

                            menustate.push(id)
                        } else {
                            content.slideUp(200);
                            menustate = menustate.filter(str => str !== id);
                        }

                        $.cookie('wse-menu-state', menustate.join(','))

                    })
                })
            }
        }).error(function (jqXHR, textStatus, errorThrown) {
            askForUrl();
        });
    }

    function addMethodToNode(methodNode, methodRoute) {
        if (methodRoute.length > 1) {
            let node = methodRoute.shift();
            if (!methodNode[node])
                methodNode[node] = {};
            addMethodToNode(methodNode[node], methodRoute);
        } else 
            methodNode[methodRoute[0]] = 1;
    }

    function displayMethodNode(methodNode, route) {
        let html = '';

        if (methodNode === 1) {
            html = `<a 
                class="method-link" 
                data-method="${route.join('.')}"
                title="${route[route.length - 1]}"
            >
                ${route[route.length - 1]}
            </a>`
        } else {
            html = route.length === 0 ? '' : `<div class="method-node">
                <input type="checkbox" id="method-node-input-${route.join('.')}">
                <label for="method-node-input-${route.join('.')}" title="${route[route.length - 1]}">
                    <i class="icon-down-open"></i>
                    <span>${route[route.length - 1]}</span>
                </label>
                <div class="method-node-content">`;

            for (const node in methodNode) {
                html += displayMethodNode(methodNode[node], [...route, node]);
            }
            
            html += route.length === 0 ? '' :'</div></div>';
        }

        return html;
    }

    function setStateMenu() {
        $('.method-node').each((i, n) => {
            let node = $(n);
            let content = node.children('.method-node-content')
            let checkbox = node.children('input');
            
            if (checkbox.prop('checked')) {
                content.show();
            } else {
                content.hide();
            }
        })
    }

    // select method
    function selectMethod(methodName) {
        $("#introMessage").hide();
        $("#tiptip_holder").fadeOut(200);

        if (cachedMethods[methodName]) {
            fillNewMethod(methodName);
        }
        else {
            $.ajax({
                type: "GET",
                url: ws_url,
                data: { format: "json", method: "reflection.getMethodDetails", methodName: methodName }
            }).done(function (result) {
                result = parsePwgJSON(result);

                if (result != null) {
                    let onlys = [];
                    if (result.options.post_only || result.options.admin_only) {
                        if (result.options.post_only) {
                            onlys.push('POST only');
                        }
                        if (result.options.admin_only) {
                            onlys.push('Admin only');
                        }

                    }
                    result.onlys = onlys;
                    cachedMethods[methodName] = result;
                    fillNewMethod(methodName);
                }
            }).error(function (jqXHR, textStatus, errorThrown) {
                displayError("unknown error");
            });
        }
    }

    // display method details
    function fillNewMethod(methodName) {
        resetDisplay();

        method = cachedMethods[methodName];

        $("#methodName").html(method.name).show();

        $('#onlys').html('');
        method.onlys.forEach((text) => {
            $('#onlys').append($(`<span class="only">${text}</span>`));
        })

        if (method.description != "") {
            $("#methodDescription blockquote").html(method.description);
            $("#methodDescription").show();
        }

        $("#requestFormat").val(method.options.post_only ? 'post' : 'get');

        let methodParams = '';
        if (method.params && method.params.length > 0) {

            $('.no-params').hide();
            $("#methodParams table").show();
            
            for (let i = 0; i < method.params.length; i++) {
                let param = method.params[i],
                    isOptional = param.optional,
                    acceptArray = param.acceptArray,
                    defaultValue = param.defaultValue == null ? '' : param.defaultValue,
                    info = param.info == null ? '' : '<i class="methodInfo icon-info-circled-1" title="' + param.info.replace(/"/g, '&quot;') + '"></i>',
                    type = '',
                    subtype = '',
                    optional = '<span class="required" title = "This parameter is required" >*</span >',
                    array = '<span class="type-badge icon-clone" title="Can be an array"></span >';

                if (param.type.match(/bool/)) type += '<span class="type-badge" title="Boolean">B<span>';
                if (param.type.match(/int/)) type += '<span class="type-badge" title="Integer">I</span>';
                if (param.type.match(/float/)) type += '<span class="type-badge" title="Float">F</span>';

                if (param.type.match(/positive/)) subtype += '<span class="type-badge icon-plus" title="Positive"></span>';
                if (param.type.match(/notnull/)) subtype += '<span class="type-badge" title="Not null"><span style="transform:translateY(-3px)">&oslash;</span></span>';


                // if an array is direclty printed, the delimiter is a comma where we use a pipe
                if (typeof defaultValue == 'object') {
                    defaultValue = defaultValue.join('|');
                }

                methodParams += `<tr>
                    <td>${param.name + (isOptional ? '' : optional ) + info }</td>
                    <td class="mini">${(acceptArray ? array : '') + type + subtype}</td>
                    <td class="input"><input type="text" class="methodParameterValue" data-id="${i}" value="${defaultValue}"></td>
                    <td class="mini">
                        <input type="checkbox" id="parameter-send-${i}" class="methodParameterSend" data-id="${i}" ${(isOptional ? '' : 'checked="checked"')}>
                        <label class="methodParameterSendCheckbox" for="parameter-send-${i}"><i class="icon-ok"></i></label>
                    </td>
                    </tr>`;
            }
            $("#methodParams tbody").html(methodParams);
        }
        else {
            $('.no-params').show();
            $("#methodParams table").hide();
        }

        $("#methodWrapper").show();

        // trigger field modification
        $("input.methodParameterValue").change(function () {
            $("input.methodParameterSend[data-id='" + $(this).data('id') + "']").attr('checked', 'checked');
        });

        // tiptip
        $(".methodInfo").tipTip({
            maxWidth: "300px",
            defaultPosition: "bottom",
            delay: 0
        });

        $(".required, .type-badge").tipTip({
            maxWidth: "100px",
            defaultPosition: "bottom",
            delay: 0
        });
    }

    // invoke method
    function invokeMethod(methodName, newWindow) {

        $('#requestURLDisplay').show();
        $('#requestResultDisplay').show();

        let method = cachedMethods[methodName];

        let reqUrl = ws_url + "?format=" + $("#responseFormat").val();

        // GET
        if ($("#requestFormat").val() == 'get') {
            reqUrl += "&method=" + methodName;

            for (let i = 0; i < method.params.length; i++) {
                if (!$("input.methodParameterSend[data-id='" + i + "']").is(":checked")) {
                    continue;
                }

                let paramValue = $("input.methodParameterValue[data-id='" + i + "']").val();

                let paramSplitted = paramValue.split('|');
                if (method.params[i].acceptArray && paramSplitted.length > 1) {
                    $.each(paramSplitted, function (v) {
                        reqUrl += '&' + method.params[i].name + '[]=' + paramSplitted[v];
                    });
                }
                else {
                    reqUrl += '&' + method.params[i].name + '=' + paramValue;
                }
            }

            if (newWindow) {
                window.open(reqUrl);
            }
            else {
                if ($("#responseFormat").val() === 'json') {
                    $("#invokeFrame").hide();
                    $('#json-viewer').show();
                    fetch(reqUrl)
                    .then(data => data.json())
                    .then(json => {
                        $('#json-viewer').jsonViewer(json);
                    })
                } else {
                    $("#invokeFrame").show();
                    $('#json-viewer').hide();
                    $("#invokeFrame").attr('src', reqUrl);
                }
            }

            $('#requestURLDisplay').find('.url').html(reqUrl).end()
                .find('.params').hide();
        }
        // POST
        else {
            let params = {};

            let form = $("#invokeForm");
            form.attr('action', reqUrl);

            let t = '<input type="hidden" name="method" value="' + methodName + '">';

            for (let i = 0; i < method.params.length; i++) {
                if (!$("input.methodParameterSend[data-id='" + i + "']").is(":checked")) {
                    continue;
                }

                let paramValue = $("input.methodParameterValue[data-id='" + i + "']").val(),
                    paramName = method.params[i].name,
                    paramSplitted = paramValue.split('|');

                if (method.params[i].acceptArray && paramSplitted.length > 1) {
                    params[paramName] = [];

                    $.each(paramSplitted, function (i, value) {
                        params[paramName].push(value);
                        t += '<input type="hidden" name="' + paramName + '[]" value="' + value + '">';
                    });
                }
                else {
                    params[paramName] = paramValue;
                    t += '<input type="hidden" name="' + paramName + '" value="' + paramValue + '">';
                }
            }


            if (!newWindow && $("#responseFormat").val() === 'json') {
                $("#invokeFrame").hide();
                $('#json-viewer').show();
                jQuery.ajax({
                    url: reqUrl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        "method": methodName,
                        ...params
                    },
                    success : function(data) {
                        $('#json-viewer').jsonViewer(data);
                    }
                })
            } else {
                $("#invokeFrame").show();
                $('#json-viewer').hide();

                form.html(t);
                form.attr('target', newWindow ? "_blank" : "invokeFrame");
                form.submit();
            }

            $('#requestURLDisplay').find('.url').html(reqUrl).end()
                .find('.params').show().html(JSON.stringify(params, null, 4));
        }

        return false;
    }

    $('#search input').val('');

    $('#search input').on('input', function () {
        if ($(this).val()) {
            $('.method-node, .method-link').hide();
            if (!$('#methodsList').hasClass('onSearch')) {
                $('#methodsList').addClass('onSearch');
                $('.method-node-content').show();
            }

            function showBranch(methodNode) {
                methodNode.show();
                while (methodNode.parent().parent().hasClass("method-node")) {
                    methodNode = methodNode.parent().parent();
                    methodNode.show();
                }
            }

            $('.method-link').each((i, n) => {
                if ($(n).data('method').toLowerCase().search($(this).val().toLowerCase())!= -1) {
                    showBranch($(n));
                }
            })

        } else {
            $('.method-node, .method-link').show();
            $('#methodsList').removeClass('onSearch');
            setStateMenu()
        }
    })

    if ($.cookie('wse-dark-mode')) {
        $('#the_body').addClass('dark-mode');
        $('.darkModeButton').addClass('icon-sun-inv');
    }

    $('.darkModeButton').click(() => {
        if ($.cookie('wse-dark-mode')) {
            $('.darkModeButton').removeClass('icon-sun-inv').addClass('icon-moon-inv');
            $.removeCookie('wse-dark-mode');
            $('#the_body').removeClass('dark-mode');
        } else {
            $('.darkModeButton').removeClass('icon-moon-inv').addClass('icon-sun-inv');
            $.cookie('wse-dark-mode', true);
            $('#the_body').addClass('dark-mode');
        }
    })
})