# DPD.pl PICKUP Services
Integration for DPD.pl's PICKUP Services API avialble mypudo.dpd.com.pl.

Installation
------------

* install with Composer
```
composer require answear/dpd-pl-pickup-services-bundle
```

`Answear\DpdPlPickupServicesBundle\AnswearDpdPlPickupServicesBundle::class => ['all' => true],`  
should be added automatically to your `config/bundles.php` file by Symfony Flex.

Configuration
-------------

Below you will find bundle's full configuration:

```yaml
answear_dpd_pl_pickup_services:
    key: 'xxxxxx' # your API Key
    # settings below are set as default, you don't need to provide them
    url: 'https://mypudo.dpd.com.pl/api/pudo/' # service's URL
    requestTimeout: 10.0 # time the library will wait for server's response
```

When using library without Symfony you need to put your configuration into an
instance of `\Answear\DpdPlPickupServicesBundle\Service\ConfigProvider` object.

Usage
-----

DPD's API is available through the `Answear\DpdPlPickupServicesBundle\Service\PUDOList`
that is automatically registered in your Symfony application's DI container. PUDO items
are returned to you as `Answear\DpdPlPickupServicesBundle\ValueObject\PUDO` objects.

Final notes
------------

Feel free to open pull requests with new features, improvements or bug fixes. The Answear team 
will be grateful for any comments.
