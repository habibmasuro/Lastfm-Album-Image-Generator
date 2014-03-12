# Last.fm Top 10 Album Image Generator
By Dan Barrett - [yesdevnull.net/lastfm](http://yesdevnull.net/lastfm)

Easily get images for a top 10 list for your Last.fm profile.

## What It Is?
I'm using this on my website to generate [Last.fm](http://www.last.fm) album artwork - you can see it on [my profile here](http://last.fm/user/yesdevnull).

Originally, my image generator was a pretty rushed job and didn't look very pretty.  I started using Laravel recently and decided to port my code over to Laravel, with the goal of open sourcing this codebase.

I have limited the code to max out at the first 10 albums from the Last.fm API, but if you know what you're doing, you can modify it to pull down much more.

## What It Does
By providing your Last.fm username and the album number in a query string you'll get the image returned.

```
?user=yourusername&num=1
```

![Number 1 Album](http://lastfmalbumimagegenerator.com?user=yesdevnull&num=1)

You can also get a link returned instead of an image so you can have a URL that will redirect users to the Last.fm album page.  To do this, add the variable `type` with the value `link` - or `&type=link`.

## Generator

![Last.fm Album Image Generator Page](http://yesdevnull.net/wp-content/uploads/2014/03/generator.jpg)

If you enter the address `/generator` you'll land on an HTML form where you can enter your Last.fm username and get the BBCode required for Last.fm's profile page.

## Requires

* PHP 5.4 or greater

While this codebase is based on Laravel 4 ([laravel/framework](https://github.com/laravel/framework)), you'll also need a few other libraries.  While these are listed in the `composer.json` file, you'll find the list below:

* [laravel/framework](https://github.com/laravel/framework)
* [intervention/image](https://github.com/Intervention/image)
* [nesbot/carbon](https://github.com/briannesbitt/Carbon)
* [pda/pheanstalk](https://github.com/pda/pheanstalk)
* [guzzle/guzzle](https://github.com/guzzle/guzzle)
* [phpunit/phpunit](https://github.com/sebastianbergmann/phpunit)
* [whatthejeff/nyancat-phpunit-resultprinter](https://github.com/whatthejeff/nyancat-phpunit-resultprinter)

You'll also need to be running [Beanstalkd](http://kr.github.io/beanstalkd/), a simple, fast work queue system if you want to use the queuing system in Laravel.  If you don't have it installed and working, the queue system will process straight away, rather than being a proper queue system...

## Getting It Running

Once you've pulled down the codebase, it's important to do a few things.  First off, using composer you should install the rest of the dependancies:

```
composer update
```

Or `php composer.phar update` if Composer isn't in your bin path.

Next, you should run

```
php artisan key:generate
```

This will change the application key, as each installation should have a different, unique key.  Composer should hopefully do this already for you, but there's no harm in running it again.

You may also find that you need to change the default Queue driver in `app/config/queue.php` to `sync` if you're not using Beanstalkd.

## Unit Testing

I've written some tests, but it's my first time doing unit tests and using PHPUnit.  In its current state, there are 21 tests and 77 assertions, and all should pass.  I also spent some time making sure it's __mostly__ compliant with PSR-1 and PSR-2.  As my codebase isn't namespaced, PHP_CodeSniffer will freak out a bit.