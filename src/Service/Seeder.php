<?php

namespace Kalnoy\LaravelCommon\Service;

use Illuminate\Database\Seeder as BaseSeeder;

/**
 * Base seeder that allows to specify global setup code.
 */
abstract class Seeder extends BaseSeeder
{
    /**
     * Whether the seeder is booted.
     */
    static private $booted;

    /**
     * Init seeder.
     */
    public function __construct()
    {
        static::bootIfNotBooted();
    }

    /**
     * Run `boot` method if not booted already.
     */
    protected static function bootIfNotBooted()
    {
        if ( ! self::$booted) {
            self::$booted = true;

            static::boot();
        }
    }

    /**
     * Boot up the seeder.
     *
     * Default implementation unguards eloquent models.
     */
    protected static function boot()
    {
        \Eloquent::unguard();
    }

}