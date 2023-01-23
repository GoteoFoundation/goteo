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

$(() => {
    // $(window).on("pronto.request", function(e){
    // });

    $(window).on(`pronto.render`, function (e) {
        $("div.project-menu div.item, div.sticky-item").removeClass("current");

        $('table.footable').footable();
        var url = e.currentTarget.location.href;
        var section = 'home';
        if (url.indexOf('/updates') !== -1) section = 'updates';
        if (url.indexOf('/participate') !== -1) section = 'participate';
        // console.log('section', section);

        $("." + section).addClass("current");

        $("a.accordion-toggle").click(function () {
            if ($(this).hasClass('collapsed'))
                $(this).find('span.glyphicon').removeClass("glyphicon-menu-down").addClass("glyphicon-menu-up");
            else
                $(this).find('span.glyphicon').removeClass("glyphicon-menu-up").addClass("glyphicon-menu-down");
        });

        $("#infoCarousel").swiperight(function () {
            $(this).carousel('prev');
        });

        $("#infoCarousel").swipeleft(function () {
            $(this).carousel('next');
        });

        $('#go-top').click(function () {
            $('body,html').animate({scrollTop: 0}, 500);
            return false;
        });

        $('div.button-msg').click(function () {
            $(".box").hide();
            $("div.button-msg .main-button button").removeClass("message-grey").addClass("green");
            $(this).find('.main-button button').removeClass("green").addClass("message-grey");
            $(this).find('.box').show();
        });

        $("#reset-chart").click(function () {
            $("div.chart-costs").fadeOut("slow", function () {
                printCosts();
            });
            $("div.chart-costs").fadeIn("slow");
        });

    });

    function _favourite_ajax(user, project) {
        if (user) {
            fetch("/project/favourite/" + project,
                {
                    method: "POST"
                }).then(r => $(".favourite").addClass('active'))
        }
    };

    function _delete_favourite_ajax (user, project) {
        if (user) {
            fetch("/project/delete-favourite", {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({'project': project, 'user': user})
            }).then(() => $(".favourite").removeClass('active'));
        }
    };

    $(".favourite").click(function () {
        const user = $(this).data('user');
        const project = $(this).data('project');

        if ($(this).hasClass('active'))
            _delete_favourite_ajax(user, project);
        else
            _favourite_ajax(user, project);
    });

    $("a.accordion-toggle").click(function () {
        if ($(this).hasClass('collapsed'))
            $(this).find('span.glyphicon').removeClass("glyphicon-menu-down").addClass("glyphicon-menu-up");
        else
            $(this).find('span.glyphicon').removeClass("glyphicon-menu-up").addClass("glyphicon-menu-down");
    });

    $("div.widget.rewards a.accordion-toggle").click(function () {
        if ($(this).hasClass('collapsed'))
            $(this).parent().removeClass('rewards-collapsed');
        else
            $(this).parent().addClass('rewards-collapsed');
    });

    $("#infoCarousel").swiperight(function () {
        $(this).carousel('prev');
    });

    $("#infoCarousel").swipeleft(function () {
        $(this).carousel('next');
    });

    $('#go-top').click(function () {
        $('body,html').animate({scrollTop: 0}, 500);
        return false;
    });

    $('div.button-msg').click(function () {
        $(".box").hide();
        $("div.button-msg .main-button button").removeClass("message-grey").addClass("green");
        $(this).find('.main-button button').removeClass("green").addClass("message-grey");
        $(this).find('.box').show();
    });

    $("#show-link").click(function () {
        $("#link-box").toggle(600);
    });

    $("#reset-chart").click(function () {
        $("div.chart-costs").fadeOut("slow", function () {
            printCosts();
        });
        $("div.chart-costs").fadeIn("slow");
    });

    $("div.row.call-info").hover(function () {
        $(".info-default-call").toggle();
        $(".info-hover-call").toggle();
    });

    // Delete support msg
    $('.msg').on('click', ".delete-msg", function (e) {
        e.preventDefault();
        const ask = $(this).data('confirm');
        const url = $(this).data('url');
        const $item = $(this).closest('.msg');
        const $error = $item.find('.error-message');
        if (confirm(ask)) {
            $.ajax({
                url: url,
                type: 'DELETE',
                success: function (data) {
                    //console.log('success', data);
                    $item.remove();
                }
            }).fail(function (data) {
                const error = JSON.parse(data.responseText);
                //console.log('error', data, error)
                $error.removeClass('hidden').html(error.error);
            });
        }
    });


    // Send comments
    $(document).on('click', '.ajax-comments .send-comment', function (e) {
        e.preventDefault();
        const $parent = $(this).closest('.ajax-comments');
        const $list = $($parent.data('list'));
        const url = $parent.data('url');
        const $error = $parent.find('.error-message');
        const $textarea = $parent.find('[name="message"]');
        const data = {
            message: $textarea.val(),
            thread: $parent.data('thread'),
            project: $parent.data('project'),
            view: 'project'
        };

        $error.addClass('hidden').html('');
        $.post(url, data, function (data) {
            // console.log('ok!', data);
            $list.append(data.html);
            $textarea.val('');
            $parent.closest('.box').hide();
        }).fail(function (data) {
            const error = JSON.parse(data.responseText);
            // console.log('error', data, error)
            $error.removeClass('hidden').html(error.error);
        });
    });

    $('.slider-matchers').slick({
        dots: true,
        infinite: true,
        speed: 1000,
        fade: true,
        arrows: false,
        cssEase: 'linear'
    });

    $('.slider-footprint').slick({
        fade:true,
    })

    $('.slider-sdgs').slick({
        slidesToShow: 3,
        slidesToScroll: 1
    })

    $('[data-toggle="tooltip"]').tooltip();

    function amount_estimation_divide_data(estimatedAmount, data) {
        return parseInt(data) / parseInt(estimatedAmount)
    }

    function data_divide_amount_estimation(estimatedAmount, data) {
        return parseInt(estimatedAmount) / parseInt(data)
    }

    function calculateResult(estimatedAmount, data, operationType, value) {
        switch (operationType) {
            case "data_divide_amount_estimation":
                return data_divide_amount_estimation(estimatedAmount, data) * value;
            case "amount_estimation_divide_data":
                return amount_estimation_divide_data(estimatedAmount, data) * value;
        }
    }

    const $impactCalculatorModal = document.getElementById('impact-calculator-modal')
    const $modalBudget = document.getElementById('modal-budget')
    $modalBudget.addEventListener('change', function() {
        const value = this.value
        const count = $impactCalculatorModal.dataset.impactDataProjectCount
        const valuePerImpactData = (value / count).toFixed(2)
        const $impactDataList = $('.impact-data-info').each(function(index, impactData) {
            const estimationAmount = impactData.dataset.estimationAmount
            const data = impactData.dataset.data
            const operationType = impactData.dataset.operationType
            const id = impactData.dataset.id

            const $p = document.getElementById('result-impact-data-' + id)
            const resultMessage = $p.dataset.resultMsg
            const result = calculateResult(estimationAmount, data, operationType, valuePerImpactData).toFixed(2)

            $p.innerHTML = resultMessage.replace("%s", value).replace("%s", result)

        })
    })
});

// @license-end
