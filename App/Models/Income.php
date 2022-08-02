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
        $this->validate();

        if (empty($this->errors)) {

            $sql = 'INSERT INTO incomes (user_id, income_category_assigned_to_user_id, 
                    amount, date_of_income, income_comment) 
                    VALUES (:user_id, :exp_cat, :amount, :expdate, :expcomment)';
                                              
            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':user_id', Auth::getUserID(), PDO::PARAM_INT);
            $stmt->bindValue(':exp_cat', $this->income_category, PDO::PARAM_INT);
            $stmt->bindValue(':amount', $this->amount, PDO::PARAM_STR);
            $stmt->bindValue(':expdate', $this->date, PDO::PARAM_STR);
            $stmt->bindValue(':expcomment', $this->income_comment, PDO::PARAM_STR);

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
        if ($this->income_category == '') {
            $this->errors[] = 'Type of income is required';
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
    }

    /**
     * Validate current property values, adding valiation error messages to the errors array property
     *
     * @return void
     */
    public function validateCategoryExists()
    {
        if(static::categoryExists($this->name, $this->id ?? null)){
            $this->errors[] = 'Category already exists!';
        }
    }

    /**
     * See if a category record already exists with the specified category
     *
     * @param string $category category to search for
     *
     * @return boolean  True if a record already exists with the specified category, false otherwise
     */
    public static function categoryExists($category, $ignore_id = null)
    {
         //return static::findByEmail($email) !== false;
        $categ = static::findByCategory($category);
        
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
    public static function findByCategory($category)
    {
        $sql = 'SELECT * FROM incomes_category_assigned_to_users 
                WHERE name = :name AND user_id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':name', $category, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', Auth::getUserID(), PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
        /*
        if($stmt->execute()){
            $num_rows= $stmt->rowCount();
            if($num_rows != 0) {
                return true;
            }
        }
        return false; */
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

    /**
     * Delete income from table
     * 
     * @param int
     * 
     * @return void
     */
    public static function deleteIncome($id)
    {
        $sql = 'DELETE FROM incomes WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        //$id = htmlspecialchars(strip_tags($id));
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        if($stmt->execute()){				
            return true;
        }
    }

    /**
     * Edit income category
     * 
     * @return void
     */
    public function editIncomeCategory()//($data)
    {
        $this->validateCategoryChange();
        $this->validateCategoryExists();
        if (empty($this->errors)) {
            $sql = 'UPDATE incomes_category_assigned_to_users 
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
     * Add income category
     * 
     * @param int
     * 
     * @return void
     */
    public function addIncomeCategory()
    {
        //$this->validateCategoryChange();
        $this->validateCategoryExists();

        if (empty($this->errors)) {
            $sql = 'INSERT INTO incomes_category_assigned_to_users(user_id, name)  
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
     * Delete income category from table
     * 
     * @param int
     * 
     * @return void
     */
    public static function deleteIncomeCategory($id)
    {
        $sql = 'DELETE FROM incomes_category_assigned_to_users WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        if($stmt->execute()){				
            return true;
        }
        return false;
    }
}