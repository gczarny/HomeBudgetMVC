<?php

namespace App\Models;

use PDO;
use \App\Auth;

/**
 * User model
 *
 * PHP version 7.0
 */
class Income extends \Core\Model
{
    /**
     * Error messages
     *
     * @var array
     */
    public $errors = [];

    /**
     * Class constructor
     *
     * @param array $data  Initial property values
     *
     * @return void
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    /**
     * Save the income model with the current property values
     *
     * @return boolean  True if the user was saved, false otherwise
     */
    public function save()
    {
        //$this->validate();

        if (empty($this->errors)) {

            $sql = 'INSERT INTO incomes (user_id, income_category_assigned_to_user_id, 
                    amount, date_of_income, income_comment) 
                    VALUES (:user_id, :exp_cat, :amount, :expdate, :expcomment)';
                                              
            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':user_id', Auth::getUserID(), PDO::PARAM_INT);
            $stmt->bindValue(':exp_cat', $this->income_category, PDO::PARAM_INT);
            $stmt->bindValue(':amount', $this->amount, PDO::PARAM_INT);
            $stmt->bindValue(':expdate', $this->date, PDO::PARAM_STR);
            $stmt->bindValue(':expcomment', $this->income_comment, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }

    /**
     * Validate current property values, adding valiation error messages to the errors array property
     *
     * @return void
     */
    public function validate()
    {
        if ($this->amount == '') {
            $this->errors[] = 'Amount is required';
        }
        if ($this->income_category == '') {
            $this->errors[] = 'Type of income is required';
        }
    }

    /**
     * Get income category list
     * 
     * @param string $id The user ID
     * 
     * @return mixed income category object
     */
    public static function getIncomeCategory()
    {
        $sql = 'SELECT income.id, income.user_id, income.name FROM incomes_category_assigned_to_users
                AS income WHERE income.user_id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', Auth::getUserID(), PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}