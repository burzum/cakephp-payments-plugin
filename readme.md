# CakePHP Payments Plugin #

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

 * Sofort.de http://github.com/burzum/Sofort

### List of Commercial Payment Processors using this API

Contact us to get your plugin reviewed and added to this list if it matches the acceptance criteria. A good processor has proper value validation, error handling and logging.

There are no commercial processors available yet.

## Implementing your processor based on this API

All of the following steps are considered as required to write a proper and as good as possible fail save and easy to use payment processor:

* Your processer has to extend BasePaymentProcessor and use the interfaces as needed
* You have to use set() to set values for the API / processor and validateValues() to check if all required values for a call are present
* You have to use the Exceptions from the Payments plugin to encapsulate payment gateway API errors and payment processor issues
* You have to map the payment statuses from the foreign APIs to the constants of the PaymentStatus class and return them instead the foreign statuses
* Your processor should not have hard dependencies on anything else if possible
* Use the PaymentApiLog to log payment related messages

Contact us to get your processor reviewed and added to the processor list if it matches the acceptance criterias.

### Recommended field names

To make it easier for everyone to use different processors without the need to map the fields of the app to all the processors in a different way the following field names are recommended. If you want to get added to the processor list above you'll have to follow the conventions.

Not all of these fields are required by each processor. If they match what you need use them. Do not use other names!

Generic fields:

* amount
* reason
* reason2
* currency
* vat
* payment_reference
* subscription_reference
* sender_email
* sender_first_name
* sender_last_name
* receiver_email
* receiver_first_name
* receiver_last_name
* receiver_street
* receiver_street2
* receiver_zip
* receiver_country
* receiver_iban - Bank account number
* receiver_bic - Bank id
* receiver_account_id - Can be used for payment systems using something else than email or iban/bic

For Credit Card processors

* card_number
* card_code
* card_holder
* card_expiration_date - Format: (MM-YYYY)

For recurring payments

* recurring_start_data
* recurring_end_date
* recurring_interval
* recurring_frequency
* recurring_occurence
* recurring_trial_occurence

### cURL Wrapper

This plugin also comes with a Curl class to wrap the native php cURL functionality in object oriented fashion.

Please use it instead of any other 3rd party libs in your processors.

## Support

For support and feature request, please visit the Payments issue page

https://github.com/burzum/Payments/issues

## License

Copyright 2012, Florian Krämer

Licensed under The MIT License
Redistributions of files must retain the above copyright notice.