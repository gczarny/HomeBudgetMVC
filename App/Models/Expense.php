<?php

namespace App\Models;

use PDO;
use \App\Auth;

/**
 * User model
 *
 * PHP version 7.0
 */
class Expense extends \Core\Model
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
     * Save the expense model with the current property values
     *
     * @return boolean  True if the user was saved, false otherwise
     */
    public function save()
    {
        //$this->validate();

        if (empty($this->errors)) {

            $sql = 'INSERT INTO expenses (user_id, expense_category_assigned_to_user_id, 
                    payment_method_assigned_to_user_id, amount, date_of_expense, expense_comment) 
                    VALUES (:user_id, :exp_cat, :paymethod, :amount, :expdate, :expcomment)';
                                              
            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':user_id', Auth::getUserID(), PDO::PARAM_INT);
            $stmt->bindValue(':exp_cat', $this->expense_category, PDO::PARAM_INT);
            $stmt->bindValue(':paymethod', $this->expense_payment, PDO::PARAM_INT);
            $stmt->bindValue(':amount', $this->amount, PDO::PARAM_INT);
            $stmt->bindValue(':expdate', $this->date, PDO::PARAM_STR);
            $stmt->bindValue(':expcomment', $this->expense_comment, PDO::PARAM_STR);

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
        if ($this->expense_category == '') {
            $this->errors[] = 'Type of expense is required';
        }
        if ($this->expense_payment == '') {
            $this->errors[] = 'Method of payment is required';
        }
    }

    /**
     * Get expense category list
     * 
     * @param string $id The user ID
     * 
     * @return mixed Expense category object
     */
    public static function getExpenseCategory()
    {
        $sql = 'SELECT expense.id, expense.user_id, expense.name FROM expenses_category_assigned_to_users
                AS expense WHERE expense.user_id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', Auth::getUserID(), PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get Payment method list
     * 
     * @param string $id The user ID
     * 
     * @return mixed Payment category object
     */
    public static function getPaymentMethod()
    {
        $sql = 'SELECT expense.id, expense.user_id, expense.name FROM payment_methods_assigned_to_users
                AS expense WHERE expense.user_id =	:user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', Auth::getUserID(), PDO::PARAM_INT);

        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }
}