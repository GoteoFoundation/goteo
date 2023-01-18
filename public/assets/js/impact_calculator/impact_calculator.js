$(function() {
    $('[data-toggle="tooltip"]').tooltip();

    $('input[type=checkbox]').change(function() {
        const $article = $(this.closest('article'))
        this.checked ? $article.addClass('active') : $article.removeClass('active')
    });

    function amount_estimation_divide_data(estimatedAmount, data) {
        return parseInt(data) / parseInt(estimatedAmount)
    }

    function data_divide_amount_estimation(estimatedAmount, data) {
        return parseInt(estimatedAmount) / parseInt(data)
    }

    function calculateResult(estimatedAmount, data, operationType) {
        switch (operationType) {
            case "data_divide_amount_estimation":
                return data_divide_amount_estimation(estimatedAmount, data);
            case "amount_estimation_divide_data":
                return amount_estimation_divide_data(estimatedAmount, data);
        }
    }

    $("input[name^='form']").change(function() {
        const impactData = this.closest('article').dataset.impactData;
        const footprint = this.closest('article').dataset.footprint;
        const operation = this.closest('article').dataset.operation;

        const estimatedAmount = $("#form_" + footprint + "_" + impactData + "_estimated_amount").val();
        const data = $("#form_" + footprint + "_" + impactData + "_data").val();

        if (estimatedAmount && data)
        {
            const $resultMsg = document.getElementById("card_" + footprint + "_" + impactData + "_result_msg");
            const result = calculateResult(estimatedAmount, data, operation);
            const text = $resultMsg.dataset.text;

            $resultMsg.innerHTML = text.replace("%s", result);
        }
    });
});



