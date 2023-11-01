<?php
session_start();
include('config/functions.php');
include('dbcon.php');

if(isset($_GET['token']))
{
    $token = mysqli_real_escape_string($con, $_GET['token']);
    $verify_query = "SELECT verify_token,verify_status FROM users WHERE verify_token='$token' LIMIT 1";
    $verify_query_run = mysqli_query($con, $verify_query);

    // New Random Generated Token
    $new_gen_token = md5(rand());

    // Checks your Token exists or not
    if(mysqli_num_rows($verify_query_run) > 0)
    {
        $row = mysqli_fetch_array($verify_query_run);
        if($row['verify_status'] == "0")
        {
            $clicked_token = $row['verify_token'];
            
            // Once verfied, it will update your Verification Status as 1 and Change your Token value for Sucurity purpose.
            $update_query = "UPDATE users SET verify_status='1', verify_token='$new_gen_token' WHERE verify_token='$clicked_token' LIMIT 1";
            $update_query_run = mysqli_query($con, $update_query);

            if($update_query_run)
            {
                redirect("login.php", "Your Account has been verified Successfully.!");
            }
            else
            {
                redirect("login.php", "Verification Failed.!");
            }
        }
        else
        {
            redirect("login.php", "Email Already Verified. Please Login");
        }
    }
    else
    {
        redirect("login.php", "This Token does not Exists");
    }
}
else
{
    redirect("login.php", "Not Allowed");
}

?>