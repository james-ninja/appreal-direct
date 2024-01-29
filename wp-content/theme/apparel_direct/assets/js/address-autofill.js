jQuery("body").on('click', '.form_option_billing, .form_option_edit', function () {
    
    setTimeout(function(){initValuationMap();
        jQuery('.ocwma_woocommerce-address-fields #billing_address_1').keypress(function() {
            jQuery('.pac-container').css('z-index', '99999999999');
        });
        if (jQuery('.woocommerce-edit-address #billing_state').length > 0) {
            jQuery('#billing_state').select2('destroy');
        }
    }, 2000);
    var modal = document.getElementById("ocwma_billing_popup");
	window.onclick = function(event) {
	  if (event.target == modal) {
		modal.style.display = "block";
	  }
}
});

jQuery("body").on('click', '.form_option_shipping, .form_option_ship_edit', function () {
    //alert('test');
    setTimeout(function(){initValuationMap();
        jQuery('.ocwma_woocommerce-address-fields #shipping_address_1').keypress(function() {
            jQuery('.pac-container').css('z-index', '99999999999');
        });
        if (jQuery('.woocommerce-edit-address #shipping_state').length > 0) {
            jQuery('#shipping_state').select2('destroy');
        }
    }, 2000);
    var modal = document.getElementById("ocwma_billing_popup");
	window.onclick = function(event) {
	  if (event.target == modal) {
		modal.style.display = "block";
	  }
}
});

//Autocomplete Address

let autocomplete;
let homeautocomplete;
let billing_address_1;
let address_autofill;
let home_address_1;
let address2Field;
let postalField;

function initValuationMap() {

    address_autofill = document.querySelector('.ocwma_woocommerce-address-fields #billing_address_1');
    address_autofill_ship = document.querySelector('.ocwma_woocommerce-address-fields #shipping_address_1');
    billing_address_1 = document.querySelector("#billing_address_1");
    home_address_1 = document.querySelector("#home_address_1");

    postalField = document.querySelector("#billing_postcode");

    autocomplete = new google.maps.places.Autocomplete(billing_address_1, {
        componentRestrictions: { country: ["us"] },
        fields: ["address_components", "geometry"],
        types: ["address"],
    });
    homeautocomplete = new google.maps.places.Autocomplete(home_address_1, {
        componentRestrictions: { country: ["us"] },
        fields: ["address_components", "geometry"],
        types: ["address"],
    });
    popupautocomplete = new google.maps.places.Autocomplete(address_autofill, {
        componentRestrictions: { country: ["us"] },
        fields: ["address_components", "geometry"],
        types: ["address"],
    });
    popupautocomplete_ship = new google.maps.places.Autocomplete(address_autofill_ship, {
        componentRestrictions: { country: ["us"] },
        fields: ["address_components", "geometry"],
        types: ["address"],
    });

    if (billing_address_1) {
        autocomplete.addListener("place_changed", fillInAddress);
    }

    if (home_address_1) {
        homeautocomplete.addListener("place_changed", aboutfillInAddress);
    }
    if (address_autofill) {
        popupautocomplete.addListener("place_changed", popupfillInAddress);
    }
    if (address_autofill_ship) {
        popupautocomplete_ship.addListener("place_changed", popupfillInAddressShip);
    }
}

function popupfillInAddress() {
    const place = popupautocomplete.getPlace();
    let address1 = "";
    let postcode = "";

    for (const component of place.address_components) {
        const componentType = component.types[0];
        switch (componentType) {
            case "street_number": {
                address1 = `${component.long_name} ${address1}`;
                break;
            }
            case "route": {
                address1 += component.short_name;
                break;
            }
            case "postal_code": {
                postcode = `${component.long_name}${postcode}`;
                jQuery(".ocwma_woocommerce-address-fields #billing_postcode").val(postcode);
                break;
            }
            case "sublocality_level_1": {
                jQuery(".ocwma_woocommerce-address-fields #billing_city").val(component.long_name);
                break;
            }
            case "locality":
                jQuery(".ocwma_woocommerce-address-fields #billing_city").val(component.long_name);
                break;

            case "administrative_area_level_1": {
                jQuery(".ocwma_woocommerce-address-fields #billing_state").val(component.short_name);
                break;
            }
        }
    }
    address_autofill.value = address1;
    //postalField.value = postcode;
}

function popupfillInAddressShip() {
    const place = popupautocomplete_ship.getPlace();
    let address1 = "";
    let postcode = "";

    for (const component of place.address_components) {
        const componentType = component.types[0];
        switch (componentType) {
            case "street_number": {
                address1 = `${component.long_name} ${address1}`;
                break;
            }
            case "route": {
                address1 += component.short_name;
                break;
            }
            case "postal_code": {
                postcode = `${component.long_name}${postcode}`;
                jQuery(".ocwma_woocommerce-address-fields #shipping_postcode").val(postcode);
                break;
            }
            case "sublocality_level_1": {
                jQuery(".ocwma_woocommerce-address-fields #shipping_city").val(component.long_name);
                break;
            }
            case "locality":
                jQuery(".ocwma_woocommerce-address-fields #shipping_city").val(component.long_name);
                break;

            case "administrative_area_level_1": {
                jQuery(".ocwma_woocommerce-address-fields #shipping_state").val(component.short_name);
                break;
            }
        }
    }
    address_autofill_ship.value = address1;
}

function fillInAddress() {
    // Get the place details from the autocomplete object.
    const place = autocomplete.getPlace();
    let address1 = "";
    let postcode = "";

    for (const component of place.address_components) {
        const componentType = component.types[0];

        switch (componentType) {
            case "street_number": {
                address1 = `${component.long_name} ${address1}`;
                break;
            }
            case "route": {
                address1 += component.short_name;
                break;
            }
            case "postal_code": {
                postcode = `${component.long_name}${postcode}`;
                break;
            }
            case "sublocality_level_1": {
                document.querySelector("#billing_city").value = component.long_name;
                break;
            }
            case "locality":
                 document.querySelector("#billing_city").value = component.long_name;
                break;

            case "administrative_area_level_1": {
                document.querySelector("#billing_state").value = component.short_name;
                break;
            }
        }
    }
    billing_address_1.value = address1;
    postalField.value = postcode;
}

function aboutfillInAddress() {
    // Get the place details from the autocomplete object.
    const place = homeautocomplete.getPlace();
    let address1 = "";
    let postcode = "";

    for (const component of place.address_components) {
        const componentType = component.types[0];

        switch (componentType) {
            case "street_number": {
                address1 = `${component.long_name} ${address1}`;
                break;
            }
            case "route": {
                address1 += component.short_name;
                break;
            }
            case "postal_code": {
                postcode = `${component.long_name}${postcode}`;
                document.querySelector("#home_postcode").value = postcode;
                break;
            }
            case "sublocality_level_1": {
                document.querySelector("#home_city").value = component.long_name;
                break;
            }
            case "locality":
                document.querySelector("#home_city").value = component.long_name;
                //console.log(component);
                break;

            case "administrative_area_level_1": {
                document.querySelector("#home_state").value = component.short_name;
                break;
            }
        }
    }
    home_address_1.value = address1;
}
//Autocomplete Address End