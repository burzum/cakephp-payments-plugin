# CakePHP Payments Plugin #

http://github.com/burzum/Payments

This plugin is thought to provide a generic API for different payment processors.

The idea is to wrap every payment processor into the same interface so that it becomes immediately usable by every developer who is familiar with this API, removing the need to learn all the different payment providers and their specific APIs.

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

All of the following steps are considered as required to write a proper and as good as possible fail save payment processor.

* Your processer has to extend BasePaymentProcessor and use the interfaces as needed
* You have to use set() to set values for the API / processor and validateValues() to check if all required values for a call are present
* You have to use the Exceptions from the Payments plugin to encapsulate payment gateway API errors and payment processor issues
* Use the PaymentApiLog to log payment related messages

Contact us to get your processor reviewed and added to this list if it matches the acceptance criterias.

### Recommended field names

To make it easier for everyone to use different processors without the need to map the fields of the app to all the processors in a different way the following field names are recommended:

Generic fields:

* amount
* description

For Credit Card processors

* card_number
* card_code
* card_holder
* card_expiration_date

## Support

For support and feature request, please visit the Payments issue page

https://github.com/burzum/Payments/issues

## License

Copyright 2012, Florian Krämer

Licensed under The MIT License
Redistributions of files must retain the above copyright notice.