<?php
session_start();
include('config/functions.php');
include('dbcon.php');

if(isset($_POST['login_now_btn']))
{
    if(!empty(trim($_POST['email'])) && !empty(trim($_POST['password'])))
    {
        $email = mysqli_real_escape_string($con,$_POST['email']);
        $password = mysqli_real_escape_string($con,$_POST['password']);

        $login_query = "SELECT * FROM users WHERE email='$email' AND password='$password' LIMIT 1";
        $login_query_run = mysqli_query($con, $login_query);

        // checks email and password exists or not
        if(mysqli_num_rows($login_query_run) > 0)
        {
            $row = mysqli_fetch_array($login_query_run);
            
            // If the users Verification Status is 1, then will be able to login- Else verfiy your account.
            if($row['verify_status'] == "1")
            {
                $_SESSION['authenticated'] = TRUE;
                $_SESSION['auth_user'] = [
                    'username' => $row['name'],
                    'phone' => $row['phone'],
                    'email' => $row['email'],
                ];
                redirect("dashboard.php", "You are Logged In Successfully.");
            }
            else
            {
                redirect("login.php", "Please Verify your Email Address to Login.");
            }
        }
        else
        {
            redirect("login.php", "Invalid Email or Password");
        }
    }
    else
    {
        redirect("login.php", "All fields are Mandetory");
    }
    
}

?>