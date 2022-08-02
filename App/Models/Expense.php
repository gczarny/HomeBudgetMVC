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
        $this->validate();

        if (empty($this->errors)) {

            $sql = 'INSERT INTO expenses (user_id, expense_category_assigned_to_user_id, 
                    payment_method_assigned_to_user_id, amount, date_of_expense, expense_comment) 
                    VALUES (:user_id, :exp_cat, :paymethod, :amount, :expdate, :expcomment)';
                                              
            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':user_id', Auth::getUserID(), PDO::PARAM_INT);
            $stmt->bindValue(':exp_cat', $this->expense_category, PDO::PARAM_INT);
            $stmt->bindValue(':paymethod', $this->expense_payment, PDO::PARAM_INT);
            $stmt->bindValue(':amount', $this->amount, PDO::PARAM_STR);
            $stmt->bindValue(':expdate', $this->date, PDO::PARAM_STR);
            $stmt->bindValue(':expcomment', $this->expense_comment, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }
     /**
     * Validate date
     * 
     * @return bool
     */
    public function validateDate($date)
    {
        if (false === strtotime($date)) { 
            return false;
        } 
        list($year, $month, $day) = explode('-', $date); 
        return checkdate($month, $day, $year);
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
        else if(!is_numeric($this->amount)){
            if(strstr($this->amount, ",")){
                $this->amount = str_replace(",",".", $this->amount);
                if(!is_numeric($this->amount)){
                    $this->errors[] = 'Amount must be numeric!';
                }
            }
            else{
                $this->errors[] = 'Amount must be numeric!';
            }
        }
        else if (strstr($this->amount, ",") && !is_numeric($this->amount)){
            $var = str_replace(",","", $this->amount);
            if(!is_numeric($var)){
                $this->errors[] = 'Amount must be numeric!';
            }
        }
        if ($this->expense_category == '') {
            $this->errors[] = 'Type of expense is required';
        }
        if ($this->expense_payment == '') {
            $this->errors[] = 'Method of payment is required';
        }
        if (!($this->validateDate($this->date))) {
            $this->errors[] = 'Wrong format of date!';
        }
    }

    /**
     * Validate current property values, adding valiation error messages to the errors array property
     *
     * @return void
     */
    public function validateCategoryChange()
    {
        if ($this->name == ''){
            $this->errors[] = 'Category name cannot be empty!';
        }
        else if(is_numeric($this->name)){
            $this->errors[] = 'Category cannot be numeric!';
        }

        if ($this->categoryLimit == '0' && $this->limitCheckbox == '1') {
            $this->errors[] = 'Amount is required';
        }
        else if(!is_numeric($this->categoryLimit) && $this->limitCheckbox == '1'){
            if(strstr($this->categoryLimit, ",")){
                $this->categoryLimit = str_replace(",",".", $this->categoryLimit);
                if(!is_numeric($this->categoryLimit)){
                    $this->errors[] = 'Amount must be numeric!';
                }
            }
            else{
                $this->errors[] = 'Amount must be numeric!';
            }
        }    
    }

    /**
     * Validate current property values, adding valiation error messages to the errors array property
     *
     * @return void
     */
    public function validateCategoryExists($categname)
    {
        if(static::categoryExists($categname, $this->name, $this->id ?? null)){
            $this->errors[] = 'Category already exists!';
        }
    }

    /**
     * Validate payment method 
     * 
     * @return void
     */
    public function validatePaymentMethod()
    {
        if ($this->name == ''){
            $this->errors[] = 'Method name cannot be empty!';
        }
        else if(is_numeric($this->name)){
            $this->errors[] = 'Method cannot be numeric!';
        }
    }

    /**
     * See if a category record already exists with the specified category
     *
     * @param string $category category to search for
     *
     * @return boolean  True if a record already exists with the specified category, false otherwise
     */
    public static function categoryExists($name, $category, $ignore_id = null)
    {
         //return static::findByEmail($email) !== false;
        $categ = static::findByCategory($name, $category);
        
        if($categ) {
            if($categ->id != $ignore_id){
                return true;
            }
        }
        return false;
    }

    /**
     * Find a category model by category name
     * @param string $category to search on
     * 
     * @return mixed Category object is found, false otherwise 
     */
    public static function findByCategory($name, $category)
    {
        if($name === 'payment'){
            $sql = 'SELECT * FROM payment_methods_assigned_to_users 
                WHERE name = :name AND user_id = :user_id';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $category, PDO::PARAM_STR);
            $stmt->bindValue(':user_id', Auth::getUserID(), PDO::PARAM_INT);

            $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

            $stmt->execute();

            return $stmt->fetch();
        }
        if($name === 'expense'){
            $sql = 'SELECT * FROM expenses_category_assigned_to_users 
                WHERE name = :name AND user_id = :user_id';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $category, PDO::PARAM_STR);
            $stmt->bindValue(':user_id', Auth::getUserID(), PDO::PARAM_INT);

            $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

            $stmt->execute();

            return $stmt->fetch();
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
        $sql = 'SELECT expense.id, expense.user_id, expense.name, expense.is_limit, expense.limit_set
         FROM expenses_category_assigned_to_users AS expense WHERE expense.user_id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', Auth::getUserID(), PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get expenses sum
     * 
     * @return string Expense sum
     */
    public function getExpenseSum()  //expense.expense_category_assigned_to_user_id
    {
        /* $sql = "SELECT SUM(expense.amount) 
                FROM expenses AS expense
                WHERE expense.user_id = :user_id 
                AND expense.expense_category_assigned_to_user_id = :elo "; */

        $sql = "SELECT (    
                    (SELECT SUM(expense.amount) 
                        FROM expenses AS expense
                        WHERE expense.user_id = :user_id 
                        AND expense.expense_category_assigned_to_user_id = :expCatAssedToUserId) -
                    (SELECT limit_set FROM expenses_category_assigned_to_users as limitset
                        WHERE limitset.user_id = :user_id 
                        AND limitset.id = :expCatAssedToUserId)
                ) AS total";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', Auth::getUserID(), PDO::PARAM_INT);
        $stmt->bindValue(':expCatAssedToUserId', $this->str, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Check if imit is set
     * 
     * @return bool
     */
    public function checkLimitExists()  //expense.expense_category_assigned_to_user_id
    {
        $sql = 'SELECT is_limit as limitisset FROM expenses_category_assigned_to_users 
                WHERE user_id = :user_id AND id = :expCatAssedToUserId';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', Auth::getUserID(), PDO::PARAM_INT);
        $stmt->bindValue(':expCatAssedToUserId', $this->str1, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Get limit
     * 
     * @return string limit
     */
    public static function getLimitExpense()
    {
        $sql = 'SELECT limit_set as limit_sum FROM expenses_category_assigned_to_users 
                WHERE user_id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', Auth::getUserID(), PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
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

    /**
     * Delete expense from table
     * 
     * @param int
     * 
     * @return void
     */
    public static function deleteExpense($id)
    {
        $sql = 'DELETE FROM expenses WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        //$id = htmlspecialchars(strip_tags($id));
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        if($stmt->execute()){				
            return true;
        }
    }
    
    /**
     * Edit expense category
     * 
     * @return void
     */
    public function editExpenseCategory()
    {
        $this->validateCategoryChange();
        $this->validateCategoryExists('expense');

        if (empty($this->errors)) {
            $sql = 'UPDATE expenses_category_assigned_to_users 
            SET name = :name, is_limit = :is_limit, limit_set = :limit_set
            WHERE id = :id';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
            $stmt->bindValue(':is_limit', $this->limitCheckbox, PDO::PARAM_INT);
            $stmt->bindValue(':limit_set', $this->categoryLimit, PDO::PARAM_STR); //$data['categoryLimit']
            return $stmt->execute();				
        }

        return false;
    }

    /**
     * Edit payment method
     * 
     * @return void
     */
    public function editPaymentMethod()
    {
        $this->validatePaymentMethod();

        if (empty($this->errors)) {
            $sql = 'UPDATE payment_methods_assigned_to_users 
                    SET name = :name WHERE id = :id';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
            return $stmt->execute();				
        }
        return false;
    }

    /**
     * Add expense category
     * 
     * @return void
     */
    public function addExpenseCategory()
    {
        //$this->validatePaymentMethod();
        $this->validateCategoryExists('expense');

        if (empty($this->errors)) {
            $sql = 'INSERT INTO expenses_category_assigned_to_users(user_id, name)
                    VALUES (:user_id, :name)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', Auth::getUserID(), PDO::PARAM_INT);
            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            return $stmt->execute();				
        }

        return false;
    }

    /**
     * Add payment category
     * 
     * @return void
     */
    public function addPaymentCategory()
    {
        $this->validateCategoryExists('payment');

        if (empty($this->errors)) {
            $sql = 'INSERT INTO payment_methods_assigned_to_users(user_id, name)
                    VALUES (:user_id, :name)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':user_id', Auth::getUserID(), PDO::PARAM_INT);
            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            return $stmt->execute();				
        }

        return false;
    }

    /**
     * Delete expesnse category from table
     * 
     * @param int
     * 
     * @return void
     */
    public static function deleteExpenseCategory($id)
    {
        $sql = 'DELETE FROM expenses_category_assigned_to_users WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        if($stmt->execute()){				
            return true;
        }
        return false;
    }

    /**
     * Delete payment from table
     * 
     * @param int
     * 
     * @return void
     */
    public static function deletePaymentMethod($id)
    {
        $sql = 'DELETE FROM payment_methods_assigned_to_users WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        if($stmt->execute()){				
            return true;
        }
        return false;
    }
}