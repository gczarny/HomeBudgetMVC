<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Models\Expense;
use \App\Models\Income;
use \App\Models\Balance;
use \App\Flash;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Dashboard extends Authenticated
{
    /**
     * Before filter - called before each action method
     * 
     * @return void
     */
    
    protected function before()
    {
        parent::before(); //so we will not override

        $this->user = Auth::getUser();
        
    }

    /**
     * Show the dashboard index page
     *
     * @return void
     */
    public function indexAction()
    {
        View::renderTemplate('Dashboard/index.html', [
            'amountByMonth' => Balance::getAmountByMonthToChart(),
            'allIncome' => Balance::getAllIncome(),
            'monthIncome' => Balance::getMonthIncome(),
            'todayIncome' => Balance::getTodayIncome(),
            'allExpense' => Balance::getAllExpense(),
            'monthExpense' => Balance::getMonthExpense(),
            'todayExpense' => Balance::getTodayExpense(),
            'user' => $this->user
        ]);
    }

    /**
     * Show the form to add income
     * 
     * @return void
     */
    public function addincomeAction()
    {
        $this->incomeList = Income::getIncomeCategory();
        View::renderTemplate('Dashboard/addincome.html', [
            'incomeList' => $this->incomeList,
            'user' => $this->user
        ]);
    }

     /**
     * Show the form to add expense
     * 
     * @return void
     */
    public function addexpenseAction()
    {
        $this->expenseList = Expense::getExpenseCategory();
        $this->paymentList = Expense::getPaymentMethod();
        View::renderTemplate('Dashboard/addexpense.html', [
            'expenseList' => $this->expenseList,
            'paymentList' => $this->paymentList,
            'user' => $this->user
        ]);
        //var_dump($expenseList);
    }

    /**
     * Show income balance view
     * 
     * @return void
     */
    public function showincomebalanceAction()
    {
        $this->incomeTable = Balance::getIncomeRecords();
        View::renderTemplate('Dashboard/showincomebalance.html', [
            'incomeTable' => $this->incomeTable,
            'user' => $this->user
        ]);
    }

    /**
     * Show expense balance view
     * 
     * @return void
     */
    public function showexpensebalanceAction()
    {
        $this->expenseTable = Balance::getExpenseRecords();
        View::renderTemplate('Dashboard/showexpensebalance.html', [
            'expenseTable' => $this->expenseTable,
            'user' => $this->user
        ]);
    }

    /**
     * Add expense to database
     * 
     * @return void
     */
    public function createexpenseAction()
    {
        $expense = new Expense($_POST);

        if ($expense->save()) {
            Flash::addMessage('Expense saved');
            $this->redirect('/dashboard/addexpense');
        } else {
            View::renderTemplate('Dashboard/addexpense.html', [
                'expense' => $expense
            ]);

        }
    }

    /**
     * Add income to database
     * 
     * @return void
     */
    public function createincomeAction()
    {
        $income = new Income($_POST);

        if ($income->save()) {
            Flash::addMessage('Income saved');
            $this->redirect('/dashboard/addincome');
        } else {
            View::renderTemplate('Dashboard/addincome.html', [
                'income' => $income
            ]);

        }
    }

    /**
     * 
     */

}
