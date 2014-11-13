# CakePHP Payments Plugin #

**Discontinued**. Go for another lib like https://github.com/thephpleague/omnipay. I would prefer the architecture of this  CakePHP plugin but maintaining this code alone is far beyond my available free time.

---

http://github.com/burzum/Payments

This plugin is thought to provide a generic API for different payment processors.

The idea is to wrap every payment processor into the same interface so that it becomes immediately usable by every developer who is familiar with this API, removing the need to learn all the different payment providers and their specific APIs.

The processors should not depend on anything else than this API and be useable within any application, even shell scripts.

It might become possible that there will be an independent non CakePHP version in the future. It is already built with a less as possible CakePHP dependencies to make this possible without a lot work.

Contributions are welcome!

## Requirements

 * CakePHP 2.x

This plugin is just an API and set of interfaces it does not contain any processors, you'll have to pick and add processor for your app.

### List of Open Source Payment Processors using this API

Please create a ticket on github or send an email if you want to get your processor on this list.

 * Sofort.de (LGPL License) - http://github.com/burzum/Sofort

### List of Commercial Payment Processors using this API

Contact us to get your plugin reviewed and added to this list if it matches the acceptance criteria. A good processor has proper value validation, error handling and logging.

There are no commercial processors available yet.

## Implementing your processor based on this API

All of the following steps are considered as required to write a proper and as good as possible fail save and easy to use payment processor:

* Your processor has to extend BasePaymentProcessor and use the interfaces as needed
* Your processor must not have any application specific or dependant code in it
* You have to use set() to set values for the API / processor and validateValues() to check if all required values for a call are present
* You have to use the Exceptions from the Payments plugin to encapsulate payment gateway API errors and payment processor issues
* You have to map the payment statuses from the foreign APIs to the constants of the PaymentStatus class and return them instead the foreign statuses
* Your processor should not have hard dependencies on anything else if possible
* Use the PaymentApiLog to log payment related messages

Contact us to get your processor reviewed and added to the processor list if it matches the acceptance criterias.

### Configuration of Processors

All Payment processors must follow this convention for configuration data

	'SomePaymentProcessor' => array(
		'sandboxMode' => false,
		'live' => array(
			'apiKey' => '11223:123456:h25lh252525hlhadslgh2362l6h2lsfg'
			'apiId' => '151611574',
			'...' => '...'
		),
		'sandbox' => array(
			'apiKey' => '33221:652141:kl262lhsdgh15dslhgslhj325lhdsglsd'
			'apiId' => '623512526',
			'...' => '...'
		),
	),

sandboxMode mode and live are required, sandbox also if a sandbox configuration is available. sandboxMode can be true or false to switch between live and sandbox configuration.

### Sandbox mode

You'll have to call YourPaymentProcessor::sandboxMode(true) or YourPaymentProcessor::sandboxMode(false) to set a payment processor into sandbox mode. This is important to toggle between live and sandbox settings and special testing variables and URLs most sandboxes require. To get the current state of a processor just call YourPaymentProcessor::sandboxMode() without passing an argument.

### Recommended field names

To make it easier for everyone to use different processors without the need to map the fields of the app to all the processors in a different way the following field names are recommended. If you want to get added to the processor list above you'll have to follow the conventions.

Not all of these fields are required by each processor. If they match what you need use them. Do not use other names!

#### Generic fields:

* amount
* currency
* vat
* payment_reason
* payment_reason2
* payment_reference - Mixed string|integer
* customer_email
* customer_first_name
* customer_last_name
* customer_email
* customer_first_name
* customer_last_name
* customer_phone
* customer_street
* customer_address
* customer_address2
* customer_zip
* customer_country
* customer_state
* customer_description - String
* customer_iban - Integer, Bank account number
* customer_bic - Integer, Bank id
* customer_account_id - Can be used for payment systems using something else than email or iban/bic
* billing_address
* billing_address2
* billing_zip
* billing_city
* billing_country
* billing_state

#### For Credit Card processors

* card_number Integer
* card_code Integer, length 3 to 4
* card_holder String
* card_month - Integer, Format: 01, 02,..., 10, 12. Expiration date month
* card_year - Integer, Format: YYYY, Processor should parse that to whatever it needs

#### For recurring payments

* subscription_reference - Mixed string|integer
* recurring_trial_amount - Float
* recurring_start_data - String, Format: YYYY-MM-DD, Processor should parse that to whatever it needs
* recurring_end_date - String, Format: YYYY-MM-DD, Processor should parse that to whatever it needs
* recurring_interval - String, day, month, year, dayOfMonth, dayOfWeek, dayOfYear (singular)
* recurring_frequency - Integer
* recurring_occurence - Integer, number of times a payment will recur
* recurring_trial_occurence - Integer

recurring_interval and recurring_frequency together will allow you to define virtually any billing cycle. The processor muste translate the passed values to whatever the implemented API requires.

#### Custom fields:

* custom1 - Mixed
* custom2 - Mixed
* custom3 - Mixed
* ...

### cURL Wrapper

This plugin also comes with a Curl class to wrap the native php cURL functionality in object oriented fashion.

Please use it instead of any other 3rd party libs in your processors.

## Example of working with a processor

## Usage

First get an instance of your payment processor and pass configuration

	$config = array('apiKey' => 'YOU-API-KEY');
	$Processor = new YourPaymentProcessor($Config);

Note that different processors might require different fields. But

	$Processor->set('payment_reason', 'Order 123'); // required
	$Processor->set('payment_reason2', 'Something here'); // optional

Call the pay method.

	$Processor->pay(15.99);

## Support

For support and feature request, please visit the Payments issue page

https://github.com/burzum/Payments/issues

## License

Copyright 2012, Florian Krämer

Licensed under The MIT License
Redistributions of files must retain the above copyright notice.
