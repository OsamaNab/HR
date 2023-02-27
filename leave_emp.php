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
                // Insert leave request into tbleavemp table directly since leave hours are available
                $query = "INSERT INTO tbleavemp(LeaveType, FromDate, ToDate, Descr, Status, IsRead, empid) 
                VALUES ('$LeaveTypee','$fromdate','$todate','$Description','$status','$isread','$empid')";
                
                if ($conn->query($query) === TRUE) { 
                    $msg = "تم ارسال طلب الاجازة الخاص بك الى المسؤول سوف يتم الاجابة عليك باسرع وقت :شكرا لك";
                } else {  
                    $error = "اسف لايمكن ارسال الطلب بسبب هناك اخطاء يرجى المحاولة لاحقاً";
                }

            } 
            else {
                // Show a Bootstrap confirmation message
                echo '<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                  <div class="modal-content">
                                    <div class="modal-header">
                                      <h5 class="modal-title" id="confirmationModalLabel">Confirmation Required</h5>
                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                      </button>
                                    </div>
                                    <div class="modal-body">
                              لا يوجد لديك اجازات سيتم خصم هذه الاجازة من راتبك هل تريد المتابعة?
                                    </div>
                                    <div class="modal-footer">
                                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                      <button type="button" class="btn btn-primary" id="confirmLeaveBtn">Yes</button>
                                    </div>
                                  </div>
                                </div>
                              </div>';
            
                // Add a JavaScript function to handle the confirmation button click
                
echo '<script>
var j = jQuery.noConflict();

j(document).ready(function() {
  j("#confirmLeaveBtn").click(function() {
    // Insert the leave request into the tbleavemp table
    var sql = "INSERT INTO tbleavemp (LeaveType, FromDate, ToDate, Descr, Status, IsRead, IsPaid, empid) " +
      "VALUES (\'" + $LeaveTypee + "\', \'" + $fromdate + "\', \'" + $todate + "\', \'" + $Description + "\', \'" + $status + "\', \'" + $isread + "\', \'" + $isPaid + "\', \'" + $empid + "\')";
    j.post("insert-leave-request.php", {sql: sql}, function(data) {
      // Display a success message
      j("#confirmationModal").modal("hide");
      alert("Leave request submitted successfully.");
    });
  });
});
</script>';
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

<script>
var j = jQuery.noConflict();

j(document).ready(function() {
  // Event handler for when the user selects a leave type
  j("#leaveType").change(function() {
    // Code to update the form based on the selected leave type goes here
  });

  // Event handler for when the user clicks the "Submit" button
  j("#submitLeaveBtn").click(function(e) {
    // Prevent the form from submitting by default
    e.preventDefault();

    // Code to validate the form data goes here
    // ...

    // Call the confirmLeave function if the form data is valid
    confirmLeave();
  });

  // Event handler for when the user clicks the "Cancel" button on the confirmation dialog
  j("#cancelLeaveBtn").click(function() {
    // Hide the confirmation dialog
    j("#confirmLeaveModal").modal("hide");
  });

  // Define the click event handler for the confirmation button
  j("#confirmLeaveBtn").click(function() {
    // Get the selected leave type and update the form action
    var leaveType = $j("#leaveType").val();
    var formAction = "submit_leave.php?type=" + leaveType;
    j("#leaveForm").attr("action", formAction);

    // Submit the form
    j("#leaveForm").submit();
  });
});

function confirmLeave() {
  // Code to check leave days and show the confirmation dialog goes here
  // ...

  // Show the confirmation dialog
  j("#confirmLeaveModal").modal("show");
}
   
 </script>

<style>
		
		/*Overrides for Tailwind CSS */
		
		.modal {
            width: 100% !important;
margin: auto;        }

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











