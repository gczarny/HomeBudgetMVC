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
    const DB_HOST = 'Your_host';

    /**
     * Database name
     * @var string
     */
    const DB_NAME = 'your_db_name';

    /**
     * Database user
     * @var string
     */
    const DB_USER = 'your_db_user';

    /**
     * Database password
     * @var string
     */
    const DB_PASSWORD = 'your_password';

    /**
     * Show or hide error messages on screen
     * @var boolean
     */
    const SHOW_ERRORS = true;

    /**
     * Secret key for hashing
     * @var string
     */
    const SECRET_KEY = 'your_generated_key_for_hashing';
}
