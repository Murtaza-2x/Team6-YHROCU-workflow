<?php
/*
-------------------------------------------------------------
Function: sendTaskCreateEmail
Description:
- Sends an email notification to the provided email address.
- Notifies the recipient about a new task assignment.
- Uses PHPMailer's SMTP configuration for Mailtrap.
-------------------------------------------------------------
*/

use PHPMailer\PHPMailer\PHPMailer;
USE PHPMailer\PHPMailer\Exception;

require_once __DIR__.'/../libs/PHPMailer/src/PHPMailer.php';
require_once __DIR__.'/../libs/PHPMailer/src/SMTP.php';
require_once __DIR__.'/../libs/PHPMailer/src/Exception.php';

function sendTaskCreateEmail ($toEmail) {
    $mail = new PHPMailer(true);

    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '15ae02232bf29d';
        $mail->Password = '68f352cc509c23';

        // Email details
        $mail->setFrom('yhrocunotifications@gmail.com', 'Task Notification');
        $mail->addAddress($toEmail);

        // Email body
        $mail->isHTML(true);
        $mail->Subject = 'A new task has been assigned';
        $mail->Body = '<p>Hi,</p><p>A new task has been assigned to you</p>';

        // Send email
        $mail->send();
    
    } catch (Exception $e) {
        // Log any errors
        error_log("Mailtrap email failed: {$mail->ErrorInfo}");
    }
}
?>