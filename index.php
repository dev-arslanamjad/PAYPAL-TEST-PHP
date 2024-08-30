<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayPal Express Checkout</title>
</head>
<body>

<!-- PayPal Button Container -->
<div id="paypal-button-container"></div>
<script src="https://www.paypal.com/sdk/js?client-id=AXHQoXDXD7wkgthihmLQ5l0q11CLmXtGHwWn3GIDeETJNuhTtGYEK9jHS5MGYRIrKoqlX22wrPx4Ek6y&currency=USD"></script>

<script>
    paypal.Buttons({
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: '7.01' // Adjust this amount dynamically if needed
                    }
                }]
            });
        },
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                // Log transaction details
                console.log('Transaction details:', details);

                // Send transaction details to the server
                fetch('http://localhost/paypal_express/save_transaction.php', { // Use HTTPS if available
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        orderID: data.orderID,
                        payerID: data.payerID,
                        paymentID: data.paymentID,
                        details: details
                    })
                })
                .then(response => {
                    console.log('Server response:', response);
                    return response.json();
                })
                .then(result => {
                    if (result.success) {
                        console.log('Redirecting to success page');
                        window.location.href = `http://localhost/paypal_express/success.php?orderID=${data.orderID}&payerID=${data.payerID}&paymentID=${data.paymentID}&details=${encodeURIComponent(JSON.stringify(details))}`;
                    } else {
                        alert('Failed to save transaction details.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while saving transaction details.');
                });
            });
        },
        onError: function(err) {
            console.error('PayPal Checkout Error:', err);
            alert('An error occurred during the PayPal transaction.');
        }
    }).render('#paypal-button-container');
</script>

</body>
</html>
