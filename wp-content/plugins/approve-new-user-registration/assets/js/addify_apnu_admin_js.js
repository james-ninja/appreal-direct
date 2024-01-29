jQuery( function($) {


	//On Change
	$('#addify_apnu_enable_admin_email_notification').change(function () {
		if (this.checked) { 
			//  ^
			$('.apnu_enable_admin_email').fadeIn('fast');
			$(".apnu_enable_admin_email").parents("tr").show();
		} else {
			$('.apnu_enable_admin_email').fadeOut('fast');
			$(".apnu_enable_admin_email").parents("tr").hide();
		}
	});

	//On Load
	if ($("#addify_apnu_enable_admin_email_notification").is(':checked')) {
		$(".apnu_enable_admin_email").show();  // checked
		$(".apnu_enable_admin_email").parents("tr").show();
	} else {
		$(".apnu_enable_admin_email").hide();
		$(".apnu_enable_admin_email").parents("tr").hide();
	}




	//On Change
	$('#addify_apnu_enable_pending_email_notification').change(function () {
		if (this.checked) { 
			//  ^
			$('.apnu_enable_pending_email').fadeIn('fast');
			$(".apnu_enable_pending_email").parents("tr").show();
		} else {
			$('.apnu_enable_pending_email').fadeOut('fast');
			$(".apnu_enable_pending_email").parents("tr").hide();
		}
	});

	//On Load
	if ($("#addify_apnu_enable_pending_email_notification").is(':checked')) {
		$(".apnu_enable_pending_email").show();  // checked
		$(".apnu_enable_pending_email").parents("tr").show();
	} else {
		$(".apnu_enable_pending_email").hide();
		$(".apnu_enable_pending_email").parents("tr").hide();
	}




	//On Change
	$('#addify_apnu_enable_approved_email_notification').change(function () {
		if (this.checked) { 
			//  ^
			$('.apnu_enable_approved_email').fadeIn('fast');
			$(".apnu_enable_approved_email").parents("tr").show();
		} else {
			$('.apnu_enable_approved_email').fadeOut('fast');
			$(".apnu_enable_approved_email").parents("tr").hide();
		}
	});

	//On Load
	if ($("#addify_apnu_enable_approved_email_notification").is(':checked')) {
		$(".apnu_enable_approved_email").show();  // checked
		$(".apnu_enable_approved_email").parents("tr").show();
	} else {
		$(".apnu_enable_approved_email").hide();
		$(".apnu_enable_approved_email").parents("tr").hide();
	}



	//On Change
	$('#addify_apnu_enable_disapproved_email_notification').change(function () {
		if (this.checked) { 
			//  ^
			$('.apnu_enable_disapproved_email').fadeIn('fast');
			$(".apnu_enable_disapproved_email").parents("tr").show();
		} else {
			$('.apnu_enable_disapproved_email').fadeOut('fast');
			$(".apnu_enable_disapproved_email").parents("tr").hide();
		}
	});

	//On Load
	if ($("#addify_apnu_enable_disapproved_email_notification").is(':checked')) {
		$(".apnu_enable_disapproved_email").show();  // checked
		$(".apnu_enable_disapproved_email").parents("tr").show();
	} else {
		$(".apnu_enable_disapproved_email").hide();
		$(".apnu_enable_disapproved_email").parents("tr").hide();
	}

});
