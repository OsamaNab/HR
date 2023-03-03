<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

@include '../config.php';





if(isset($_SESSION['emplogin'])==0)
{   
    header('location:../index.php');
} 
else 
{
   
    $expensedate = date("Y-m-d");
  
    if (isset($_POST['add_le'])) 
    {
        $empid = $_SESSION['eid'];
        $LeaveTypee = $_POST['let'];
     $fromdate = $_POST['fod'];
        $todate = $_POST['tod'];
        $Description = $_POST['des'];
        $status = 0;
        $isread = 0;
        $isPaid = 1 ; 
       if ($fromdate > $todate) 
       {
            $error = "الرجاء اكتب بيانات صحيحة : يجب ان يكون تاريخ بداء الاجازة قبل تاريخ نهاية الاجازة ";

        } 
        else 
        { $start = $fromdate;
            $end = $todate;
            $days = 0; ;
            
            $query = "SELECT Vac_Date FROM vacations";
            $result = mysqli_query($conn, $query);
            $vacations = mysqli_fetch_all($result, MYSQLI_ASSOC);
            
            // Iterate through the date range
            while ($start <= $end) {
                // Check if the date is a Friday or in the Vac_Date column
                if (date('N', strtotime($start)) != 5 && !in_array($start, array_column($vacations, 'Vac_Date'))) {
                    // Increment the days counter
                    $days++;
                }
                // Move to the next day
                $start = date("Y-m-d", strtotime("+1 day", strtotime($start)));
            }
            
            
            $empname = $_SESSION['eid'] ;
            $sql = mysqli_query($conn, "SELECT  Daily_Hours , leaveDays
            FROM tbemployees  where ID_emp='$empname'");
            
            $row1 = (mysqli_fetch_assoc($sql)) ;
            
            $daily = $row1['Daily_Hours'];//8
            $leave= $row1['leaveDays']; //80 
            
            $Newhours = $daily * $days ; //8 * 5 = 40 
           





            if($leave > $Newhours )
            {
 // Employee has enough leave days, insert data into database
        $isPaid = 0;
    } else {

      echo '<script>  // Employee does not have enough leave days, ask for confirmation to proceed with paid leave request
        $confirmed =   confirm("You do not have enough leave days to cover this request. Do you still want to submit this as a paid leave request?");
        if($confirmed) {
            // User confirmed paid leave request, insert data into database with isPaid = 1
            $isPaid = 1;
        } else {
            // User did not confirm paid leave request, exit script
            exit();
        }

</script>';

                
            }
                // Insert leave request into tbleavemp table directly since leave hours are available
                $query = "INSERT INTO tbleavemp(LeaveType, FromDate, ToDate, Descr, Status, IsRead, empid) 
                VALUES ('$LeaveTypee','$fromdate','$todate','$Description','$status','$isread','$empid')";
                
                if ($conn->query($query) === TRUE) { 
                    $msg = "تم ارسال طلب الاجازة الخاص بك الى المسؤول سوف يتم الاجابة عليك باسرع وقت :شكرا لك";
                } else {  
                    $error = "اسف لايمكن ارسال الطلب بسبب هناك اخطاء يرجى المحاولة لاحقاً";
                }

            
           
        }
    }
}


   

 $empslect = $_SESSION['eid'] ;
                $select_name =mysqli_query($conn,"SELECT * from tbemployees where ID_emp=$empslect ");
                if(mysqli_num_rows($select_name)){
                  while($row=mysqli_fetch_assoc($select_name)){
                    $name = $row['name_emp'];
                }
                }

?>
<!DOCTYPE html>
<html>

<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="">
<meta name="author" content="">

<title> اضافة اجازة</title>
 <!-- jQuery -->
 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
 
 
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
 <!-- Bootstrap core CSS -->
<link href="../admin/css/bootstrap.rtl.min.css" rel="stylesheet">


<!-- Custom styles for this template -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
<link href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css"   rel="stylesheet">


    <!-- Bootstrap core JavaScript -->
    <script src="../admin/js/bootstrap.min.js"></script>
  <script src="../admin/js/bootstrap.bundle.min.js"></script>
  <script src="../admin/js/nav.js"></script>


  <script src="../admin/js/nav.js"></script>

<!-- Custom styles for this template -->

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/5.3.45/css/materialdesignicons.css" integrity="sha256-NAxhqDvtY0l4xn+YVa6WjAcmd94NNfttjNsDmNatFVc=" crossorigin="anonymous" />


<link href="../admin/css/style.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
      
    </style>
    <script>
        $(document).ready(function() {
            // Submit form using AJAX
            $('form').submit(function(event) {
                event.preventDefault();
                var form_data = $(this).serialize();
                $.ajax({
                    url: 'check_leave_days.php',
                    method: 'POST',
                    data: form_data,
                    success: function(response) {
                        if(response == 'paid') {
                            // Show confirmation message
                            var confirmation_message = '<div class="confirmation-message">';
                            confirmation_message += '<h2>You do not have enough leave days to cover this request.</h2>';
                            confirmation_message += '<p>Do you still want to submit this as a paid leave request?</p>';
                            confirmation_message += '<button id="confirm-paid-leave">Yes</button>';
                            confirmation_message += '<button id="cancel-paid-leave">No</button>';
                            confirmation_message += '</div>';
                            $('body').append(confirmation_message);
                            
                            // Handle confirmation button clicks
                            $('#confirm-paid-leave').click(function() {
                                $('input[name="isPaid"]').val('1');
                                $('form').unbind('submit').submit();
                            });
                            $('#cancel-paid-leave').click(function() {
                                $('.confirmation-message').remove();
                            });
                            
                        } else {
                            // No need for confirmation, submit form as normal
                            $('input[name="isPaid"]').val('0');
                            $('form').unbind('submit').submit();
                        }
                    }
                });
            });
        });
    </script>



<style>
		
		/*Overrides for Tailwind CSS */
		
		.modal {
            width: 100% !important;
margin: auto;        }
  .confirmation-message {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #fff;
            padding: 20px;
            border: 1px solid #000;
            border-radius: 10px;
            box-shadow: 0 0 10px #000;
        }
        .confirmation-message h2 {
            margin-top: 0;
        }
        .confirmation-message button {
            margin-top: 20px;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #4CAF50;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
        }
        .confirmation-message button:hover {
            background-color: #3e8e41;
        }

</style>

</head>

<body  id="body-pd" >
<div class="d-flex" id="wrapper">

 <?php 
  $page='dashboard';
  include '../include/navemp.php';
 ?>
    <div class="container" dir="rtl"> 

    <h3 class="mt-4 text-center">  التقدم بطلب للحصول على اجازة  </h3>
    <hr>

    <p class="text-muted font-14 mb-4">يرجى ملئ النموذج للحصول على اجازة جديدة</p>
 <?php if(@$error){?><div class="alert alert-danger "><strong>رسالة: </strong><?php echo $error; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

                            
                             </div><?php } 
                                 else if(@$msg){?><div class="alert alert-success" role="alert">
                                    <strong>رسالة: </strong><?php echo $msg; ?> 
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

                                 </div><?php }?>
    <div class="row ">

        <div class="col-md-2"></div>

        <div class="col-md" style="margin:auto;">
            <form action="" name="addemp" method="POST">
            <div class="form-group row pt-5">
                    <label for="expensedate" class="col-sm-4 col-form-label "><b>  تاريخ بداء الاجازة</b></label>
                    <div class="col-md-6 " >
                        <input type="date" class="form-control col-sm-12 " value="<?php echo $expensedate; ?>" name="fod" id="expensedate" required>
                    </div>
                </div>
                <div class="form-group row pt-5">
                    <label for="expensedate" class="col-sm-4 col-form-label "><b>تاريخ انتهاء الاجازة  </b></label>
                    <div class="col-md-6 " >
                        <input type="date" class="form-control col-sm-12 " value="<?php echo $expensedate; ?>" name="tod" id="expensedate" required>
                    </div>
                </div>
                <div class="form-group row  pt-5">
                <label for="expenseamount" class="col-sm-4 col-form-label"><b>  نوع الاجازة  </b></label>
                    <div class="col-md-6">
                    <select class="form-select" name="let" aria-label="Default select example">                          
                                         <option value="">اختار ..</option>
            <?php
            $select_Departm = mysqli_query($conn, "SELECT * FROM  tbleavetype ") or die('query failed');


            while ($row = mysqli_fetch_assoc($select_Departm)) {


            ?>
              <option name="let">
                <?php echo $row['LeaveType']; ?>
              </option>
            <?php
            }
            ?>
          </select>
                            </div>
                </div>
              
                
           
                <div class="form-group row  pt-5">
                <label for="expenseamount" class="col-sm-4 col-form-label"><b> اكتب سبب الاجازة او ملاحظة  </b></label>
                <div class="col-md-6">
                        <input type="text" class="form-control col-sm-12" value=""id="validationCustom01" name="des" required>

                    </div>
                </div>
              
                <div class="form-group row pt-5">
                                <div class="col-md-12 text-right">

                                         <input type="hidden" name="isPaid" value="">
                         
                                        <button type="submit" name="add_le" class="btn btn-primary mb-5"onclick="return valid();" > ارسال   </button>
                                </div>
                            </div>
                    </form>
                </div>

            </div>

            





        </div>
    
<br>



                </div>

                
                <!-- trading history area end -->
            </div>





    </div>
 </div>
<?php include 'footer_emp.php';?>


  
  
    </body>



</html>





