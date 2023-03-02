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
            $error = "the beginning of holiday can't be more than the end!";

        } 
        else 
        { $start = $fromdate;
            $end = $todate;
            $days = 0; ;
            //function to exclude weekends and vacations days from leave request 
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
            
            //retreive the remaining leave hours for employee
            $empname = $_SESSION['eid'] ;
            $sql = mysqli_query($conn, "SELECT  Daily_Hours , leaveDays
            FROM tbemployees  where ID_emp='$empname'");
            
            $row1 = (mysqli_fetch_assoc($sql)) ;
            
            $daily = $row1['Daily_Hours'];
            $leave= $row1['leaveDays']; 
            //converting the hours into working days
            $Newhours = $daily * $days ; 
           





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
              //ask the employee that this leave is going to be paid and if confirmed and insert isPaid ==1
		    
		    /* I have no idea how to bring this off 
		    */
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
        <input type="hidden" id="LeaveTypee" name="LeaveTypee" value="<?php echo $LeaveTypee ?>">
<input type="hidden" id="fromdate" name="fromdate" value="<?php echo $fromdate ?>">
<input type="hidden" id="todate" name="todate" value="<?php echo $todate ?>">
<input type="hidden" id="Description" name="Description" value="<?php echo $Description ?>">
<input type="hidden" id="status" name="status" value="<?php echo $status ?>">
<input type="hidden" id="isread" name="isread" value="<?php echo $isread ?>">
<input type="hidden" id="isPaid" name="isPaid" value="<?php echo $isPaid ?>">
<input type="hidden" id="empid" name="empid" value="<?php echo $empid ?>">


<br>



                </div>

                
                <!-- trading history area end -->
            </div>





    </div>
 </div>
<?php include 'footer_emp.php';?>


  
  
    </body>



</html>











