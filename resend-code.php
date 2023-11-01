<?php
session_start();
include('config/functions.php');
include('dbcon.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

function resend_email_verify($name, $email, $verify_token)
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
    $mail->Subject = "Resend - Email Verification from JoasZonderJoas";

    $email_template = "
        <h2>You have Registered with JoasZonderJoas</h2>
        <h3>Verify your email address to Login with the below given link</h3>
        <br/><br/>
        <a href='$domain_name/verify-email.php?token=$verify_token'> Click Me </a>
    ";
    
    $mail->Body = $email_template;
    $mail->send();
}

if(isset($_POST['resend_email_verify_btn']))
{
    if(!empty(trim($_POST['email'])))
    {
        $email = mysqli_real_escape_string($con, $_POST['email']);

        $checkemail_query = "SELECT * FROM users WHERE email='$email' LIMIT 1";
        $checkemail_query_run = mysqli_query($con, $checkemail_query);

        // Check record exists or not
        if(mysqli_num_rows($checkemail_query_run) > 0)
        {
            $row = mysqli_fetch_array($checkemail_query_run);
            // Checking that if you are not verified then Resend Email verification link will be share to you Email address.
            if($row['verify_status'] == "0")
            {
                $name = $row['name'];
                $email = $row['email'];
                $verify_token = $row['verify_token'];

                resend_email_verify($name,$email,$verify_token);

                redirect("login.php", "Verification Email Link has been sent to your email address.!");
            }
            else
            {
                redirect("resend-email-verification.php", "Email already verified. Please Login");
            }
        }
        else
        {
            redirect("register.php", "Email is not registered. Please Register now.!");
        }
    }
    else
    {
        redirect("resend-email-verification.php", "Please enter the email field");
    }
}

?>