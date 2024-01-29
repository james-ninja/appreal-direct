# wc-usaepay
WooCommerce USAePay Payment Gateway

# Changelog
= 2.5.3 (06 May 2021) =
* UPDATE: Update use strict for jquery script

= 2.4.3 (05 May 2021) =
* UPDATE: Update code prefix and escape

= 2.3.3 (26 April 2021) =
* FIXED: WP Dashboard error

= 2.3.2 (03 April 2021) =
* UPDATE: Add Default order status option in plugin settings

= 2.2.2 (28 Feb 2021) =
* FIXED: Fix add payment method bug

= 2.2.1 (12 Feb 2021) =
* UPDATE: UMip params

= 2.1.1 (03 Jan 2021) =
* FIXED: rollback the old version refund

= 2.1.0 (29 Dec 2020) =
* UPDATE: auto charge when then the order status is completed

= 2.0.0 (10 Dec 2020) =
* UPDATE: Removed old version
* FIXED: Refund

= 1.18.11 (5 Nov 2020) =
* UPDATE: Transaction API override subtotal
* UPDATE: Transaction API  $1 authorization
* UPDATE: Transaction API  re-auth+capture
* UPDATE: USAepay Library


= 1.17.10 (3 May 2020) =
* UPDATE: Transaction API UMisRecurring included in request api

= 1.16.10 (17 Mar 2020) =
* UPDATE: WC 4.0.0 tested


= 1.15.10 (23 Feb 2020) =
* UPDATE: Transaction API Cardholder name
* UPDATE: Transaction API Order Description


= 1.14.10 (28 Jan 2020) =
* FIXED: Fix Bug save token 
* FIXED: cc:save to cc:sale for UMsaveCard

= 1.14.9 (29 Nov 19) =
* UPDATE: Transaction API Add payment method in my account
* UPDATE: Transaction API change payment method in for subscription order
* FIXED: Transaction API create subscription order using existing token

= 1.13.8 (30 Oct 19) =
* NEW: Transaction API Integration 
* UPDATE: USAePay Two way integration 
* UPDATE: Bring back old stable version 1.10.6 USAePay PHP Library integration 
* REMOVE: REST API Integration.  

= 1.12.7 (28 Sept 19) =
* UPDATE: rest api code enable multi-currency functionality 

= 1.11.7 (25 July 19) =
* UPDATE: payment remote call to REST API
* UPDATE: Admin Settings key inputs to password type
* FIXED: PHP7 error.
* TESTED: Wordpress Version up to 5.2.2
* TESTED: WooCommerce Version up to 3.6.5
* TESTED: PHP 7 Version up to 7.2.20
* TESTED: MySQL Version up to 5.6.41-84.1
* TESTED: Wordpress Multisite

= 1.10.6 (24 Dec 18) =
* UPDATE: remove and update all deprecated functions and calls.
* FIXED: fixed card validation in subscription.
* FIXED: fixed cvv2 in subscription.
* UPDATE: Add Extra links online documentation and support
* TESTED: Wordpress up to 5.0.2
* TESTED: WooCoommerce up to 3.5.3

= 1.9.6 (21 Aug 18) =
* FIXED: preg_replace /e modifier error. Use preg_replace_callback() instead.
* UPDATE: Subscription/Recurring process capture the AuthOnly Transaction.
* TESTED: Wordpress up to 4.9.8
* TESTED: WooCoommerce up to 3.4.4

= 1.8.5 (05 july 18) =
* New: Add New metabox to process and capture the AuthOnly Transaction
* UPDATE: Remove Capture an AuthOnly Transaction Command in the settings
* UPDATE: implement new refund order connecting post meta key _usaepay_refnum
* UPDATE: Expired purchase code notice dismiss update

= 1.7.5 (02 dec 17) =
* New: Multisite Supported 
* New: Add New settings CodeCanyon Purchase Code Activation.
* UPDATE: Options to set Accepted Card Logos
* FIXED: Refund
* FIXED: credit_card_form is deprecated since version 2.6
* FIXED: Pay for order customer payment page

= 1.6.4 (02 may 17) =
* UPDATE: Multi-Currency Implemented.

= 1.5.4 (28 march 17) =
* FIXED: Bugs for Stock Reduction Issue.
* FIXED: Existing CCSale Error ( Standard Checkout).
* FIXED: Existing CCSale Error ( Subscription/Recurring Checkout).

= 1.5.3 (14 march 17) =
* FIXED: Bugs for not existing customer checkout.

= 1.5.2 (25 feb 17) =
* UPDATE: Shipping address included to usaepay.
* UPDATE: Checkout reference order ID change to Order Number.

= 1.4.2 (16 feb 17) =
* UPDATE: Auto Detect if order is virtual. the order status is automatically complete after payment. 
* UPDATE: Removed Order Status Setting 

= 1.3.2 (23 December 16) =
* UPDATE: Subscription/Recurring standard one-time payments Removed.
* UPDATE: Subscription/Recurring Re-Code for Tokenization Implemented.
* UPDATE: Auto Detect Subscription/Recurring Products Implemented.
* UPDATE: Credit Card Payment Tokenization for customer future payments Implemented.
* UPDATE: Refund Payment Implemented.
* UPDATE: Option to set enable test mode Implemented.
* UPDATE: Option to set enable name on card Implemented.
* UPDATE: Option to set enable save card Implemented.

= 1.2.2 (29 march 16) =

* UPDATE: Source PIN Settings Implemented.
* UPDATE: Log Events Request and Response for debugging Implemented.

= 1.1.2 (29 june 14) =

* FIXED: Fatal error: Class ‘WC_Subscriptions_Order’ not found has been fixed.
* UPDATE: New Settings Implemented Enable/Disable Subscriptions and Recurring Payment.

= 1.1.1 (25 june 14) =

* UPDATE: Support Subscription and Recurring Payments ( Requires: WooCommerce Subscriptions plugin 1.4.5 or higher )
* UPDATE: New settings implemented option to select ( processing or completed ) auto update/changed the order status after payment complete.
* UPDATE: New settings implemented option to select Transaction Processing Command types.
* UPDATE: New settings implemented option to set Enable/Disable USAePay Accepted Card Logos.
* UPDATE: New settings implemented option to set send customer recurring billing receipt.

= 1.0.1 (12 june 14) =

* FIXED: "Fatal error: Can't use method return value in write context" has been fixed for WooCommerce version 2.1.11 .

= 1.0 (9 june 14) =

* Initial release version
