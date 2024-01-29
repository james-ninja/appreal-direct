jQuery(".quick-v2.execute-order #account_number").change( function() {
    var acc_num = jQuery(this).val();
    var user_login = jQuery('.quick-v2.execute-order #user_login').val();
    var curr_userid = jQuery('.quick-v2.execute-order #curr_userid').val();
    var user_role = jQuery('.quick-v2.execute-order #user_role').val();

    if(user_login == 1){
        if(curr_userid){
            // sales_representative
            if(user_role == 'sales_representative'){
                var data = {
                'action': 'update_sales_user_data',
                'curr_userid': curr_userid,
                'user_role': user_role,
                'acc_num': acc_num
                };
                jQuery.ajax({
                    url : custom.ajaxurl,
                    data:data,
                    dataType:'json',
                    type:'POST',
                    beforeSend: function(xhr){
                        jQuery(".quick-v2.execute-order #account_number").after("<div class='cnt_overlay'></div>");
                        // jQuery("#account_number").attr("disabled", "disabled"); 
                    },
                    success:function(response){
                        var first_name = response.first_name;
                        var po_number = response.po_number;
                        var billing_purchaser = response.billing_purchaser;
                        var billing_country = response.billing_country;
                        var billing_state = response.billing_state;
                        
                        var billing_address_1 = response.billing_address_1;
                        var billing_city = response.billing_city;
                        var billing_postcode = response.billing_postcode;
                        var billing_email = response.billing_email;
                        var billing_phone = response.billing_phone;
                        var rep_name = response.rep_name;
                        var shipping_first_name = response.shipping_first_name;
                        var shipping_last_name = response.shipping_last_name;
                        var shipping_company = response.shipping_company;
                        var shipping_address_1 = response.shipping_address_1;
                        var shipping_city = response.shipping_city;
                        var shipping_state = response.shipping_state;
                        var shipping_postcode = response.shipping_postcode;
                        

                        jQuery('.quick-v2.execute-order #account_name').val(first_name);
                        jQuery('.quick-v2.execute-order #po_number').val(po_number);
                        jQuery('.quick-v2.execute-order #billing_purchaser').val(billing_purchaser);
                        jQuery('.quick-v2.execute-order #billing_country').val(billing_country);
                        jQuery('.quick-v2.execute-order #billing_state').val(billing_state);
                        
                        jQuery('.quick-v2.execute-order #billing_address_1').val(billing_address_1);
                        jQuery('.quick-v2.execute-order #billing_city').val(billing_city);
                        jQuery('.quick-v2.execute-order #billing_postcode').val(billing_postcode);
                        jQuery('.quick-v2.execute-order #billing_email').val(billing_email);
                        // jQuery('.quick-v2.execute-order #billing_email').val(rep_name);
                        jQuery('.quick-v2.execute-order #billing_phone').val(billing_phone);
                        jQuery('.quick-v2.execute-order #billing_company').val(rep_name);

                        // jQuery('.quick-v2.execute-order #shipping_first_name').val(shipping_first_name);
                        // jQuery('.quick-v2.execute-order #shipping_last_name').val(shipping_last_name);
                        // jQuery('.quick-v2.execute-order #shipping_company').val(shipping_company);
                        // jQuery('.quick-v2.execute-order #shipping_address_1').val(shipping_address_1);
                        // jQuery('.quick-v2.execute-order #shipping_city').val(shipping_city);
                        // jQuery('.quick-v2.execute-order #shipping_state').val(shipping_state);
                        // jQuery('.quick-v2.execute-order #shipping_postcode').val(shipping_postcode);

                        jQuery('.quick-v2.execute-order .cnt_overlay').remove();
                        // jQuery("#account_number").removeAttr("disabled"); 
               
                    }
                });
            }
        }
    }
});

function fileValidation() { 
    var fileInput =  document.getElementById('add_billing_attach'); 
    var filePath = fileInput.value; 
    var allowedExtensions = /(\.pdf|\.xls|\.xlsx|\.doc|\.docx|\.odt|\.jpg|\.jpeg|\.png)$/i; 
      
    if (!allowedExtensions.exec(filePath)) { 
        alert('Invalid file type'); 
        fileInput.value = ''; 
        return false; 
    }
}