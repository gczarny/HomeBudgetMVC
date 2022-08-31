$(document).ready(function() {
    $(".updateIncome").click(function() {
        $("#incomeID").val($(this).attr("data-id"));
        $("#incomeCategory").val($(this).attr("data-name"));
        $("#staticIncomeModal").modal("show");
    });

    $(".updateExpense").click(function() {
        $('#messageEditExpense').html('');
        $("#expenseCategory").val($(this).attr("data-name"));
        $("#categoryLimit").val($(this).attr("data-limit"));
        $("#expenseID").val($(this).attr("data-id"));
        var str = $("#expenseID").val();
        
        $("#staticExpenseModal").modal('show');
        if ($("#limitcheckbox").prop('checked')) {
            $("#categoryLimit").prop('disabled', false);
        }
        else
        {
            $("#categoryLimit").prop('disabled', true);
        }
        $.ajax({
            method: "POST",
            url: "/dashboard/checklimitexists",
            dataType: 'html',
            data: {'str1' : str},
                success: function(res) {
                    var datas1 = JSON.parse(res);
                    if(datas1['0'].limitisset == "1"){
                        $("#limitcheckbox").prop('checked', true);
                        $("#categoryLimit").prop('disabled', false);
                        $('#categoryLimit').attr('disabled', false).rules('add', {
                              required: true
                        });
                        $('#limitcheckbox').val('1');
                        $('input[type="submit"]').attr('disabled', false);
                    }
                    else{
                        $("#limitcheckbox").prop('checked', false);
                        $("#categoryLimit").prop('disabled', true);
                        validator.resetForm()
                        $('#categoryLimit').attr('disabled', true).rules('remove');
                        $('#limitcheckbox').val('0');
                        $('input[type="submit"]').attr('disabled', true);
                    }
                },
                error: function(res){
                    alert('Something went wrong');
                }
        });
    });

    $(".updatePayment").click(function() {
        $("#editPaymentID").val("");
        $("#editPaymentID").val($(this).attr("data-id"));
        $("#paymentMethod").val($(this).attr("data-name"));
        $("#staticPaymentModal").modal("show");
    });

    $(".addIncomeCategory").click(function() {
        $("#staticAddIncomeCategoryModal").modal("show");
    });

    $(".addExpenseCategory").click(function() {
        $("#staticAddExpenseCategoryModal").modal("show");
        if ($('#limitcheckbox').prop('checked')) {
            $("#categoryLimit").prop("disabled", false);
        }
        else
        {
            $("#categoryLimit").prop("disabled", true);
        }
    });

    $(".AddPaymentMethod").click(function() {
        $("#staticAddPaymentCategoryModal").modal("show");
    });

    var validator = $('#staticExpenseModal #formExpense').validate({
		rules: {
            expenseCategory: {
			 	required: true,
			 	minlength: 1
		    }
		},
		submitHandler: function(form) {
            var formData = {
                name: $("#expenseCategory").val(),
                categoryLimit: $("#categoryLimit").val(),
                limitCheckbox: $("#limitcheckbox").val(), 
                id: $("#expenseID").val(),
            }; 
            $.ajax({
                method: "POST",
                url: "/dashboard/editexpensecategory",
                dataType: 'text',
                data: formData,
                    success: function(resultEditExp) {
                        var data = JSON.parse(resultEditExp);
                        $('#messageEditExpense').html('');
                        if(data.status=='success'){
                            $("#staticExpenseModal").modal('hide');
                            window.location.reload();
                        }else{
                            var resultEditExpCat='';
                            $.each(data.errors,  function (index, element) {               
                                resultEditExpCat += element +  '<br/>';  
                              });           
                              $('#messageEditExpense').html(resultEditExpCat); //insert the concatinated values inside a div 
                        }
                    },
                    error: function(resultEditExp){
                        alert('Something went wrong');
                    }     
            });
          }
	});

    var validator = $('#staticIncomeModal #formIncome').validate({
		rules: {
            incomeCategory: {
			 	required: true,
			 	minlength: 1
		    }
		},
		submitHandler: function(form) {
            var formData = {
                name: $("#incomeCategory").val(),
                id: $("#incomeID").val(),
            }; 
            $.ajax({
                method: "POST",
                url: "/dashboard/editincomecategory",
                dataType: 'text',
                data: formData,//{id:id, name:name},
                    success: function(result) {
                        var data = JSON.parse(result);
                        $('#messageEditIncome').html('');
                        if(data.status=='success'){
                            $("#staticIncomeModal").modal('hide');
                            window.location.reload();
                        }else{
                            var resultIncome='';
                            $.each(data.errors,  function (index, element) {               
                                resultIncome += element +  '<br/>';  
                              });           
                              $('#messageEditIncome').html(resultIncome); //insert the concatinated values inside a div 
                        }
                    },
                    error: function(result){
                        alert('Something went wrong');
                    }     
            });
            //return false
          }
	});

    var validator = $('#staticPaymentModal #formPayment').validate({
		rules: {
            paymentMethod: {
			 	required: true,
			 	minlength: 1
		    }
		},
		submitHandler: function(form) {
            var formData = {
                name: $("#paymentMethod").val(),
                id: $("#editPaymentID").val(),
            }; 
            $.ajax({
                method: "POST",
                url: "/dashboard/editpaymentmethod",
                dataType: 'text',
                data: formData,
                    success: function(result) {
                        var data = JSON.parse(result);
                        $('#messageEditPayment').html('');
                        if(data.status=='success'){
                            $("#staticPaymentModal").modal('hide');
                            window.location.reload();
                        }else{
                            var result1='';
                            $.each(data.errors,  function (index, element) {               
                                result1 += element +  '<br/>';   
                              });           
                              $('#messageEditPayment').html(result1); //insert the concatinated values inside a div 
                        }
                    },
                    error: function(result){
                        alert('Something went wrong');
                    }     
            });
          }
	});

    var validator = $('#staticAddIncomeCategoryModal #formAddIncomeCategory').validate({
		rules: {
            addIncomeCategory: {
			 	required: true,
			 	minlength: 1
		    }
		},
		submitHandler: function(form) {
            var formData = {
                name: $("#addIncomeCategory").val(),
            }; 
            $.ajax({
                method: "POST",
                url: "/dashboard/addincomecategory",
                dataType: 'text',
                data: formData,
                    success: function(result) {
                        var data = JSON.parse(result);
                        $('#messageAddIncCategory').html('');
                        if(data.status=='success'){
                            $("#staticAddIncomeCategoryModal").modal('hide');
                            window.location.reload();
                        }else{
                            var result1='';
                            $.each(data.errors,  function (index, element) {               
                                result1 += element +  '<br/>';  
                              });           
                              $('#messageAddIncCategory').html(result1);
                        }
                    },
                    error: function(result){
                        alert('Something went wrong');
                    }     
            });
          }
	});

    var validator = $('#staticAddExpenseCategoryModal #formAddExpenseCategory').validate({
		rules: {
            addExpenseCategory: {
			 	required: true,
			 	minlength: 1
		    }
		},
		submitHandler: function(form) {
            var formData = {
                name: $("#addExpenseCategory").val(),
            }; 
            $.ajax({
                method: "POST",
                url: "/dashboard/addexpensecategory",
                dataType: 'text',
                data: formData,
                    success: function(resultAddExp) {
                        var data = JSON.parse(resultAddExp);
                        $('#messageAddExpCategory').html('');
                        if(data.status=='success'){
                            $("#staticAddExpenseCategoryModal").modal('hide');
                            window.location.reload();
                        }else{
                            var resultAddExpenseCat='';
                            $.each(data.errors,  function (index, element) {               
                                resultAddExpenseCat += element +  '<br/>';  
                              });           
                              $('#messageAddExpCategory').html(resultAddExpenseCat); //insert the concatinated values inside a div 
                        }
                    },
                    error: function(resultAddExp){
                        alert('Something went wrong');
                    }     
            });
          }
	});

    var validator = $('#staticAddPaymentCategoryModal #formAddPaymentCategory').validate({
		rules: {
            addPaymentCategory: {
			 	required: true,
			 	minlength: 1
		    }
		},
		submitHandler: function(form) {
            var formData = {
                name: $("#addPaymentCategory").val(),
            }; 
            $.ajax({
                method: "POST",
                url: "/dashboard/addpaymentcategory",
                dataType: 'text',
                data: formData,
                    success: function(resultAddPay) {
                        var data = JSON.parse(resultAddPay);
                        $('#messageAddPayCategory').html('');
                        if(data.status=='success'){
                            $("#staticAddPaymentCategoryModal").modal('hide');
                            window.location.reload();
                        }else{
                            var resultAddPaymentCat='';
                            $.each(data.errors,  function (index, element) {               
                                resultAddPaymentCat += element +  '<br/>';  
                              });           
                              $('#messageAddPayCategory').html(resultAddPaymentCat); //insert the concatinated values inside a div 
                        }
                    },
                    error: function(resultAddPay){
                        alert('Something went wrong');
                    }     
            });
          }
	});

    $('#limitcheckbox').click(function() {
        if($(this).is(':checked')) {
          $('#categoryLimit').attr('disabled', false).rules('add', {
            required: true
          });
          $('#limitcheckbox').val('1');
          $('input[type="submit"]').attr('disabled', false);
        } else {
          validator.resetForm()
          $('#categoryLimit').attr('disabled', true).rules('remove');
          $('#limitcheckbox').val('0');
          $('input[type="submit"]').attr('disabled', true);
        }
    });

    $("#staticIncomeModal").on("hidden.bs.modal", function(){
        $("#messageEditIncome").html("");
    });
    $("#staticExpenseModal").on("hidden.bs.modal", function(){
        $("#messageEditExpense").html("");
    });
    $("#staticPaymentModal").on("hidden.bs.modal", function(){
        $("#messageEditPayment").html("");
    });
    $("#staticAddIncomeCategoryModal").on("hidden.bs.modal", function(){
        $("#messageAddIncCategory").html("");
    });
    $("#staticAddExpenseCategoryModal").on("hidden.bs.modal", function(){
        $("#messageAddExpCategory").html("");
    });
    $("#staticAddPaymentCategoryModal").on("hidden.bs.modal", function(){
        $("#messageAddPayCategory").html("");
    });

    $("#list-incomes").on('click', '.delete', function(){
            var button = $(this);
            var id = $(this).attr("id");	
            var action = "deleteIncomeCategory";
            
            if(confirm("Are you sure you want to delete this record? Your will lost category assignemt to your incomes")) {
                $.ajax({
                    url:"/dashboard/deleteincomecategory",
                    method:"POST",
                    data:{id:id, action:action},
                    success:function(callback) {		
                        button.closest("li").remove();
                    }
                });
            } else {
                return false;
            }
    });
    $("#list-expenses").on('click', '.delete', function(){
        var button = $(this);
        var id = $(this).attr("id");	
        var action = "deleteExpenseCategory";
        
        if(confirm("Are you sure you want to delete this record? Your will lost category assignemt to your expenses")) {
            $.ajax({
                url:"/dashboard/deleteexpensecategory",
                method:"POST",
                data:{id:id, action:action},
                success:function(callback) {		
                    button.closest("li").remove();
                }
            });
        } else {
            return false;
        }
    });
    $("#list-payments").on('click', '.delete', function(){
        var button = $(this);
        var id = $(this).attr("id");	
        var action = "deletePaymentMethod";
        
        if(confirm("Are you sure you want to delete this record? Your will lost category assignemt to your payment")) {
            $.ajax({
                url:"/dashboard/deletepaymentmethod",
                method:"POST",

                data:{id:id, action:action},
                success:function(callback) {		
                    button.closest("li").remove();
                }
            });
        } else {
            return false;
        }
    });

});