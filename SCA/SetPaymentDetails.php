<?php
    session_start();
    require_once "../config.php";
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css/sample.css">
    </head>

    <body>

        <div class="container">

            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <a class="navbar-brand start-over" href="#">Amazon Pay PHP SDK Sample: One-Time Payment Checkout (SCA)</a>
                    </div>
                    <div id="navbar" class="navbar-collapse collapse">
                        <ul class="nav navbar-nav navbar-right">
                            <li><a class="start-over" href="#">Logout and Start Over</a></li>
                        </ul>
                    </div>
                </div>
            </nav>

            <b>Order ID:</b><div id="divOrderId"></div><br>

            <div class="jumbotron jumbotroncolor" style="padding-top:25px;" id="api-content">
                <div id="section-content">
                        <center><div id="addressBookWidgetDiv" style="width:400px; height:240px; display:inline-block;"></div>
                        <div id="walletWidgetDiv" style="width:400px; height:240px; display:inline-block;"></div>
                        <p><p><button id="place-order" class="btn btn-lg btn-success" disabled>Place Order</button>
                            <div id="ajax-loader" style="display:none;"><img src="images/ajax-loader.gif" /></div>
                        </center>
                </div>
            </div>

            <p>This is the live response from the previous API call.</p>
            <pre id="get_details_response">
                <div class="text-center"><img src="images/ajax-loader.gif" /></div>
            </pre>

        </div>

        <script type="text/javascript" src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
        <script type='text/javascript'>
            var orderId;
            var access_token = '<?php print $_GET["access_token"];?>';

            window.onAmazonLoginReady = function () {
                try {
                    amazon.Login.setClientId('<?php print $amazonpay_config['client_id']; ?>');
                    //amazon.Login.setUseCookie(true);
                } catch (err) {
                    alert(err);
                }
            };

            window.onAmazonPaymentsReady = function () {
                new OffAmazonPayments.Widgets.AddressBook({
                    sellerId: "<?php echo $amazonpay_config['merchant_id']; ?>",
                    onOrderReferenceCreate: function (orderReference) {

                        /* Make a call to the back-end that will SetOrderReferenceDetails
                         * and GetOrderReferenceDetails. This will set the order total
                         * to 19.95 and return order reference details.
                         */

                        orderId = orderReference.getAmazonOrderReferenceId();
                        $("#divOrderId").html(orderId);

                        $.post("Apicalls/GetDetails.php", {
                            orderReferenceId: orderId,
                            accessToken: access_token
                        }).done(function (data) {
                            try {
                                JSON.parse(data);
                            } catch (err) {
                            }
                            $("#get_details_response").html(data);
                        });

                    },
                    onAddressSelect: function (orderReference) {
                        console.log('on AddressSelect callback');
                        // If you want to prohibit shipping to certain countries, this is where you would handle that

                        $.post("Apicalls/GetDetails.php", {
                            orderReferenceId: orderId,
                            accessToken: access_token
                        }).done(function (data) {
                            try {
                                JSON.parse(data);
                            } catch (err) {
                            }
                            $("#get_details_response").html(data);
                            $('#place-order').prop('disabled', false); // we can enable button once order ID is put into the session by the back-end call
                        });

                    },
                    design: {
                        designMode: 'responsive'
                    },
                    displayMode:"Edit",
                    onError: function (error) {
                        // your error handling code
                        alert("AddressBook Widget error: " + error.getErrorCode() + ' - ' + error.getErrorMessage());
                    }
                }).bind("addressBookWidgetDiv");


                new OffAmazonPayments.Widgets.Wallet({
                    sellerId: "<?php echo $amazonpay_config['merchant_id']; ?>",
                    onPaymentSelect: function (orderReference) {

                        console.log('onPaymentSelect callback');

                        $.post("Apicalls/GetDetails.php", {
                            orderReferenceId: orderId,
                            accessToken: access_token
                        }).done(function (data) {
                            try {
                                JSON.parse(data);
                            } catch (err) {
                            }
                            $("#get_details_response").html(data);
                        });

                    },
                    design: {
                        designMode: 'responsive'
                    },
                    displayMode:"Edit",
                    onError: function (error) {
                        // your error handling code
                        alert("Wallet Widget error: " + error.getErrorCode() + ' - ' + error.getErrorMessage());
                    }
                }).bind("walletWidgetDiv");


function placeOrder(confirmationFlow) {
    console.log("in placeOrder top");
    $.ajax({
        url: "Apicalls/Confirm.php?id=" + orderId,
        success: function (data) {
            confirmationFlow.success(); // continue Amazon Pay hosted site
        },
        error: function (data) { // called on ajax error and timeout
            confirmationFlow.error(); // abort Amazon Pay initConfirmationFlow
            // you might want to add additional error handling
        },
        timeout: "3000" //specify your timeout value (for example, 3000)
        //If the ajax request takes longer than this timeout (in ms), the error callback
        //will be called
    });
    console.log("in placeOrder bottom");
}


                $(document).ready(function() {
                    $('.start-over').on('click', function() {
                        amazon.Login.logout();
                        document.cookie = "amazon_Login_accessToken=; expires=Thu, 01 Jan 1970 00:00:00 GMT";
                        window.location = 'index.php';
                    });
                    $('#place-order').on('click', function() {
                        $(this).hide();
                        $('#ajax-loader').show();

                        OffAmazonPayments.initConfirmationFlow("<?php echo $amazonpay_config['merchant_id']; ?>", orderId, function(confirmationFlow) {
                            placeOrder(confirmationFlow);
                        });

                    });
                });

            };

        </script>
        <script async="async" type='text/javascript' src="<?php echo getWidgetsJsURL($amazonpay_config); ?>"></script>
    </body>
</html>
