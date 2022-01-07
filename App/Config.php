<?php

namespace App;

/**
 * Application configuration
 *
 * PHP version 7.0
 */
class Config
{

    /**
     * Database host
     * @var string
     */
    const DB_HOST = '127.0.0.1:3307';

    /**
     * Database name
     * @var string
     */
    const DB_NAME = 'user';

    /**
     * Database user
     * @var string
     */
    const DB_USER = 'mvcuser';

    /**
     * Database password
     * @var string
     */
    const DB_PASSWORD = 'asdasd';

    /**
     * Show or hide error messages on screen
     * @var boolean
     */
    const SHOW_ERRORS = true;

    /**
     * Secret key for hashing
     * @var string
     */
    const SECRET_KEY = 'SJI3aWnP14xTGCjPDoVNUF5dH7UQugdW';

     /**
     * Mailgun API key
     * 
     * @var string
     */
    //const MAILGUN_API_KEY = '1905e3e201ffe331da3b0d8dbaca0182-7dcc6512-70a4447a';
    /**
     * Mailgun domain
     * 
     * @var string
     */
    //const MAILGUN_DOMAIN = 'sandboxb2545464388c411c97b324822e460142.mailgun.org';
}
