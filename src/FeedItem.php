<?php

namespace Mateusjatenee\JsonFeed;

use BadMethodCallException;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class FeedItem
{
    /**
     * @var array
     */
    protected $requiredProperties = [
        'id',
    ];

    /**
     * @var array
     */
    protected $acceptedProperties = [
        'content_text', 'date_published', 'title', 'author', 'tags',
        'content_html', 'summary', 'image', 'banner_image',
        'id', 'url', 'external_url', 'date_modified',
    ];

    /**
     * @var array
     */
    protected $dates = ['date_published', 'date_modified'];

    /**
     * @var mixed
     */
    protected $object;

    /**
     * @var mixed
     */
    protected $attachments;

    /**
     * @param $object
     * @param array $attachments
     */
    public function __construct($object, array $attachments = [])
    {
        $this->object = $object;
        $this->attachments = new Collection($attachments);
    }

    /**
     * Builds the structure of the feed item
     *
     * @return \Illuminate\Support\Collection
     */
    public function build()
    {
        return (new Collection($this->acceptedProperties))->flatMap(function ($property) {
            $method = 'get' . studly_case($property);

            return [$property => $this->$method()];
        })->reject(function ($value, $property) {
            return empty($value);
        });
    }

    /**
     * Converts the built item to an array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->build()->toArray();
    }

    /**
     * Gets a feed property if it exists
     *
     * @param string $property
     * @return mixed
     */
    public function getProperty(string $property)
    {
        $method = 'getFeed' . $property;

        $property = snake_case($property);

        if (method_exists($this->object, $method)) {
            $value = $this->object->$method();

            return in_array($property, $this->dates) ?
            (new Carbon($value))->toRfc3339String() :
            $value;
        }
    }

    /**
     * Handle dynamic methods calls
     *
     * @param $method
     * @param $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (substr($method, 0, 3) == 'get') {
            return $this->getProperty(substr($method, 3));
        }

        $className = static::class;

        throw new BadMethodCallException("Call to undefined method {$className}::{$method}()");
    }
}
