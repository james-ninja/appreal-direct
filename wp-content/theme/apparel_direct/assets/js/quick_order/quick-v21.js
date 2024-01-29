(function ($) {

    var products;
    //var ajaxclass = "ajax2";
    var ajaxclass = "blockUI blockOverlay";
    var variations = {};
    var variations_data = {};
    var variation_data_custom = {};
    var attributes = {};
    var skus = {};

    /**
     * Events & links
     */

    $(document).on('submit', '#quick-v2[data-action="submit"]', quick_v2);
    $(document).on('click', '#quick-v2 [type="submit"]', quick_v2_submit);
    $(document).on('products-load', quick_v2_products);
    $(document).on('change', 'select.product', quick_v2_product_change);

    $(document).on('change', 'input[data-name="product"]', quick_v2_product_change);
    $(document).on('awesomplete-selectcomplete', 'input[data-name="product"]', quick_v2_product_change);

    //$(document).on('change', 'input[data-name="product_style"]', quick_v2_product_change);
    $(document).on('awesomplete-selectcomplete', 'input[data-name="product_style"]', quick_v2_product_change);

    //$(document).on('change', 'input[data-name="upc"]', quick_v2_product_change);
    $(document).on('awesomplete-selectcomplete', 'input[data-name="upc"]', quick_v2_product_change);

    $(document).on('click', '.add-more-item', add_more_item);
    $(document).on('click', '.execute-order .add-item', quick_v2_add_item);
    $(document).on('click', '.page-order .add-item', quick_v2_add_item);
    $(document).on('click', '.row .remove', quick_v2_remove_item);
    $(document).on('click', '.row .each', quick_v2_each);

    $(document).on('change', '.attrib-pa_size select.attribute', quick_v2_attribute_change_size);
    $(document).on('change', '.attrib-pa_color select.attribute', quick_v2_attribute_change_color);

    $(document).on('change', 'select.attribute', quick_v2_attribute_change);
    $(document).on('change', 'select.attribute', quick_v2_update_qty);
    $(document).on('check-product', '.row', quick_v2_row);
    $(document).on('change', '.quick-v2 #ship-to-different-address-checkbox', quick_v2_ship);
    $(document).on('click', '.expand', quick_v2_expand);

    $(document).on('input', '[data-name="qty"]', quick_v2_update_qty);
    $(document).on('keypress', '[data-name="qty"]', quick_v2_keypress_qty);
    $(document).on('keypress', '[data-name="rxtray"]', quick_v2_keypress_rxtray);



    $(document).on('update', '.table', quick_v2_update_table);
    $(document).on('input', '#quick-v2 :input', quick_v2_changed);

    //     $( document ).on( 'chosen:activate', 'select', quick_v2_chosen_activate );
    //     $( document ).on( 'focus', '.input', quick_v2_chosen_activate );
    //     $( document ).on( 'keypress', '.chosen-container', quick_v2_chosen_open );
    //     $( document ).on( 'focus', '*', function(event) {
    //         var el = $( event.target );
    //         console.log( event.target.outerHTML );
    //     } );

    /**

     * DOM ready

     *

     */

    $(function () {

        var ifexist = $('input[data-name="product_id"]').val();

        if (ifexist == "") {

            $('.block-quick-info select').css('width', '100%').select2();

            $(document).trigger('products-load');

            $('.quick-v2 #ship-to-different-address-checkbox').trigger('change');
            $('[data-name="qty"]').trigger('input');

            if ($('.page-executiveorders #quick-v2, .page-internalorders #quick-v2').length) {

            }

            else {
                $('.expand').trigger('click');
            }

            $('.validate-required input, .validate-required select').attr('required', 'required');

            for (var i = 0; i < 10; i++) {

                $('.execute-order .add-item').trigger('click', { generated: 1 });

            }
            for (var i = 0; i < 5; i++) {

                $('.page-order .add-item').trigger('click', { generated: 1 });

            }

            $('select.product').first().trigger('chosen:activate');
            $('.rowp select.product').first().attr('required', 'required');

        }

    });


    function add_more_item() {
        for (var i = 0; i < 5; i++) {
            $('.add-item').trigger('click');
        }
    }


    function quick_v2_submit(event) {

        var form = $(event.target).closest('form');
        if (!form[0].checkValidity()) {
            $('.expand.collapse').trigger('click');
        }

    }

    /**
     * Process submitting the form
     */

    function quick_v2(event) {

        event.preventDefault();

        var form = $(event.target);

        form.addClass(ajaxclass);

        var data = {};

        form.find(':input').each(function () {

            var el = $(this);
            var n = el.attr('name');
            var v = el.val();
            var tag = el.prop('tagName').toLowerCase();

            if (typeof (n) != 'undefined') {
                if (el.is('[type="file"]')) {
                    var val = el[0].files[0];
                    data[n] = val;
                }
                else if (el.is('[type="checkbox"]')) {
                    data[n] = el.is(':checked') ? 1 : 0;
                }
                else {
                    data[n] = v;
                }
            }
        });

        data.action = 'quick_v2_submit';

        // AJAX CALL

        var a = $.ajax({

            url: woo2.ajaxurl,
            method: "POST",
            data: data,
            dataType: "json"

        });

        a.done(function (response) {

            if (response.error) {
                if (response.message) {
                    alert(response.message);
                }
                else {
                    alert('There was an error processing your order. Please review the form and try again.');
                }
            }
            else {
                location.href = response.redirect;
                return false;
            }
            form.removeClass(ajaxclass);
        });
        return false;
    }

    /**
     *
     *
     */

    function quick_v2_products() {

        var all = $('#all');

        if (typeof (products) == 'undefined' && all.length) {
            products = JSON.parse(all.html());
        }

        else if (typeof (products) == 'undefined') {
            products = {};
        }

    }

    /**
     *
     *
     */

    function quick_v2_product_change(event, qty) {


        var el = $(event.target);

        var data_el = $('#all_products li').filter(function (index) { return $(this).text().toLowerCase() === el.val().toLowerCase(); });

        var data_el_style = $('#all_products_style li').filter(function (index) { return $(this).text().toLowerCase() === el.val().toLowerCase(); });

        //variation data
        var data_el_variation_upc = $('#all_products_variation_upc li').filter(function (index) { return $(this).text().toLowerCase() === el.val().toLowerCase(); });


        var sku_variation = data_el_variation_upc.attr('data-id');
        var sku_parent = data_el_variation_upc.attr('data-parentid');

        var sku_style = data_el_style.attr('data-id');
        if (!sku_parent) {
            sku_parent = sku_style;
        }

        parent_product_name = jQuery('#all_products [data-id="' + sku_parent + '"]').text();

        var sku = data_el.attr('data-id');



        var cont = el.closest('.row');
        //cont.addClass(ajaxclass);
        jQuery('.table.master').addClass(ajaxclass);
        cont.find('[data-attrib]').html('');
        cont.removeClass('product-row available no-available');
        cont.find('.qty_cal').prop('disabled', false);
        cont.find('.product_not_available').text("");
        if (typeof (skus[sku]) != 'undefined') {
            quick_v2_populate_item(cont, skus[sku], qty);
            //cont.removeClass(ajaxclass);
            jQuery('.table.master').removeClass(ajaxclass);
            return;
        }

        if (sku_style) {
            sku = sku_style;
        }


        if (sku_parent) {
            sku = sku_parent;
        }

        var a = $.ajax({

            url: woo2.ajaxurl,
            method: "POST",
            dataType: "json",
            data: {
                action: "woo2_load_product",
                sku: sku,
                sku_parent: sku_parent,
                sku_variation: sku_variation,
                type: "json"
            }
        });

        //console.log( a );

        a.done(function (data) {

            if (data.error) {
                //cont.addClass('error');
                cont.removeClass('product-row');
            }
            else {
                variations[data.id] = data.variations;
                variations['varid'] = sku_variation;
                //variations['variations_filter'] = data.variations_filter;
                variations['variations_filter_' + data.id] = data.variations_filter;
                variations_data[data.id] = data.variation_data;
                // variation_data_custom = data.variation_data;
                variation_data_custom[data.id] = data.variation_data;
                attributes[data.id] = data.vars;
                skus[sku] = data.id;
                quick_v2_populate_item(cont, data.id, 1);
                if (sku_variation || sku_style) {
                    cont.find('[data-name="product"]').val(parent_product_name);
                }
            }
            //cont.removeClass(ajaxclass);
            jQuery('.table.master').removeClass(ajaxclass);

        });
    }

    /**
     *
     *
     */

    function quick_v2_populate_item(cont, id) {

        cont.addClass('product-row');
        cont.find('[data-name="product_id"]').val(id);
        var el_qty = cont.find('[data-name="qty"]');

        if (!el_qty.val()) {
            el_qty.val(1);
        }

        el_qty.trigger('input');

        var first = true;
        var to_focus;



        $.each(attributes[id], function (attrib, values) {

            var index = cont.attr('data-index');
            var count = Object.keys(values).length;

            var s = $('<select>').addClass('attribute').css({
                width: '100%'

            }).attr({
                name: 'pr[' + index + '][attribute_' + attrib + ']',
                required: 'required'
            });

            if (count > 1) {
                //$('<option>').attr('value', '').text('Please Select...').appendTo(s);
                $('<option>').attr('value', '').text('Please Select / Reset Options').appendTo(s);
            }

            $.each(values, function (value, label) {
                $('<option>').attr({
                    value: value
                }).text(label).appendTo(s);
            });

            var cell = cont.find('[data-attrib="' + attrib + '"]');

            cell.html(s);

            //             s.select2({
            //                 minimumResultsForSearch: Infinity
            //             });
            //             s.chosen({
            //                 disable_search_threshold: 10,
            //                 search_contains: true,
            //                 width: '100%'
            //             });
            //             s.css({
            //                 position: 'absolute',
            //                 display: 'block !important',
            //                 width: '1px',
            //                 'z-index': '-1'
            //             });



            if (count == 1) {
                s.trigger('change');
            }
            else if (first) {
                //                 s.select2( 'open' );
                s.trigger('change');
                s.focus();
                to_focus = s;
                //                 s.trigger( 'chosen:activate' );
                //                 console.log( 'quick_v2_populate_item' );
                first = false;
            }
            //             s.attr('tabindex', '-1');
            //             $('.chosen-results').attr('tabindex', '-1');
        });

        if (variations['varid']) {
            var jsonParsedArray = JSON.parse(JSON.stringify(variations[id]));
            for (key in jsonParsedArray) {
                if (jsonParsedArray.hasOwnProperty(key)) {
                    if (jsonParsedArray[key] == variations['varid']) {
                        var selected_att = key;
                    }
                }
            }
            const attArr = selected_att.split(":");
            //console.log(variations['varid']);
            // console.log(attArr[0]);
            cont.find('.attrib-pa_size select').val(attArr[0]).change();
            cont.find('.attrib-pa_color select').val(attArr[1]).change();
        }

        cont.closest('.table').trigger('update');
        //         to_focus.focus();
    }

    /**
     *
     *
     */

    function quick_v2_add_item(event, variation) {

        event.preventDefault();

        var cont = $('.table.master');

        if ((typeof (variation) != 'undefined') && (cont.find('.row:not(.product-row):not(.thead)').length) && (typeof (variation.generated) == 'undefined')) {
            var clone = cont.find('.row:not(.product-row):not(.thead)').first();
        }
        else {
            var index = parseInt(cont.attr('data-index'));
            cont.attr('data-index', index + 1);
            var clone = $('.cloner .row').clone().slideUp(0);
            clone.attr('data-index', index);
            clone.find('[data-name]').each(function () {

                var el = $(this);
                var name = el.attr('data-name');
                el.attr('name', 'pr[' + index + '][' + name + ']');
                if ((index == 0) && (name == 'product')) {
                    el.attr('required', 'required');
                }

                if (name == 'product') {
                    el.attr('id', 'pr_' + index);
                }

                if (name == 'upc') {
                    el.attr('id', 'prupc_' + index);
                }

                if (name == 'product_style') {
                    el.attr('id', 'prstyle_' + index);
                }
            });

            //             clone.find( 'select.product' ).select2({
            //                 data: products
            //             });
            //             var sel_products = clone.find( 'input.product' );
            //             $.each( products, function( index, value ) {
            //                 $( '<option>' ).val( value.id ).text( value.text ).appendTo( sel_products );
            //             });

            cont.append(clone);

            var product = clone.find('[data-name="product"]').first();
            var input = document.getElementById(product.attr('id'));

            var product_upc = clone.find('[data-name="upc"]').first();

            var input_upc = document.getElementById(product_upc.attr('id'));

            var product_style = clone.find('[data-name="product_style"]').first();
            var input_style = document.getElementById(product_style.attr('id'));

            var combopleteupc = new Awesomplete(input_upc, {

                autoFirst: true,
                minChars: 0,
                list: "#all_products_variation_upc",
                maxItems: 10

            });

            var comboplete = new Awesomplete(input, {

                autoFirst: true,
                minChars: 0,
                list: "#all_products",

                /* filter: function( text, input ) {
                     if( input == '' ) {
                         return false;
                     }
                     var inp = input.toLowerCase();
                     var itt = quick.products[text.value]; 
                     if(itt !="" && itt != undefined){ 
                         var itt = itt.toString();
                         if( ( itt.indexOf( inp ) != 0 ) && ( text.toLowerCase().indexOf( inp ) != 0 ) ) {
                             return false;
                         }
                     }
                     return true;
                 },*/

                maxItems: 10

            });

            var combopletevariation = new Awesomplete(input_style, {

                autoFirst: true,
                minChars: 0,
                list: "#all_products_style",
                maxItems: 10

            });

            //             console.log( $( '#' + product.attr('id') + ':after' ) );
            //             Awesomplete.$('[name="pr[' + index + '][btn]"]').addEventListener( "click", function() {
            //             Awesomplete.$( '#' + product.attr('id') ).addEventListener( "focus", function() {
            //                 if( comboplete.ul.childNodes.length === 0 ) {
            //                     comboplete.minChars = 0;
            //                     comboplete.evaluate();
            //                 }
            //                 else if( comboplete.ul.hasAttribute('hidden') ) {
            //                     comboplete.open();
            //                 }
            //                 else {
            //                     comboplete.close();
            //                 }
            //             });

            //             sel_products.chosen({
            // //                 max_shown_results: 5,
            //                 search_contains: true,
            //                 width: '100%'
            //             });

            //             sel_products.attr('tabindex', '-1');
            //             $('.chosen-results').attr('tabindex', '-1');

            clone.show(0, function () {

                if (typeof (variation) == 'undefined') {
                    //                     clone.find( 'select.product' ).select2( 'open' );
                    //                     sel_products.trigger( 'chosen:activate' );
                    //                     console.log( 'quick_v2_add_item', '1' );
                }
            });
        }

        cont.closest('.table').trigger('update');

        if ((typeof (variation) != 'undefined') && typeof (variation.hitenter) != 'undefined') {
            clone.find('select.product').trigger('chosen:activate');
        }

        else if ((typeof (variation) != 'undefined') && (!variation.generated)) {
            quick_v2_set_data(clone, variation);
        }
    }


    /**
     *
     *
     */

    function quick_v2_remove_item(event) {

        event.preventDefault();

        var row = $(event.target).closest('.row');

        row.fadeOut(400, function () {
            row.remove();
            quick_v2_update_qty();

        });

    }

    /**
     *
     *
     */

    function quick_v2_attribute_change_size(event) {

        var target = this.value;

        var row = $(event.target).closest('.row');

        var productid = row.find('[data-name="product_id"]').val();

        var available_options = $.map(variations['variations_filter_' + productid], function (e) {
            if (e.pa_size == target) {
                return e;
            }
        });

        row.find('.error_msg').remove();

        var selectoptions = new Array();

        if (available_options && available_options.length > 0) {
            jQuery(available_options).each(function (key, value) {
                selectoptions.push(value['pa_color']);
            });

            row.find(".attrib-pa_color select option").each(function () {
                var $thisOption = $(this);

                if (jQuery.inArray($thisOption.val(), selectoptions) > -1) {
                    $thisOption.removeAttr("disabled");
                } else {
                    if ($thisOption.val()) {
                        $thisOption.attr("disabled", "disabled");
                    }
                }
            });
        } else {
            row.find(".attrib-pa_color select option").removeAttr("disabled");
        }
    }

    function quick_v2_attribute_change_color(event) {

        var target = this.value;
        var row = $(event.target).closest('.row');
        row.find('.error_msg').remove();
        var productid = row.find('[data-name="product_id"]').val();

        var available_options = $.map(variations['variations_filter_' + productid], function (e) {
            if (e.pa_color == target) {
                return e;
            }

        });

        var selectoptions = new Array();

        if (available_options && available_options.length > 0) {
            jQuery(available_options).each(function (key, value) {
                selectoptions.push(value['pa_size']);
            });

            row.find(".attrib-pa_size select option").each(function () {
                var $thisOption = $(this);

                if (jQuery.inArray($thisOption.val(), selectoptions) > -1) {
                    $thisOption.removeAttr("disabled");
                } else {
                    if ($thisOption.val()) {
                        $thisOption.attr("disabled", "disabled");
                    }
                }
            });
        } else {
            row.find(".attrib-pa_size select option").removeAttr("disabled");
        }

    }

    function quick_v2_attribute_change(event) {

        var el = $(event.target);
        if (jQuery(this).val() == "") {

            el.closest('.row').find('[data-name="upc"]').val(" ");
            el.closest('.row').find('.instock_qty').text("");
            el.closest('.row').find('.rxtray_price').text("0");
            el.closest('.row').find('.qty_cal').attr({
                "max": 0
            });
        }
        el.closest('.row').trigger('check-product');

    }

    /**
     *
     *
     */

    function quick_v2_row(event) {

        var row = $(event.target);

        row.find('[data-name="variation_id"]').val('');

        row.removeClass('available no-available');
        row.find('.qty_cal').prop('disabled', false);
        row.find('.product_not_available').text("");

        var first;
        var missing = false;
        var keys = [];

        row.find('select.attribute').each(function () {

            var el = $(this);
            if (!el.val()) {
                missing = true;
                first = el;
            }

            keys.push(el.val());

        });


        if (missing) {

            if (typeof (first) != 'undefined') {
                //                 first.select2( 'open' );
                //                 first.trigger( 'chosen:activate' );
                //                 console.log( 'quick_v2_row' );
            }

            return;

        }

        var product_id = row.find('[data-name="product_id"]').val();

        var key = keys.join(':');

        var allclass = row.attr("class");

        if (allclass.search('editorder') > -1) {

            var pname = $(event.target).find('input[data-name="product"]').val();
            var data_el = $('#all_products li').filter(function (index) { return $(this).text().toLowerCase() === pname.toLowerCase(); });
            var sku = data_el.attr('data-id');

            //row.addClass(ajaxclass);
            jQuery('.table.master').addClass(ajaxclass);
            


            var a = $.ajax({
                url: woo2.ajaxurl,
                method: "POST",
                dataType: "json",
                data: {
                    action: "woo2_load_product",
                    sku: sku,
                    type: "json"
                }
            });

            a.done(function (data) {

                if (data.error) {
                    //row.addClass('error');
                    row.removeClass('product-row');
                } else {

                    variations[data.id] = data.variations;
                    variations_data[data.id] = data.variation_data;
                    attributes[data.id] = data.vars;
                    skus[sku] = data.id;

                    if (typeof (variations[product_id][key]) == 'undefined') {
                        row.addClass('no-available');
                        //console.log('not-availabe');
                    }

                    else {
                        row.addClass('available');
                        row.find('[data-name="variation_id"]').val(variations[product_id][key]);
                        row.find('[data-name="qty"]').first();
                    }
                }

                //row.removeClass(ajaxclass);
                jQuery('.table.master').removeClass(ajaxclass);
                

            });

        } else {

            if (typeof (variations[product_id][key]) == 'undefined') {

                row.addClass('no-available');
                //console.log('not-availabe2');

                row.find('.qty_cal').prop('disabled', true);
                row.find('.product_not_available').text("This variation not available.");

                row.find('.rxtray_price').text("0");
                row.find('.qty_cal').attr({
                    "max": 0
                });
                row.find('[data-name="upc"]').val(" ");
                row.find('[data-name="product_variation"]').val(" ");
                row.find('.instock_qty').text("");
            }

            else {

                row.addClass('available');
                //custom
                row.find('[data-name="variation_id"]').val(variations[product_id][key]);
                //price get
                var variation_data_extra = variation_data_custom[product_id][variations[product_id][key]];

                if (variation_data_extra['variation_price']) {
                    row.find('[data-name="variation_price"]').val(variation_data_extra['variation_price']);
                }
                if (variation_data_extra['variation_upc']) {
                    row.find('[data-name="upc"]').val(variation_data_extra['variation_upc']);
                }

                /* if(variation_data_extra['variation_sku']){
                     row.find('[data-name="product_variation"]').val(variation_data_extra['variation_sku']);
                     row.find('.qty_cal').val(1);
                 }*/

                if (variation_data_extra['product_style']) {
                    row.find('[data-name="product_style"]').val(variation_data_extra['product_style']);
                    row.find('.qty_cal').val('');
                    row.find('.qty_cal').attr("placeholder", "0");
                   
                }

                row.find('.instock_qty').text(variation_data_extra['variation_max_qty']);

                row.find('.qty_cal').attr({
                    "max": variation_data_extra['variation_max_qty']
                });
                //mt custom end

                row.find('[data-name="qty"]').first();

            }

        }

    }


    /**
     *
     */

    function quick_v2_each(event) {

        event.preventDefault();

        var el = $(event.target);
        var row = el.closest('.row');
        var product_id = row.find('[data-name="product_id"]').val();
        var sku = row.find('[data-name="product"]').val();
        var qty = row.find('[data-name="qty"]').val();

        var selected = {};

        row.find('select.attribute option:selected').each(function () {

            var attrib = $(this).closest('.attrib');
            var attrib_name = attrib.attr('data-attrib');
            var val = $(this).val();
            if (val) {
                selected[attrib_name] = val;
            }
        });

        var first = true;

        $.each(variations_data[product_id], function (index, variation) {

            variation.sku = sku;
            variation.qty = qty;
            var skip = false;

            $.each(selected, function (attrib, val) {
                if (variation.attributes[attrib] != val) {
                    skip = true;
                }
            });

            if (skip) {
                return;
            }

            if (first) {
                quick_v2_set_data(row, variation);
                first = false;
            }

            else {
                $('.add-item').trigger('click', variation);
            }

        });

    }


    /**
     *
     */

    function quick_v2_set_data(row, data) {

        var sku = data.sku;
        var qty = data.qty;

        if (!qty) {
            qty = 1;
        }



        row.find('[data-name="qty"]').val(qty);
        row.find('[data-name="product"]').val(data.sku);
        row.find('[data-name="product"]').trigger('change');

        var keys = Object.keys(data.attributes);

        row.find('select.attribute').each(function (index) {

            var el = $(this);
            var value = data.attributes[keys[index]];

            el.find('[value="' + value + '"]').attr('selected', 'selected');
            el.trigger('change');

        });
    }

    /**
     *
     */

    function quick_v2_ship(event) {

        var el = $(event.target);
        var cont = el.closest('.block-quick-info-shipping').find('.shipping_address');

        if (el.is(':checked')) {
            if (!$('body').hasClass('slug-orders')) {
                cont.slideDown();
            }

            cont.find('input, select').removeAttr('disabled');
            //             cont.find('.validate-required input, .validate-required select').attr( 'required', 'required' );

        }

        else {

            if (!$('body').hasClass('slug-orders')) {
                cont.slideUp();
            }

            //             cont.find('.validate-required input, .validate-required select').removeAttr( 'required', 'required' );
            cont.find('input, select').attr('disabled', 'disabled');
        }

    }


    /**
     *
     */

    function quick_v2_expand(event) {

        var el = $(event.target);

        el.toggleClass('collapse');

        var alt = el.attr('data-alt');
        var text = el.text();

        el.text(alt);

        el.attr('data-alt', text);

        $('.block-quick-info').slideToggle();

    }


    /**
     *
     */

    function quick_v2_update_qty(event) {

        var sum = 0;

        /* var max = parseInt(jQuery(this).attr('max'));
         var min = parseInt(jQuery(this).attr('min'));
         if (jQuery(this).val() > max) {
             jQuery(this).val(max);
         } else if (jQuery(this).val() < min) {
             jQuery(this).val(min);
         }*/

        var max = parseInt(jQuery(this).closest('.row').find('[data-name="qty"]').attr('max'));
        var min = parseInt(jQuery(this).closest('.row').find('[data-name="qty"]').attr('min'));

        if (jQuery(this).closest('.row').find('[data-name="qty"]').val() > max) {
            jQuery(this).closest('.row').find('[data-name="qty"]').val(max);
        } else if (jQuery(this).closest('.row').find('[data-name="qty"]').val() < min) {
            jQuery(this).closest('.row').find('[data-name="qty"]').val(min);
        }

        var single_price = jQuery(this).closest('.row').find('[data-name="variation_price"]').val()
        var single_qty = jQuery(this).closest('.row').find('[data-name="qty"]').val()
        var single_vari_totalprice = (single_qty * single_price).toFixed(2);

        jQuery(this).closest('.row').find('.rxtray_price').html(single_vari_totalprice);


        $('[data-name="qty"]').each(function () {
            var val = $(this).val();
            if (val) {
                sum += parseInt(val);
            }
        });


        var sumprice = 0;
        $('.rxtray_price').each(function () {
            var val = $(this).text();
            if (val) {
                sumprice += parseFloat(val);
            }
        });

        if (sumprice > 0) {
            jQuery('.add_to_cart_quick').prop('disabled', false);
        } else {
            jQuery('.add_to_cart_quick').prop('disabled', true);
        }

        //$('.total_price b').text(sumprice);
        $('.total_price b').text(parseFloat(sumprice).toFixed(2));

        $('.total b').text(sum);

    }

    /**
     *
     */

    function quick_v2_keypress_qty(event) {

        if (event.keyCode == 13) {

            event.preventDefault();

            var el = $(event.target);

            var cont = el.closest('.row');

            cont.find('[data-name="rxtray"]').focus();

        }

    }

    /**
     *
     */

    function quick_v2_keypress_rxtray(event) {

        if (event.keyCode == 13) {
            event.preventDefault();
            $('.add-item').trigger('click', { hitenter: true });
        }

    }

    /**
     *
     */

    function quick_v2_update_table(event) {

        var el = $(event.target);
        var hides = {};
        var hide_action = true;

        el.find('.row:not(.thead)').each(function () {

            var row = $(this);

            row.find('.attrib').each(function () {

                var attrib = $(this).attr('data-attrib');

                if (typeof (hides[attrib]) == 'undefined') {
                    hides[attrib] = true;
                }

                if ($.trim($(this).text()) != '') {
                    hides[attrib] = false;
                }

            });

        });

        $.each(hides, function (key, value) {

            if (value) {
                $('.attrib-' + key).hide();
            }

            else {
                $('.attrib-' + key).css('display', 'table-cell');
            }

        });

    }

    /**
     *
     */

    function quick_v2_chosen_activate(event) {

        var el = $(event.target);

        $('.chosen-container-active').each(function () {

            var m = $(this).prev('select');

            if (el.get(0) != m.get(0)) {
                $(this).removeClass('chosen-container-active');
            }

        });

    }

    /**
     *
     */

    function quick_v2_chosen_open(event) {

        if (event.which === 13) {

            var el = $(event.target);

            event.preventDefault();

            var select = el.closest('.chosen-container').prev('select');

            select.trigger('chosen:open');

        }

    }

    function quick_v2_changed(event) {

        formIsDirty = true;

    }

    window.onload = function () {

        window.addEventListener("beforeunload", function (e) {

            if (formSubmitting || !formIsDirty) {
                return undefined;
            }
            var confirmationMessage = 'It looks like you have been editing something. '

                + 'If you leave before saving, your changes will be lost.';

            (e || window.event).returnValue = confirmationMessage; //Gecko + IE

            return confirmationMessage; //Gecko + Webkit, Safari, Chrome etc.

        });

    };

})(jQuery);

var formSubmitting = false;
var formIsDirty = false;

function setFormSubmitting() {
    formSubmitting = true;
}