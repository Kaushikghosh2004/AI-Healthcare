<?php

$con=mysqli_connect("localhost","root","","myhmsdb", 3306);














if(isset($_POST['update_data']))
{
 $contact = mysqli_real_escape_string($con, $_POST['contact']);
 $status  = mysqli_real_escape_string($con, $_POST['status']);
 $query="update appointmenttb set payment='$status' where contact='$contact';";
 $result=mysqli_query($con,$query);
 if($result) {
  header("Location:updated.php");
  exit();
 }
}















function display_specs() {
  global $con;
  $query="select distinct(spec) from doctb";
  $result=mysqli_query($con,$query);
  while($row=mysqli_fetch_array($result))
  {
    $spec=$row['spec'];
    echo '<option value="'.$spec.'" data-value="'.$spec.'">'.$spec.'</option>';
  }
}

function display_docs()
{
 global $con;
 $query = "select * from doctb";
 $result = mysqli_query($con,$query);
 while( $row = mysqli_fetch_array($result) )
 {
  $username = $row['username'];
  $price = $row['docFees'];
  $spec = $row['spec'];
  echo '<option value="' .$username. '" data-value="'.$price.'" data-spec="'.$spec.'">'.$username.'</option>';
 }
}














if(isset($_POST['doc_sub']))
{
 $username = mysqli_real_escape_string($con, $_POST['username']);
 $query="insert into doctb(username)values('$username')";
 $result=mysqli_query($con,$query);
 if($result) {
  header("Location:adddoc.php");
  exit();
 }
}

?>