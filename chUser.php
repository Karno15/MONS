<?php
    session_start();
    require('connect.php');
    if($_POST['Login']=='.')
    {   
        setcookie("UserId", "", time() - 3600);
        unset($_SESSION['UserId']);
    }
    else {
    if(isset($_POST['Login'])){
        $query = "SELECT UserId from users where Login='" . $_POST['Login']."'";
        $result= mysqli_query($conn, $query);
        $row = mysqli_fetch_row($result);
        
      
        //add userid  to cookie
     $_SESSION["UserId"]=$row[0];
     setcookie("UserId",$_SESSION["UserId"], time() + 300);
        }
    else {
        echo "User Not found!";
      }  }
header('location:index.php')
?>