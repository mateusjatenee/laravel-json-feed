PHP Json Feed
================
[![Build Status](https://travis-ci.org/mateusjatenee/php-json-feed.svg?branch=master)](https://travis-ci.org/mateusjatenee/php-json-feed)
[![Latest Stable Version](https://poser.pugx.org/mateusjatenee/php-json-feed/v/stable)](https://packagist.org/packages/mateusjatenee/php-json-feed)
[![Total Downloads](https://poser.pugx.org/mateusjatenee/php-json-feed/downloads)](https://packagist.org/packages/mateusjatenee/php-json-feed)
[![Latest Unstable Version](https://poser.pugx.org/mateusjatenee/php-json-feed/v/unstable)](https://packagist.org/packages/mateusjatenee/php-json-feed)
[![License](https://poser.pugx.org/mateusjatenee/php-json-feed/license)](https://packagist.org/packages/mateusjatenee/php-json-feed)

![bitmoji](https://render.bitstrips.com/v2/cpanel/10152648-280888328_2-s4-v1.png?transparent=1&palette=1&width=246)   

This library provides a way to generate [JSON feeds](https://jsonfeed.org), a format recently introduced to the community.

#### Installation via Composer
``` bash
$ composer require mateusjatenee/php-json-feed
```

#### Laravel Installation   

If you are using __laravel 5.5__ or higher the installation is already finished! You don't need to do another thing.

If you are using laravel 5.4 or lower open your `config/app.php` file and register the service provider:   
```php   
'providers' => [
    ...
    Mateusjatenee\JsonFeed\JsonFeedServiceProvider::class,
];
```   
And on the `aliases` array, register the facade:   
```php   
'aliases' => [
    ...
    'JsonFeed' => Mateusjatenee\JsonFeed\Facades\JsonFeed::class,
];
```

#### Usage   
The library is really simple to use and actually does not depend on Laravel itself, though it allows you to use a config file (not yet). It automatically filters formats the JSON and removes any unnecessary property.   

If you're not using Laravel, [jump to this part](#all-at-once).

How to use it? Let's take the following JSON as an example:   
```json   
{
    "title": "My JSON Feed test",
    "home_page_url": "https://mguimaraes.co",
    "feed_url": "https://mguimaraes.co/feeds/json",
    "author": {
        "url": "https://twitter.com/mateusjatenee",
        "name": "Mateus Guimarães"
    },
    "icon": "https://mguimaraes.co/assets/img/icons/apple-touch-icon-72x72.png",
    "favicon": "https://mguimaraes.co/assets/img/icons/favicon.ico",
    "version": "https://jsonfeed.org/version/1",
    "items": [
        {
            "content_text": "Great book. It's the best book.",
            "date_published": "2017-05-22T00:00:00+00:00",
            "title": "1984",
            "author": {
                "name": "Mateus",
                "url": "https://mguimaraes.co"
            },
            "content_html": "<p>Great book. It's the best book.</p>",
            "id": "abc123",
            "url": "https://mguimaraes.co",
            "external_url": "https://laravel.com",
            "date_modified": "2017-05-22T00:00:00+00:00"
        },
        {
            "content_text": "Great book. It's the best book.",
            "date_published": "2017-05-22T00:00:00+00:00",
            "title": "1984",
            "author": {
                "name": "Mateus",
                "url": "https://mguimaraes.co"
            },
            "content_html": "<p>Great book. It's the best book.</p>",
            "id": "abc123",
            "url": "https://mguimaraes.co",
            "external_url": "https://laravel.com",
            "date_modified": "2017-05-22T00:00:00+00:00"
        }
    ]
}
```  

To do this, first you need to set the config — you can set it at any time during runtime (on a Service Provider, perhaps) using the Facade or instantiating it through the container (i.e `app('jsonFeed')`)

```php   
<?php

use Mateusjatenee\JsonFeed\Facades\JsonFeed;

$config = [
    'title' => 'My JSON Feed test',
    'home_page_url' => 'https://mguimaraes.co',
    'feed_url' => 'https://mguimaraes.co/feeds/json',
    'author' => [
        'url' => 'https://twitter.com/mateusjatenee',
        'name' => 'Mateus Guimarães',
    ],
    'icon' => 'https://mguimaraes.co/assets/img/icons/apple-touch-icon-72x72.png',
    'favicon' => 'https://mguimaraes.co/assets/img/icons/favicon.ico',
];

JsonFeed::setConfig($config);

```   

Then, you need to set the items. The items may be an array of objects or a collection of objects. We're gonna talk about this a bit later.   

```php   
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use JsonFeed;

class JsonFeedController extends Controller
{
    public function index()
    {
        $posts = App\Post::all();

        return JsonFeed::setItems($posts)->toJson();
    }
}

```

# All at once
Alternatively, you may do all at once, specially if you are not using Laravel.  

```php   
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use JsonFeed;

class JsonFeedController extends Controller
{
    public function index()
    {
        $posts = App\Post::all();

        $config = [
            'title' => 'My JSON Feed test',
            'home_page_url' => 'https://mguimaraes.co',
            'feed_url' => 'https://mguimaraes.co/feeds/json',
            'author' => [
                'url' => 'https://twitter.com/mateusjatenee',
                'name' => 'Mateus Guimarães',
            ],
            'icon' => 'https://mguimaraes.co/assets/img/icons/apple-touch-icon-72x72.png',
            'favicon' => 'https://mguimaraes.co/assets/img/icons/favicon.ico',
        ];

        return JsonFeed::start($config, $posts)->toJson();
    }
}

```

#### How to get each item properties   
It's really simple. An item only requires an `id`, the other fields are optional but highly recommended.  First of all, your model (or any other object that is gonna be used on the json feed) needs to implement `\Mateusjatenee\JsonFeed\Contracts\FeedItemContract` and it's only method - `getFeedId()`. It should return an unique Id relative to that item.  Below is a list of each method and what it does.  

| Method                 | What it does                                                                                     | Expects |
|------------------------|--------------------------------------------------------------------------------------------------|---------|
| `getFeedId`            | Gets a unique ID                                                                                 | string  |
| `getFeedUrl`           | Gets the URL of the resource                                                                     | string  |
| `getFeedExternalUrl`   | Gets the URL of the page elsewhere                                                               | string  |
| `getFeedTitle`         | Gets the resource title                                                                          | string  |
| `getFeedContentHtml`   | Gets the HTML of the content                                                                     | string  |
| `getFeedContentText`   | Gets the text of the content                                                                     | string  |
| `getFeedSummary`       | Gets the resource summary                                                                        | string  |
| `getFeedImage`         | Gets the resource image                                                                          | string  |
| `getFeedBannerImage`   | Gets the resource banner image                                                                   | string  |
| `getFeedDatePublished` | Gets the resource published date. The lib automatically converts it to the specific date format. | string  |
| `getFeedDateModified`  | Gets the resource modified date. The lib automatically converts it to the specific date format.  | string  |
| `getFeedAuthor`        | Gets the resource author. If not specified, the same as the top level one is going to be used.   | string  |
| `getTags`              | Gets the resource tags.                                                                          | array   |  

You may find all accepted methods on the [JSON Feed Spec](https://jsonfeed.org/version/1)

#### Running tests
``` bash
$ composer test
```

#### License
This library is licensed under the MIT license. Please see [LICENSE](LICENSE.md) for more details.

#### Changelog
Please see [CHANGELOG](CHANGELOG.md) for more details.

#### Contributing
Please see [CONTRIBUTING](CONTRIBUTING.md) for more details.
