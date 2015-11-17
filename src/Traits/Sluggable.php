<?php
namespace Caffeinated\Slugs\Traits;

trait Sluggable
{
    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function($model) {
            $slugName  = static::getSlugName();
            $slugField = static::getSlugField();

            $model->$slugField = str_slug($model->$slugName);

            $latestSlug = static::whereRaw("{$slugField} RLIKE '^{$model->slug}(-[0-9]*)?$'")
                ->latest('id')
                ->value($slugField);

            if ($latestSlug) {
                $pieces = explode('-', $latestSlug);

                $number = intval(end($pieces));

                $model->$slugField .= '-'.($number + 1);
            }
        });
    }

    /**
     * Get the name field associated for slugs.
     *
     * @return string
     */
    public static function getSlugName()
    {
        if (null !== static::$slug['name']) {
            return static::$slug['name'];
        }

        return 'name';
    }

    /**
     * Get the slug field associated for slugs.
     *
     * @return string
     */
    public static function getSlugField()
    {
        if (null !== static::$slug['field']) {
            return static::$slug['field'];
        }

        return 'slug';
    }
}
