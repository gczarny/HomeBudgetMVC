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
        $this->incomeList = Income::getIncomeCategory();
        $this->expenseList = Expense::getExpenseCategory();
        $this->paymentList = Expense::getPaymentMethod();
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
        //$this->expenseTable = Balance::getExpenseRecords();
        $this->expenseTable = Balance::getExpenseWithCategoriesRecords();
        View::renderTemplate('Dashboard/addexpense.html', [
            'expenseList' => $this->expenseList,
            'paymentList' => $this->paymentList,
            'expenseTable' => $this->expenseTable,
            'user' => $this->user
        ]);
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
     * Show overview balance view
     * 
     * @return void
     */
    public function showoverviewAction()
    {
        $overallTable = Balance::getOverallTable();
        View::renderTemplate('Dashboard/showoverview.html', [
            'overallTable' => $overallTable,
            'user' => $this->user
        ]);
    }

    /**
     * Show settings view
     * 
     * @return void
     */
    public function settingsAction()
    {
        View::renderTemplate('Dashboard/settings.html', [
            'incomeList' => $this->incomeList,
            'expenseList' => $this->expenseList,
            'paymentList' => $this->paymentList,
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
                'user' => $this->user,
                'expense' => $expense,
                'expenseList' => $this->expenseList,
                'paymentList' => $this->paymentList,
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
                'user' => $this->user,
                'income' => $income,
                'incomeList' => $this->incomeList
            ]);
        }
    }

    /**
     * Add income category to database
     * 
     * @return void
     */
    public function addincomecategoryAction()
    {
        $income = new Income($_POST);

        if ($income->addIncomeCategory()) {
            Flash::addMessage('Category added');
            $response['status'] = 'success';
            echo json_encode($response);
            //return true;
        } else {
            //$errors = array($limit->errors);
            $response['errors'] = $income->errors;
            echo json_encode($response);
        }
    }

    /**
     * Add expense category to database
     * 
     * @return void
     */
    public function addexpensecategoryAction()
    {
        $expense = new Expense($_POST);

        if ($expense->addExpenseCategory()) {
            Flash::addMessage('Category added');
            $response['status'] = 'success';
            echo json_encode($response);
            //return true;
        } else {
            //$errors = array($limit->errors);
            $response['errors'] = $expense->errors;
            echo json_encode($response);
        }
    }

    /**
     * Add payment category to database
     * 
     * @return void
     */
    public function addpaymentcategoryAction()
    {
        $payment = new Expense($_POST);

        if ($payment->addPaymentCategory()) {
            Flash::addMessage('Method added');
            $response['status'] = 'success';
            echo json_encode($response);
            //return true;
        } else {
            //$errors = array($limit->errors);
            $response['errors'] = $payment->errors;
            echo json_encode($response);
        }
    }

    /**
     * Delete selected income
     * 
     * @return void
     */
    public function deleteincomeAction()
    {
        if(!empty($_POST['action'])){
            if($this->user){
                if(Income::deleteIncome($_POST['id'])){
                    return true;
                }
                else{
                    return false;
                }
            }
        }
    }

    /**
     * Delete selected expense
     * 
     * @return void
     */
    public function deleteexpenseAction()
    {
        if(!empty($_POST['action'])){
            $id = $_POST['id'];
            if($this->user){
                if(Expense::deleteExpense($id)){
                    return true;
                }
                else{
                    return false;
                }
            }
        }
    }

    /**
     * Modify income category
     * 
     * @return void
     */
    public function editincomecategoryAction()
    {
        $response = [];
        $editIncome = new Income($_POST);

        if ($editIncome->editIncomeCategory()) {
            Flash::addMessage('Category changed');
            $response['status'] = 'success';
            echo json_encode($response);
            //return true;
        } else {
            //$errors = array($limit->errors);
            $response['errors'] = $editIncome->errors;
            echo json_encode($response);
        }
    }

    /**
     * Modify income category
     * 
     * @return void
     */
    public function editexpensecategoryAction()
    {
        $response = [];
        $expcateg = new Expense($_POST);

        if ($expcateg->editExpenseCategory()) {
            Flash::addMessage('Category changed');
            $response['status'] = 'success';
            echo json_encode($response);
            //return true;
        } else {
            $response['errors'] = $expcateg->errors;
            echo json_encode($response);
        }
    }

    /**
     * Modify payment method
     * 
     * @return void
     */
    public function editpaymentmethodAction()
    {
        $response = [];
        $paymethod = new Expense($_POST);

        if ($paymethod->editPaymentMethod()) {
            Flash::addMessage('Method changed');
            $response['status'] = 'success';
            echo json_encode($response);
            //return true;
        } else {
            $response['errors'] = $paymethod->errors;
            echo json_encode($response);
        }
    }

    /**
     * Delete income category
     * 
     * @return void
     */
    public function deleteincomecategoryAction()
    {
        if(!empty($_POST['action'])){
            if($this->user){
                if(Income::deleteIncomeCategory($_POST['id'])){
                    return true;
                }
                return false;
            }
            return false;
        }
        return false;
    }
    
    /**
     * Delete expense category
     * 
     * @return void
     */
    public function deleteexpensecategoryAction()
    {
        if(!empty($_POST['action'])){
            if($this->user){
                if(Expense::deleteExpenseCategory($_POST['id'])){
                    return true;
                }
                return false;
            }
            return false;
        }
        return false;
    }

    /**
     * Delete payment method
     * 
     * @return void
     */
    public function deletepaymentmethodAction()
    {
        if(!empty($_POST['action'])){
            if($this->user){
                if(Expense::deletePaymentMethod($_POST['id'])){
                    return true;
                }
                return false;
            }
            return false;
        }
        return false;
    }

    /**
     * Get expense sum
     * 
     * @return void
     */
    public function getexpensesumAction()
    {
        $expcateg = new Expense($_POST);
        //$elo = $_POST['name'];
        if($this->user){
            $response['status'] = 'success';
            $response[] = $expcateg->getExpenseSum();
            echo json_encode($response);
        }
        return false;
    }

    /**
     * check if limit is set
     * 
     * @return void
     */
    public function checklimitexistsAction()
    {
        $limitExists = new Expense($_POST);
        //$elo = $_POST['name'];
        if($this->user){
            //$response['islimit'] = 'success';
            $response[] = $limitExists->checkLimitExists();
            echo json_encode($response);
        }
        return false;
    }

    /**
     * Get expense limit
     * 
     * @return void
     */
    public function getexpenselimitAction()
    {
        $expcateg = new Expense($_POST);
        if($this->user){
            //$row = Expense::getExpenseSum()
            $response = $expcateg->getExpenseSum();
            echo json_encode($response);
        }
        return false;
    }
}
