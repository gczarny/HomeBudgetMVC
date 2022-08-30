<?php

namespace App\Models;

use PDO;
use \App\Auth;

/**
 * User model
 *
 * PHP version 7.0
 */
class Balance extends \Core\Model
{
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
     * Get expense category list
     * 
     * @return mixed Expense category object
     */
    public static function getIncomeRecords()
    {
        $sql = 'SELECT income.id, income.amount, income.date_of_income, income.income_comment, category.name
                FROM incomes AS income 
                LEFT JOIN incomes_category_assigned_to_users AS category ON income.income_category_assigned_to_user_id = category.id 
                WHERE income.user_id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', Auth::getUserID(), PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get expense records list
     * 
     * @return mixed Expense category object
     */
    public static function getExpenseRecords()
    {

        $sql = 'SELECT expense.id, expense.amount, expense.date_of_expense, category.name, payment.name AS payname, expense.expense_comment
                FROM expenses AS expense 
                LEFT JOIN expenses_category_assigned_to_users AS category ON expense.expense_category_assigned_to_user_id = category.id
                LEFT JOIN payment_methods_assigned_to_users AS payment ON expense.payment_method_assigned_to_user_id = payment.id
                WHERE expense.user_id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', Auth::getUserID(), PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**********************************************************************************************************************************************
     * Get expense records with assigned category list
     * 
     * @return mixed Expense category object
     */
    public static function getExpenseWithCategoriesRecords()
    {

        $sql = 'SELECT expense.id, expense.amount, expense.date_of_expense, expense.expense_category_assigned_to_user_id, category.name, payment.name AS payname, expense.expense_comment
                FROM expenses AS expense 
                LEFT JOIN expenses_category_assigned_to_users AS category ON expense.expense_category_assigned_to_user_id = category.id
                LEFT JOIN payment_methods_assigned_to_users AS payment ON expense.payment_method_assigned_to_user_id = payment.id
                WHERE expense.user_id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', Auth::getUserID(), PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all income
     * 
     * @return float summ amount 
     */
    public static function getAllIncome()
    {
        $sql = 'SELECT SUM(amount) AS Amount FROM incomes WHERE user_id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', Auth::getUserID(), PDO::PARAM_STR);
        $stmt->execute();
        $amount = $stmt->fetch(PDO::FETCH_ASSOC);
        return number_format((float)$amount['Amount'], 2, '.', '' );
    }

    /**
     * Get month income
     * 
     * @return float summ amount from current month
     */
    public static function getMonthIncome()
    {
        $sql = 'SELECT SUM(amount) AS Amount FROM incomes WHERE user_id = :user_id AND MONTH(date_of_income) = MONTH (CURRENT_DATE())';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', Auth::getUserID(), PDO::PARAM_STR);
        $stmt->execute();
        $amount = $stmt->fetch(PDO::FETCH_ASSOC);
        return number_format((float)$amount['Amount'], 2, '.', '' );
    }

    /**
     * Get today income
     * 
     * @return float summ today's amount 
     */
    public static function getTodayIncome()
    {
        $sql = 'SELECT SUM(amount) AS Amount FROM incomes WHERE user_id = :user_id AND date_of_income = CURRENT_DATE()';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', Auth::getUserID(), PDO::PARAM_STR);
        $stmt->execute();
        $amount = $stmt->fetch(PDO::FETCH_ASSOC);
        return number_format((float)$amount['Amount'], 2, '.', '' );
    }

    /**
     * Get all expense
     * 
     * @return float summ amount 
     */
    public static function getAllExpense()
    {
        $sql = 'SELECT SUM(amount) AS Amount FROM expenses WHERE user_id = :user_id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', Auth::getUserID(), PDO::PARAM_STR);
        $stmt->execute();
        $amount = $stmt->fetch(PDO::FETCH_ASSOC);
        return number_format((float)$amount['Amount'], 2, '.', '' );
    }

    /**
     * Get month expense
     * 
     * @return float summ amount from current month
     */
    public static function getMonthExpense()
    {
        $sql = 'SELECT SUM(amount) AS Amount FROM expenses WHERE user_id = :user_id AND MONTH(date_of_expense) = MONTH (CURRENT_DATE())';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', Auth::getUserID(), PDO::PARAM_STR);
        $stmt->execute();
        $amount = $stmt->fetch(PDO::FETCH_ASSOC);
        return number_format((float)$amount['Amount'], 2, '.', '' );
    }

    /**
     * Get today expense
     * 
     * @return float summ today's amount 
     */
    public static function getTodayExpense()
    {
        $sql = 'SELECT SUM(amount) AS Amount FROM expenses WHERE user_id = :user_id AND date_of_expense = CURRENT_DATE()';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', Auth::getUserID(), PDO::PARAM_STR);
        $stmt->execute();
        $amount = $stmt->fetch(PDO::FETCH_ASSOC);
        return number_format((float)$amount['Amount'], 2, '.', '' );
    }

    /**
     * Get today expense
     * 
     * @return mixed summ today's amount 
     */
    public static function getAmountByMonthToChart()
    {
        //$sql = 'SELECT SUM(amount) FROM expenses WHERE user_id = :user_id GROUP BY date_of_expense';
        $sql = 'SELECT YEAR(date_of_expense), MONTH(date_of_expense), SUM(amount) FROM expenses 
                WHERE user_id = :user_id
                GROUP BY YEAR(date_of_expense), MONTH(date_of_expense)
                ORDER BY YEAR(date_of_expense) ASC, MONTH(date_of_expense) ASC';
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', Auth::getUserID(), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get today expense
     * 
     * @return mixed summ today's amount 
     */
    public static function getDateToChart()
    {
        $sql = 'SELECT date_of_expense FROM expenses WHERE user_id = :user_id GROUP BY date_of_expense';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', Auth::getUserID(), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get table of incomes and expenses
     * 
     * @return mixed table of incomes and expenses
     */
    public static function getOverallTable()
    {
        $sql = 'SELECT income.id, income.amount, income.date_of_income AS date, category.name, NULL as payname, income.income_comment AS comment, "income" AS source
                FROM incomes AS income 
                LEFT JOIN incomes_category_assigned_to_users AS category ON income.income_category_assigned_to_user_id = category.id 
                WHERE income.user_id = :user_id
                UNION
                SELECT expense.id, expense.amount, expense.date_of_expense AS date, category.name, payment.name AS payname, expense.expense_comment AS comment, "expense" AS source
                FROM expenses AS expense 
                LEFT JOIN expenses_category_assigned_to_users AS category ON expense.expense_category_assigned_to_user_id = category.id
                LEFT JOIN payment_methods_assigned_to_users AS payment ON expense.payment_method_assigned_to_user_id = payment.id
                WHERE expense.user_id = :user_id';
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':user_id', Auth::getUserID(), PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

