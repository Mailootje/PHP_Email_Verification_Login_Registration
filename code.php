<?php
session_start();
include('config/functions.php');
include('dbcon.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function sendemail_verify($name,$email,$verify_token)
{
    include('config/email_config.php');
    $mail = new PHPMailer(true);
    // $mail->SMTPDebug = 2;
    $mail->isSMTP();
    $mail->SMTPAuth = true;

    $mail->Host = "$g_host";
    $mail->Username = "$domain_email";
    $mail->Password = "$domain_password";

    $mail->SMTPSecure = "$g_secure";
    $mail->Port = $g_port; 

    $mail->setFrom("$domain_email",$name);
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = "Email Verification from JoasZonderJoas";

    $email_template = "
        <h2>You have Registered with JoasZonderJoas</h2>
        <h3>Verify your email address to Login with the below given link</h3>
        <br/><br/>
        <a href='$domain_name/verify-email.php?token=$verify_token'> Click Me </a>
    ";
    
    $mail->Body = $email_template;
    $mail->send();
    // echo 'Message has been sent';

}


if(isset($_POST['register_btn']))
{
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $verify_token = md5(rand());

    if(!empty(trim($name)) && !empty(trim($phone)) && !empty(trim($email)) && !empty(trim($password)))
    {
        // Email Exists or not
        $check_email_query = "SELECT email FROM users WHERE email='$email' LIMIT 1";
        $check_email_query_run = mysqli_query($con, $check_email_query);

        if(mysqli_num_rows($check_email_query_run) > 0)
        {
            redirect("register.php", "Email Id already Exists/Registered");
        }
        else
        {
            // Insert User / Registered User Data
            $query = "INSERT INTO users (name,phone,email,password,verify_token) VALUES ('$name','$phone','$email','$password','$verify_token')";
            $query_run = mysqli_query($con, $query);

            if($query_run)
            {
                sendemail_verify("$name","$email","$verify_token");
                redirect("register.php", "Registration Successfull.! Please verify your Email Address.");
            }
            else
            {
                redirect("register.php", "Registration Failed.");
            }
        }
    }
    else
    {
        redirect("register.php", "All fields are Mandetory");
    }
}

?>
