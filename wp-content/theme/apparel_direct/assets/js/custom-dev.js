
if (jQuery('#product_brand_select').length > 0) {
    jQuery.fn.select2.amd.define('select2/selectAllAdapter', [
        'select2/utils',
        'select2/dropdown',
        'select2/dropdown/attachBody'
    ], function (Utils, Dropdown, AttachBody) {

        function SelectAll() { }
        SelectAll.prototype.render = function (decorated) {
            var self = this,
                $rendered = decorated.call(this),
                $selectAll = jQuery(
                    '<button class="btn btn-xs btn-default" type="button">Select All</button>'
                ),
                $unselectAll = jQuery(
                    '<button class="btn btn-xs btn-default" type="button" style="margin-left:6px;">Unselect All</button>'
                ),
                $btnContainer = jQuery('<div style="margin-top:3px;">').append($selectAll).append($unselectAll);
            if (!this.$element.prop("multiple")) {
                // this isn't a multi-select -> don't add the buttons!
                return $rendered;
            }
            $rendered.find('.select2-dropdown').prepend($btnContainer);
            $selectAll.on('click', function (e) {
                var $results = $rendered.find('.select2-results__option[aria-selected=false]');
                $results.each(function () {
                    self.trigger('select', {
                        data: jQuery(this).data('data')
                    });
                });
                self.trigger('close');
            });
            $unselectAll.on('click', function (e) {
                var $results = $rendered.find('.select2-results__option[aria-selected=true]');
                $results.each(function () {
                    self.trigger('unselect', {
                        data: jQuery(this).data('data')
                    });
                });
                self.trigger('close');
            });
            return $rendered;
        };

        return Utils.Decorate(
            Utils.Decorate(
                Dropdown,
                AttachBody
            ),
            SelectAll
        );

    });

}

