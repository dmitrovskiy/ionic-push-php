# Ionic push php sdk

This package may be helpful for sending ionic push notifications

## Install

Via Composer

``` bash
$ composer require dmitrovskiy/ionic-push-php
```

## Usage

``` php
$pusher = new Dmitrovskiy\IonicPush\PushProcessor(
    'APP_ID',
    'API_SECRET_ID'
);

$notifications = array(
    //...
);

$pusher->notify($notifications);
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
