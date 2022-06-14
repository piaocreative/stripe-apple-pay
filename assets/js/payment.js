var publishableKey = document.querySelector('#payment-box').dataset.pk;
var returnURL = document.querySelector('#payment-box').dataset.returnUrl;
var unitPrice = document.querySelector('#unit-price').value;
unitPrice = Math.round((unitPrice * 100));
var productLabel = document.querySelector('#product-label').value;
var currency = document.querySelector('#currency').value;
var shippingAmount = document.querySelector('#shipping-amount').value;
shippingAmount = Math.round((shippingAmount * 100));

var stripe = Stripe(publishableKey, {
	apiVersion: "2020-08-27",
});
var paymentRequest = stripe.paymentRequest({
	country: 'US',
	currency: currency,
	total: {
		label: productLabel,
		amount: unitPrice,
	},
	requestPayerName: true,
	requestPayerEmail: true,
	requestShipping: true,
	shippingOptions: [
		{
			id: 'Default Shipping',
			label: 'Default Shipping',
			detail: '',
			amount: shippingAmount,
		},
	],
});

var elements = stripe.elements();
var prButton = elements.create('paymentRequestButton', {
	paymentRequest: paymentRequest,
});

// Verify payment parameters with the the Payment Request API.
paymentRequest.canMakePayment().then(function(result) {
	if (result) {
		prButton.mount('#payment-request-button');
	} else {
		document.getElementById('payment-request-button').style.display = 'none';
	}
});

paymentRequest.on('paymentmethod', function(ev) {
	//Create Stripe payment intent
	var requestParam = {
		email: ev.payerEmail,
		unitPrice: unitPrice,
		currency: currency,
		name: ev.payerName,
		address: ev.shippingAddress.addressLine[0],
		country: ev.shippingAddress.country,
		postalCode: ev.shippingAddress.postalCode,
		shippingPrice: ev.shippingOption.amount,
	};
	var createOrderUrl = "ajax-endpoint/create-stripe-order.php";
	fetch(createOrderUrl, {
		method: "POST",
		headers: {
			"Content-Type": "application/json"
		},
		body: JSON.stringify(requestParam)
	}).then(function(result) {
		return result.json();
	}).then(function(data) {
		// Script to confirm payment 
		stripe.confirmCardPayment(
			data.clientSecret,
			{ payment_method: ev.paymentMethod.id },
			{ handleActions: false }
		).then(function(confirmResult) {
			if (confirmResult.error) {
				// Report to the browser that the payment failed, prompting it to
				// re-show the payment interface, or show an error message and close
				// the payment interface.
				ev.complete('fail');
			} else {
				// Report to the browser that the confirmation was successful, prompting
				// it to close the browser payment method collection interface.
				ev.complete('success');
				// Check if the PaymentIntent requires any actions and if so let Stripe.js
				// handle the flow. If using an API version older than "2019-02-11" instead
				// instead check for: `paymentIntent.status === "requires_source_action"`.
				if (confirmResult.paymentIntent.status === "requires_action") {
					// Let Stripe.js handle the rest of the payment flow.
					stripe.confirmCardPayment(clientSecret).then(function(result) {
						if (result.error) {
							// The payment failed -- ask your customer for a new payment method.
						} else {
							// The payment has succeeded.
							window.location.replace(returnURL + "?orderno=" + data.orderHash);
						}
					});
				} else {
					// The payment has succeeded.
					window.location.replace(returnURL + "?orderno=" + data.orderHash);
				}
			}
		});
	});
});

paymentRequest.on('shippingaddresschange', function(ev) {
	// Perform server-side request to fetch shipping options
	fetch('ajax-endpoint/calculate-product-shipping.php', {
		method: "POST",
		headers: {
			"Content-Type": "application/json"
		},
		body: JSON.stringify({
			adress: ev.shippingAddress
		})
	}).then(function(response) {
		return response.json();
	}).then(function(result) {
		ev.updateWith({
			status: 'success',
			shippingOptions: result.shippingOptions,
		});
	});
});