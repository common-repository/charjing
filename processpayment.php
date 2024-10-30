<?php
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    $charjing_setting=get_option("charjing");

    include(dirname(__FILE__)."/payments/authorize/AuthorizeNet.php");
    $transaction = new AuthorizeNetAIM($charjing_setting["loginid"],$charjing_setting["txn_key"]);
    if($charjing_setting["sandbox"]=="1")
        $transaction->setSandbox(true);
    else
        $transaction->setSandbox(false);
    
    
    $product_id=(int)$_REQUEST["product_id"];
    $charjing_info=  get_post_meta($product_id,"charjing_info",true);
    $product=get_post($product_id);
    
    $firstpayment=$charjing_info["setup_price"]+$charjing_info["price"];            
    $recurring=$charjing_info["price"];
    $recurring_start=date("Y-m-d",strtotime("1 ".$charjing_info["period_option"]));
    
    $x_card_num=sanitize_text_field($_POST['x_card_num']);
    $x_exp_month=sanitize_text_field($_POST['x_exp_month']);
    $x_exp_date=sanitize_text_field($_POST['x_exp_date']);
    $x_first_name=sanitize_text_field($_POST['x_first_name']);
    $x_last_name=sanitize_text_field($_POST['x_last_name']);
    $x_address=sanitize_text_field($_POST['x_address']);
    $x_city=sanitize_text_field($_POST['x_city']);
    $x_state=sanitize_text_field($_POST['x_state']);
    $x_country=sanitize_text_field($_POST['x_country']);
    $x_zip=sanitize_text_field($_POST['x_zip']);
    $x_email=sanitize_text_field($_POST['x_email']);
    $x_card_code=sanitize_text_field($_POST['x_card_code']);

    
    $transaction->VERIFY_PEER=false;
    $transaction->setFields(
        array(
        'amount' => $firstpayment, 
        'card_num' => $x_card_num, 
        'exp_date' => $x_exp_month."/".$x_exp_date,
        'first_name' => $x_first_name,
        'last_name' => $x_last_name,
        'address' => $x_address,
        'city' => $x_city,
        'state' => $x_state,
        'country' => $x_country,
        'zip' => $x_zip,
        'email' => $x_email,
        'card_code' => $x_card_code,
        )
    );

    $response = $transaction->authorizeAndCapture();
    if($response->approved) 
    {
        $subscription                          = new AuthorizeNet_Subscription;
        $subscription->name                    = esc_html($product->post_title);
        $subscription->intervalLength          = "1";
        $subscription->intervalUnit            = strtolower($charjing_info["period_option"]);
        $subscription->startDate               = $recurring_start;
        $subscription->totalOccurrences        = "9999";
        $subscription->amount                  = $recurring;
        $subscription->creditCardCardNumber    = $x_card_num;
        $subscription->creditCardExpirationDate=$x_exp_month."/".$x_exp_date;
        $subscription->creditCardCardCode      = $x_card_code;
        $subscription->billToFirstName         = $x_first_name;
        $subscription->billToLastName          = $x_last_name;

        $subscription->customerEmail          = $x_email;
        $subscription->billToAddress          = $x_address;
        $subscription->billToCity          = $x_city;
        $subscription->billToState          = $x_state;
        $subscription->billToZip          = $x_zip;
        $subscription->billToCountry          = $x_country;

        // Create the subscription.
        $request = new AuthorizeNetARB($charjing_setting["loginid"],$charjing_setting["txn_key"]);
        if($charjing_setting["sandbox"]=="1")
            $request->setSandbox(true);
        else
            $request->setSandbox(false);
        $request->VERIFY_PEER=false;
        $response2 = $request->createSubscription($subscription);
        $subscription_id = $response2->getSubscriptionId();
        wp_redirect(get_permalink($charjing_setting["thanks"]));
        die("Loading...");
        
    } else {
        $GLOBALS["payment_error"]=$response->response_reason_text;
    }

    
?>