function order_help_form() {
    jQuery(".order-help-btn").click(function () {
        jQuery("input[name='user-name']").val(jQuery(this).data("user-name"));
        jQuery("input[name='user-email']").val(jQuery(this).data("emailid"));
        jQuery("input[name='user-order-id']").val(jQuery(this).data("orderid"));
        jQuery('#modal_order_form').modal('show');
    });
}
function arrow_check() {
    if (jQuery('.wc-bulk-variations-table').width() <= jQuery('.variation_table_wrapper').width()) {
        jQuery('.arrow_main').hide();
    } else {
        jQuery('.arrow_main').show();
    }
}
jQuery(window).on('resize', function () {
    arrow_check();
});
jQuery(document).ready(function () {
    arrow_check();
    //registration field
    if (jQuery('#product_cat_select').length > 0) {
        jQuery('#product_cat_select').select2({
            placeholder: "Select Categories of Interest",
            dropdownAdapter: jQuery.fn.select2.amd.require('select2/selectAllAdapter'),
            allowClear: true
        });
    }

    if (jQuery('#product_brand_select').length > 0) {
        jQuery('#product_brand_select').select2({
            placeholder: "Select Brands of Interest",
            dropdownAdapter: jQuery.fn.select2.amd.require('select2/selectAllAdapter'),
            allowClear: true
        });
    }

    if (jQuery('.business_type').length > 0) {
        jQuery('.business_type').select2({
            placeholder: "Select Business Type",
            allowClear: true
        });
    }

    if (jQuery('.wishlist-empty').length > 0) {
        jQuery('.wishlist-page-links').hide();
    }

    jQuery('#business_type_ecommerce_field').hide();

    if (jQuery("#business_type option:selected").attr("value") == 'e-commerce') {
        jQuery('#business_type_ecommerce_field').show();
    }
    jQuery('#business_type').on('select2:select select2:unselect', function (e) {

        var selectedvalue = jQuery("#business_type").val();

        if (jQuery.inArray("e-commerce", selectedvalue) != -1) {
            jQuery('#business_type_ecommerce_field').show();
        } else {
            jQuery('#business_type_ecommerce_field').hide();
        }

    });

    jQuery(".another_payment_tab").hide();
    jQuery(".another_payment").click(function () {
        if (jQuery(this).is(":checked")) {
            jQuery(".another_payment_tab").show();
        } else {
            jQuery(".another_payment_tab").hide();
        }
    });

    jQuery(".shipping_address_tab").hide();
    jQuery(".shipping_address_chk").click(function () {
        if (jQuery(this).is(":checked")) {
            jQuery(".shipping_address_tab").show();
        } else {
            jQuery(".shipping_address_tab").hide();
        }
    });

    jQuery(".product_login_popup").click(function () {
        jQuery('#modal_login_form').modal('show');
    });

    //cart page
    jQuery('.view-more-cart').click(function (event) {
        jQuery('.woocommerce-cart-form tr:not(.cart_bottom_footer)').removeClass('d-none');
        jQuery(this).hide();
    });

    jQuery(document.body).on('updated_cart_totals', function () {
        jQuery('.view-more-cart').click(function () {
            jQuery('.woocommerce-cart-form tr:not(.cart_bottom_footer)').removeClass('d-none');
            jQuery(this).hide();
        });
        jQuery('.ad_checkout_btn_custom').show();
        jQuery('.ad_updatecart_btn_custom').hide();
    })
    
    jQuery('.ad_updatecart_btn_custom').hide();
    jQuery('.woocommerce').on('change keyup', 'input.qty', function() {
        jQuery('.ad_checkout_btn_custom').hide();
        jQuery('.ad_updatecart_btn_custom').show();
    });

    jQuery('.ad_updatecart_btn_custom').click(function (event) {
        jQuery("[name='update_cart']").trigger("click");
    });

    //Product details page
    jQuery('.more_color_items').hide();
    jQuery('.s_colors_ul .less_color_items').not('.s_colors_ul .color_out_of_stock').show();

    //old js
    /*jQuery('.view-more-color').click(function (event) {
       // var txt = jQuery(".more_color_items").is(':visible') ? 'View More' : 'View Less';
        var txt = jQuery(".more_color_items").not('.color_out_of_stock').is(':visible') ? 'View More' : 'View Less';
        jQuery(".view-more-color").text(txt);
        if (jQuery(".product_list_section").hasClass("show_outofstock")) {
            jQuery('.color_box').find('.more_color_items').slideToggle();
        }else{
            jQuery('.color_box').find('.more_color_items').not('.color_out_of_stock').slideToggle();
        }
        //jQuery('.color_box').find('.more_color_items').slideToggle();
    });*/

    //After changes
    jQuery('.view-more-color').click(function (event) {

        if(jQuery(".product_list_section").hasClass("show_viewmore")){
            jQuery(".product_list_section").removeClass('show_viewmore');
            jQuery(".product_list_section").addClass('show_lessmore');
            var txt = 'View Less';
            if (jQuery(".product_list_section").hasClass("show_outofstock")) {
                jQuery('.color_box').find('.more_color_items').slideDown();
                //jQuery('.color_box').find('.more_color_items.less_color_items').slideUp();
            }else{
                //jQuery('.color_box').find('.more_color_items').not('.color_out_of_stock, .less_color_items ').slideDown();
                jQuery('.color_box').find('.more_color_items').not('.color_out_of_stock').slideDown();
                //jQuery('.more_color_items.less_color_items').slideDown();
            }
           
        }else{
            jQuery(".product_list_section").addClass('show_viewmore');
            jQuery(".product_list_section").removeClass('show_lessmore');
            var txt = 'View More';
            if (jQuery(".product_list_section").hasClass("show_outofstock")) {
                /*console.log('else checkbox checked show all');*/
                //jQuery('.color_box').find('.more_color_items.less_color_items').slideDown();
                jQuery('.color_box').find('.more_color_items').slideUp();
            }else{
               /* console.log('else checkbox not checked not show all');*/
                jQuery('.color_box').find('.more_color_items').not('.color_out_of_stock, .less_color_items ').slideUp();
                //jQuery('.color_box').find('.more_color_items').not('.less_color_items ').slideUp();
                //jQuery('.more_color_items.less_color_items').slideUp();
            }
            
        }
         jQuery(".view-more-color").text(txt);

     });

    //Order Page

    order_help_form();

    if (jQuery('.woocommerce-product-details__short-description').length > 0) {
        new Cuttr('.woocommerce-product-details__short-description p', {
            //options here
            truncate: 'words',
            length: 25,
            readMore: true,
            readMoreText: 'Read More',
            readLessText: 'Read Less',
            readMoreBtnTag: 'a'
        });
    }

    //Save cart page
    jQuery('.view-more-save-cart').click(function (event) {
        jQuery('.mwb-woo-smc-shop_table tr').removeClass('d-none');
        jQuery(this).hide();
    });

    var contentSaveForLater = jQuery('.saveforlater_total_data').html();
    jQuery('.save_for_later_total').html(contentSaveForLater);

    if (jQuery('.register').length > 0) {
        //Form Validation
        jQuery(".register").validate({
            // Specify validation rules
            rules: {

                business_name: "required",
                ein: "required",
                email: {
                    required: true,
                    email: true
                },
                business_type: "required",
                business_type_ecommerce: "required",
                business_phone: {
                    phoneUS: true,
                    required: true
                },
                upload_document: "required",
                //'reg_product_brand_select[]': "required",
                //'reg_product_cat_select[]': "required",
                billing_first_name: "required",
                billing_last_name: "required",
                personal_email: {
                    required: true,
                    email: true
                },
                personal_phone: {
                    phoneUS: true,
                    required: true
                },
                billing_address_1: "required",
                home_address_1: "required",
                billing_state: "required",
                home_state: "required",
                billing_city: "required",
                home_city: "required",
                billing_postcode: {
                    required: true,
                    number: true
                },
                home_postcode: {
                    required: true,
                    number: true
                },
                privacy_policy_reg: "required"

            },
            errorPlacement: function (error, element) {
                if (element.hasClass('business_type')) {
                    error.insertAfter(element.next('.select2-container'));
                }
                else if (element.hasClass('privacy_policy_reg')) {
                    error.insertAfter(jQuery(".privacy_policy_reg_label"));
                }
                else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function (form) {
                form.submit();
            }
        });
    }
    jQuery('#modal_reg_notification').modal('show');

    //login form validation
    if (jQuery('.login_form').length > 0) {
        jQuery(".login_form").validate({
            rules: {
                user_email: {
                    required: true,
                    // email: true
                },
                user_password: {
                    required: true
                }
            },
            submitHandler: function (form) {
                jQuery.ajax({
                    url: custom.ajaxurl,
                    data: jQuery(form).serialize(),
                    type: 'POST',
                    beforeSend: function (xhr) {
                        jQuery('#login_form .login_res').html('');
                        jQuery('#login_form .ajax-loader').css('visibility', 'visible');
                    },
                    success: function (data) {
                        jQuery('#login_form .ajax-loader').css('visibility', 'hidden');
                        var response = JSON.parse(data);
                        if (response.status == 'success') {
                            var message = '<div class="alert alert-success alert-dismissible fade show" role="alert">' + response.msg + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                            jQuery('#login_form .login_res').html(message);

                            window.setTimeout(function () {
                                var href = location.href;
                                var loginURL = href.match(/([^\/]*)\/*$/)[1];
                                if (loginURL == 'login') {
                                    window.location.href = response.redirect;
                                } else {
                                    document.location.href = window.location.href;
                                }

                            }, 2000);
                        } else {
                            var message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">' + response.msg + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                            jQuery('#login_form .login_res').html(message);
                        }
                    }
                });
            }
        });

    }

});

//Load more order

jQuery(function ($) {
    var current_page = 1;
    var totalorder = jQuery('.view-more-order').attr("data-allorder");
    function load_posts() {
        current_page++;
        var data = {
            'action': 'order_loadmore',
            'page': current_page
        };
        $.ajax({
            url: custom.ajaxurl,
            data: data,
            type: 'POST',
            beforeSend: function (xhr) {
                jQuery(".view-more-order").text('Loading...');
            },
            success: function (data) {
                // console.log(current_page);
                //console.log(totalorder);
                //console.log(current_page * 3);
                if ((current_page * 3) >= totalorder) {
                    jQuery(".view-more-order").remove();
                    //jQuery(".view-more-order").attr("disabled", true);
                }
                var $data = jQuery(data);
                if ($data.length) {
                    jQuery("tr:last").after($data);
                    jQuery(".view-more-order").text('View More Orders');
                }
                order_help_form();

            }
        });
        return false;
    }

    jQuery(".view-more-order").on("click", function () {
        load_posts();
    });

});

//checkout page
jQuery(function ($) {

    jQuery(".ocwma_select").change(function () {
        jQuery(this).find("option:selected").each(function () {
            var optionValue = jQuery(this).attr("value");
            if (optionValue) {
                jQuery(".ocwma_bill_table").not("." + optionValue).hide();
                jQuery("." + optionValue).show();
            } else {
                jQuery(".ocwma_bill_table").hide();
            }
        });
    }).change();

    jQuery(".ocwma_select_shipping").change(function () {
        jQuery(this).find("option:selected").each(function () {
            var optionValue = jQuery(this).attr("value");
            if (optionValue) {
                jQuery(".ocwma_ship_table").not("." + optionValue).hide();
                jQuery("." + optionValue).show();
            } else {
                jQuery(".ocwma_ship_table").hide();
            }
        });
    }).change();
});

jQuery(window).load(function () {
    if (jQuery("#modal_reg_notification").length > 0) {
        jQuery('#woo_register').find("input[type=text],input[type=tel], textarea").val("");
        jQuery('select').val(null).trigger('change');
    }
    //jQuery('#modal_reg_notification').modal('show');
});

jQuery("body").on('click', '#togglePassword', function () {
    jQuery(this).toggleClass("fa-eye fa-eye-slash");
    var input = jQuery("#user_password");
    if (input.attr("type") === "password") {
        input.attr("type", "text");
    } else {
        input.attr("type", "password");
    }
});

(function ($) {
    jQuery(document).on('facetwp-loaded', function () {
        var qs = FWP.buildQueryString();
        if ('' === qs) { // no facets are selected
            jQuery('.reset_filter').hide();
        }
        else {
            jQuery('.reset_filter').show();
        }
    });

    document.addEventListener('facetwp-loaded', function () {
        jQuery(document).trigger('yith_wcwl_reload_fragments');
    });

    if (!jQuery('ul.products').length > 0) {
        jQuery('#primary .col-lg-3').hide();
        jQuery('.facetwp-facet').hide();
    }


    /*if (jQuery('#variation_table_wrapper').length > 0) {
        var button = document.getElementById('prev_arrow');
        button.onclick = function () {
            document.getElementById('variation_table_wrapper').scrollLeft -= 100;
        };

        var back = document.getElementById('next_arrow');
        back.onclick = function () {
            document.getElementById('variation_table_wrapper').scrollLeft += 100;
        };
    }*/
})(jQuery);

//Quick order new layout
jQuery(document).ready(function () {
    /*moveed it to doc.ready*/
    jQuery('table.wc-bulk-variations-table').wrap('<div id="variation_table_wrapper" class="variation_table_wrapper"></div>');
   
    jQuery('.single-product .wc-bulk-variations-table-wrapper h5').hide();
    jQuery('.single-product #variation_table_wrapper').hide();

    //cmt - notify me
    //if (jQuery('.variation_table_wrapper tbody').length == 0) {
    /*if (jQuery('.color_box .action_color').length == 0) {
        jQuery('.color_box').hide();
        jQuery('.wc-bulk-variations-table-wrapper').addClass('hide-single-variation-table');
        jQuery(".wc-bulk-variations-table-wrapper").after("<div class='single-out-of-stock'>Out Of Stock</div>");
    }*/

    //out of stock notify me
    if (jQuery('.cwginstock-subscribe-form').length > 0) {
        jQuery(document).ajaxStop(function(){
            setTimeout(function () {
                jQuery('.cwgstock_output').fadeOut(2000);
            }, 4000);
        });
    }

    //jQuery('.outofstock_field_span').parents('.custom-table-row-class').addClass('d-none-imp');
    jQuery('.outofstock_field_span').parents('td').find('.price_field_span').addClass('d-none-imp');
    jQuery('.outofstock_field_span').parents('td').find('.upc_field_span').addClass('d-none-imp');
    jQuery('.outofstock_field_span').parents('td').find('.wcbvp_quantity').addClass('d-none-imp');
    jQuery('.outofstock_field_span').parents('td').find('.msrp_field_span').addClass('d-none-imp');

    jQuery(".single_notifyme a").on('click touchstart', function () {   
        var varid = $(this).data("varid");
        jQuery('.cwg-variation-id').val(varid);
        jQuery('#modal_notifyme').modal('show');
    });



    jQuery(".woocommerce").on("click", ".outofstock_items_chk", function() {

        if($(this).is(":checked")) {
            //$('#variation_table_wrapper').addClass("show_outofstock");
            $('.product_list_section').addClass("show_outofstock");

            //check if show more open then show all colors
            if(jQuery('.product_list_section ').hasClass('show_lessmore')){
                jQuery('.color_out_of_stock').show();
            }

            //show if li is more then 12
            if (jQuery('.s_colors_ul li').length > 12) {
                jQuery('.product_list_section .more-color').show();
            }

            if(jQuery('.product_list_section').hasClass('show_viewmore')){
                jQuery( ".s_colors_ul li" ).not(".s_colors_ul li:lt(12)").hide();
                //jQuery(".s_colors_ul li:lt(12)").slideDown();
            }


        } else {
            //$('#variation_table_wrapper').removeClass("show_outofstock");
            $('.product_list_section').removeClass("show_outofstock");

            if (jQuery('.s_colors_ul .color_in_stock').length <= 12) {
                jQuery('.product_list_section .more-color').hide();
            }
            //check if current open color is out of stock then select in stock color
            if( jQuery('.active_color').closest('li').hasClass('color_out_of_stock')){
                jQuery("li.color_in_stock").eq(0).children('a').trigger('click');
            }
            //when uncheck then show all less color items
            jQuery('.s_colors_ul').find('.less_color_items').not('.color_out_of_stock').show();

        }
    });


    //notify me end

    jQuery(".woocommerce-product-details__short-description").insertAfter(".woocommerce-product-gallery");

    mobile = jQuery(window).width();
    if (mobile <= 767) {
        jQuery(".product_title.entry-title").insertBefore(".woocommerce-product-gallery");
        jQuery(".pro_sku_main").insertBefore(".woocommerce-product-gallery");
        jQuery(".price_section").insertBefore(".woocommerce-product-gallery");
        jQuery(".woocommerce-product-details__short-description").insertAfter(".product_list_section");
    }

    jQuery('<h5>2. Select Size and Quantity:</h5>').insertBefore('#variation_table_wrapper');
    //jQuery('<h5 class="selected_color_h5">Selected Color: <span class="selected_color_name2"></span></h5>').insertBefore('#variation_table_wrapper');

    //out of stock notify me
    var outofstockhtml = '<div class="selected_color_and_outofstock"><h5 class="selected_color_h5">Selected Color: <span class="selected_color_name2"></span></h5><label class="checkbox"><input type="checkbox" class="checkbox-input outofstock_items_chk" name="outofstock_items" value=""><span class="checkbox-checkmark-box"><span class="checkbox-checkmark"></span></span>Include Out Of Stock</label></div>';

    jQuery(outofstockhtml).insertBefore('#variation_table_wrapper');

    if (jQuery('.outofstock_field_span').length == 0) {
        jQuery('.selected_color_and_outofstock .checkbox').hide();
    }
    if (jQuery('#main .outofstock ').length > 0) {
        jQuery('.outofstock_items_chk').trigger('click');
        jQuery('.outofstock_items_chk').closest('label').hide();
    }

    if (jQuery('.s_colors_ul .color_in_stock').length <= 12) {
        jQuery('.product_list_section .more-color').hide();
    }

    jQuery(".color_column").parent().hide();

    jQuery('.wc-bulk-variations-table thead th').not('.wc-bulk-variations-table thead th:nth-child(2)').html('<span class="head_upc">UPC</span><span class="head_price">Price</span><span class="head_msrp">MSRP</span><span class="head_stock">In Stock</span><span class="head_units">Units</span>');

    jQuery('.wc-bulk-variations-table thead th:nth-child(2)').text('Size');

    jQuery('.single-product .product_details_msg').hide();

    jQuery(".div_close").click(function () {
        jQuery('.single-product .product_details_msg').hide();
        //jQuery('.single-product .product_details_msg').remove();
        Cookies.set('ad_add_to_cart_msg', 'hide', { expires: 365 });
    });

    function color_size_select() {

        jQuery(".single-product .color_box .action_color").click(function () {
             console.log('action2');

            //out of stock notify me
            jQuery('.wc-bulk-variations-table tbody tr').removeClass('outofstock_tr');

            jQuery('.single-product .wc-bulk-variations-table-wrapper h5').show();
            jQuery('.single-product #variation_table_wrapper').show();

            var galleryselectimg = jQuery(this).data('galleryselect');
            var showclassname = jQuery(this).data('colorid');
            var count = jQuery(this).data('count');

            jQuery('h5 .selected_color_name').text(jQuery(this).data('colorname'));
            jQuery('h5 .selected_color_name2').text(jQuery(this).data('colorname'));

            //for image changes in gallery
            jQuery(".wpgs-nav .slick-list .slick-track .slick-slide").each(function() {
                var image_src = jQuery(this).children('img').attr('src');  console.log('image_src = '+image_src);
                var last = image_src.split('/').pop();
                var lastIndex = last.lastIndexOf('-');

                var coming_index = last.indexOf("coming-soon");
                if(lastIndex == -1 || coming_index !== -1) {
                    var split_arr = last.split('.');
                    var final_image = split_arr[0];
                } else {
                    var final_image = last.slice(0, lastIndex);
                }
                console.log('final_image = '+final_image);
                console.log('galleryselectimg = '+galleryselectimg);
                if(final_image == galleryselectimg) {
                    jQuery(this).children('img').trigger('click');
                }
            });
            // jQuery(".slick-slide  [src='" + galleryselectimg + "']").trigger('click');

            //for image changes in gallery
            if (jQuery('.woo-product-gallery-slider .slick-slide').length != 1) { 
                // jQuery(".slick-slide  [src='" + galleryselectimg + "']").trigger('click');
                // console.log('length '+jQuery('.woo-product-gallery-slider .slick-slide').length);
            } 
           

            //class add and remove for active color
            jQuery('.color_box img').removeClass('active_color');
            jQuery(this).find('img').addClass('active_color');

            jQuery('.wc-bulk-variations-table tbody tr td').find('.product_not_available').parents('tr').show();

            count = count + 2;
            jQuery('.wc-bulk-variations-table thead th').not('.wc-bulk-variations-table thead th:nth-child(2)').hide();
            if (showclassname) {
                jQuery(".color_column").not("." + showclassname).parent().hide();
                jQuery('.wc-bulk-variations-table thead th:nth-child(2)').show();
                jQuery('.wc-bulk-variations-table thead th:nth-child(' + count + ')').show();
                jQuery('.wc-bulk-variations-table tbody tr td:nth-child(2)').show();
                jQuery('.wc-bulk-variations-table tbody tr td:nth-child(' + count + ')').show();

                //out of stock notify me
                jQuery('.wc-bulk-variations-table tbody tr td:nth-child(' + count + ')').find('.outofstock_field_span').parents('tr').addClass('outofstock_tr');

                var totaloutostock = jQuery('.wc-bulk-variations-table tbody tr td:nth-child(' + count + ')').find('.outofstock_field_span').parents('tr').length;
                var totalproductnotavailble = jQuery('.wc-bulk-variations-table tbody tr td:nth-child(' + count + ')').find('.product_not_available').parents('tr').length;
                
                var tabletotaltr = jQuery('.wc-bulk-variations-table tbody tr').length;
               
                //auto checked if whole colors stock is out of stock
               /* if(tabletotaltr == (totalproductnotavailble + totaloutostock )){
                    if(jQuery('.outofstock_items_chk').prop("checked") == false) {
                        jQuery('.outofstock_items_chk').trigger('click');
                    } 
                }*/
                
                //out of stock notify end

                jQuery('.wc-bulk-variations-table tbody tr td:nth-child(' + count + ')').find('.product_not_available').parents('tr').hide();
                jQuery("." + showclassname).parent().show();
            } else {
                jQuery(".color_column").parent().hide();
            }

            /*jQuery('html,body').animate({
                scrollTop: jQuery(".wc-bulk-variations-table-wrapper").offset().top
            },
                'slow');*/
        });

    }
    function ad_sortTable() {
        var table, rows, switching, i, x, y, shouldSwitch;
        table = document.getElementById("selected_item_table");
        switching = true;
        /* Make a loop that will continue until
        no switching has been done: */
        while (switching) {
            // Start by saying: no switching is done:
            switching = false;
            rows = table.rows;
            /* Loop through all table rows (except the
            first, which contains table headers): */
            for (i = 1; i < (rows.length - 1); i++) {
                // Start by saying there should be no switching:
                shouldSwitch = false;
                /* Get the two elements you want to compare,
                one from current row and one from the next: */
                x = rows[i].getElementsByTagName("TD")[0];
                y = rows[i + 1].getElementsByTagName("TD")[0];
                // Check if the two rows should switch place:
                if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                    // If so, mark as a switch and break the loop:
                    shouldSwitch = true;
                    break;
                }
            }
            if (shouldSwitch) {
                /* If a switch has been marked, make the switch
                and mark that a switch has been done: */
                rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                switching = true;
            }
        }
    }

    jQuery('<div class="selected_item_list"></div>').insertAfter('.wc-bulk-variations-table-wrapper');
    jQuery('.selected_item_list').hide();
    function select_item_func() {
        //product details
        var htmlitems = '<h5>Selected Items:</h5><table id="selected_item_table" class="table"><thead><tr><th>Color</th><th>Size</th><th>Qty</th></tr></thead><tbody>';

        // var itemcount = 0;
        jQuery(".wc-bulk-variations-table tr td").each(function () {
            var item_inputqty = jQuery(this).find('.wcbvp_quantity').val();

            if (item_inputqty && (item_inputqty != 0)) {
                var item_color_name = jQuery(this).find('.color_column').data('cname');
                var item_size_name = jQuery(this).parent('tr').data('csize');
                //console.log(item_color_name);
                //console.log(item_size_name);
                htmlitems += '<tr><td>' + item_color_name + '</td><td>' + item_size_name + '</td><td>' + item_inputqty + '</td></tr>';
                //itemcount++;
            }
        });
        //console.log(itemcount);
        /* if(itemcount == 0){
             jQuery(".single_selected_item").removeClass("selected_items_total");
         }else{
             jQuery(".single_selected_item").addClass("selected_items_total");
         }*/
        htmlitems += '</tbody></table>';
        //console.log(htmlitems);
        jQuery('.selected_item_list').html(htmlitems);
    }

    setTimeout(function () {
        color_size_select();
        select_item_func();
        // jQuery('.wcbvp_quantity').keyup(function(e){
        jQuery(".wcbvp_quantity").on('keyup mouseup change', function (e) {
            select_item_func();
            ad_sortTable();
        });
        //jQuery(".single-product .color_box .action_color").click(function () {
        jQuery(".single-product .color_box .action_color").one("click", function(event){
            
            var ad_show = Cookies.get('ad_add_to_cart_msg');
            if(ad_show != 'hide'){
                jQuery('.single-product .product_details_msg').show();
            }
            
        });
        //jQuery("li [data-count='1']").trigger('click');
    }, 500);
    setTimeout(function () {
        //jQuery("li [data-count='1']").trigger('click');
        if(jQuery('li.color_in_stock').length == 1){
            jQuery("li [data-count='1']").trigger('click'); 

            /*console.log('limit='+jQuery('li.color_in_stock').length);*/
        }else{
            jQuery("li.color_in_stock").eq(0).children('a').trigger('click');
        }
       
    }, 1000);

    jQuery('.selected_items_total').click(function () {
        jQuery('.selected_item_list').slideToggle();
        //jQuery('.selected_items_total').toggleClass('sel_item');
    });

    jQuery('[name="quantity"]').on('input', function (e) {
        var qty_box = jQuery(this);
        var nextinstockfield = jQuery(this).parents('td').find('.color_column');
        var error_message = jQuery('<span class="error_msg">You can not buy more then available stock</span>');
        if (parseInt(qty_box.val()) > parseInt(qty_box.attr('max'))) {
            if (jQuery(this).parents('td').find('.error_msg').length < 1) {
                jQuery(error_message).insertAfter(nextinstockfield);
            }
        }
        else {
            jQuery(this).parents('td').find('.error_msg').remove();
        }
    });

    setTimeout(function () {
        jQuery('[data-name="qty"]').on('input', function (e) {
            var qty_box = jQuery(this);
            var nextinstock_field = jQuery(this).parents('.product-row.rowp').find('.instock_qty');
            var error_message = jQuery('<div class="error_msg">You can not buy more then available stock</div>');
            if (parseInt(qty_box.val()) > parseInt(qty_box.attr('max'))) {
                if (jQuery(this).parents('.product-row.rowp').find('.error_msg').length < 1) {
                    jQuery(error_message).insertAfter(nextinstock_field);
                }
            }
            else {
                jQuery(this).parents('.product-row.rowp').find('.error_msg').remove();
            }
        });
    }, 500);


    jQuery(".single_add_to_cart_button").click(function (event) {
        if (jQuery('#wcbvp_add_to_cart .disabled').length > 0) {
            event.preventDefault();
        }
    });

    jQuery(".single_add_to_cart_button").click(function (event) {
        if (jQuery('.fixed_add_to_cart .disabled').length > 0) {
            event.preventDefault();
            jQuery('html,body').animate({
                scrollTop: jQuery(".product_list_section").offset().top
            },
                'slow');
        }

    });

    jQuery(".single_add_to_cart_button").click(function (event) {
        if (jQuery('.outviewport .disabled').length > 0) {
            event.preventDefault();
            jQuery('html,body').animate({
                scrollTop: jQuery(".product_list_section").offset().top
            },
                'slow');
        }

    });

});

(function ($) {

    $.fn.visible = function (partial) {

        var $t = jQuery(this),
            $w = jQuery(window),
            viewTop = $w.scrollTop(),
            viewBottom = viewTop + $w.height(),
            _top = $t.offset().top,
            _bottom = _top + $t.height(),
            compareTop = partial === true ? _bottom : _top,
            compareBottom = partial === true ? _top : _bottom;
        return ((compareBottom <= viewBottom) && (compareTop >= viewTop));

    };

})(jQuery);

jQuery(document).ready(function () {

    if (jQuery('body.single-product .variation_table_wrapper').length > 0) {

        jQuery(".wcbvp_quantity").val("");
        jQuery(".wcbvp_quantity").attr("placeholder", "0");

        jQuery(window).scroll(function (event) {
            var el = jQuery('.wcbvp-total-wrapper');
            if (el.visible(true)) {
                el.removeClass("outviewport");
                jQuery('.type-product').removeClass('parent_fixed_cart');
            } else {

                jQuery('.type-product').addClass('parent_fixed_cart');
                el.addClass("outviewport");

            }
        });
    }
});

//Quick order new layout
//Mylist
jQuery(document).ready(function () {

    if (jQuery('body.page-my-account').length > 0) {
        jQuery('.social_share_list').hide();
        jQuery('.social_share_list_action i').click(function () {
            jQuery(this).parents('.yith-wcwl-share').find('.social_share_list').slideToggle();
        });
    }

    jQuery(".woocommerce-order-details .order_details tr").each(function () {
        var item_track = jQuery(this).find('.tracking-content-div').length;
        if (item_track != 0) {
            jQuery(this).hide();

        }
    });

    if (jQuery('.shipment_head_tracking').length > 0) {
        jQuery('.shipment_head_default').hide();
    }

    var trackdiv = jQuery('.tracking-content-div').length;
    var total_item = jQuery('#collapse_a1 tr').length;

    if (trackdiv >= total_item) {
        jQuery('.woocommerce_order_details_custom').hide();
    }

    //jQuery('#usaepaytransapi-card-expiry').attr("placeholder", "MM/YYYY");
    //jQuery('label[for=usaepaytransapi-card-expiry]').html('Expiry (MM/YYYY) <span class="required">*</span>');
    jQuery('#usaepaytransapi-card-expiry').attr('maxlength','7');
    
    jQuery('body').on('updated_checkout', function () {
        jQuery('#usaepaytransapi-card-expiry').attr('maxlength','7');
       // jQuery('#usaepaytransapi-card-expiry').attr("placeholder", "MM/YYYY");
        //jQuery('label[for=usaepaytransapi-card-expiry]').html('Expiry (MM/YYYY) <span class="required">*</span>');
    });

    jQuery(".woocommerce").on("click", ".rm_items_chk_all", function() {
        jQuery('input:checkbox.rm_items_chk').not(this).prop('checked', this.checked);
    });
});





jQuery(document).ready(function ($) {
    // Add to cart button click handler
    $('.add-to-cart-button').on('click', function () {
        var productId = $(this).data('product-id');
		var productQty = $(this).attr('data-prod-qty');
        
        // AJAX request to add the product to the cart
        $.ajax({
            type: 'POST',
            url: ajax_url, // Make sure you have defined this variable
            data: {
                action: 'add_to_cart_action',
				mwb_woo_nonce : mwb_woo_smc_param.mwb_woo_smc_nonce,
                product_id: productId,
				MwbWooMovedProdQty: productQty,
				MwbWooMoved : 'mwb_remove_to_cart'
            },
            success: function (response) {
                // Handle the response from the server (e.g., display a success message)
                console.log(response);
				 location.reload();
            }
        });
    });
});