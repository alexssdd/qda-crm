window.Appeal = {
    // Variables
    customer_id: null,

    // Init
    init: function (){
        Appeal.initInputs();
    },
    initInputs: function (){
        // Variables
        let body = $('body');

        // Phone
        body.on('change', '#appeal-phone', function (){
            Appeal.customer($(this).val());
        });
    },

    // Customer
    customer: function (phone){
        // Variables
        let inputCustomer = $('#appeal-customer_id');
        let inputName = $('#appeal-name');

        // Set default
        Appeal.customer_id = null;
        inputCustomer.val('');

        // Check phone
        if (!phone.length){
            Appeal.actionsDisable();
            return;
        }

        // Prepare data
        let data = {
            phone: phone
        };
        data[yii.getCsrfParam()] = yii.getCsrfToken();

        // Send request
        $.post(UrlManager.to('appeal', 'customer'), data, function (res){
            // Error
            if (res['status'] === 'error'){
                alert(res['message']);
                return;
            }

            // Not found
            if (res['status'] === 'error'){
                Appeal.actionsDisable();
                return;
            }

            // Success
            if (res['status'] === 'success'){
                // Set data
                Appeal.customer_id = res['data']['id']
                inputCustomer.val(res['data']['id']);
                inputName.val(res['data']['name']);
                Appeal.actionsEnable();
            }
        });
    },

    // Actions
    actionsDisable: function (){
        $('.appeal-actions__button').prop('disabled', true);
    },
    actionsEnable: function (){
        $('.appeal-actions__button').prop('disabled', false);
    },
    actionCustomerCares: function (){
        if (Appeal.customer_id === null){
            return;
        }

        let url = UrlManager.to('appeal', 'customer-cares', {customerId: Appeal.customer_id});
        Modal.openAdditional(url);
    },
    actionCustomerOrders: function (){
        if (Appeal.customer_id === null){
            return;
        }

        let url = UrlManager.to('appeal', 'customer-orders', {customerId: Appeal.customer_id});
        Modal.openAdditional(url);
    },
}