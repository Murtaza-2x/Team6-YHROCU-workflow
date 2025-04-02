<?php

use PHPMailer\PHPMailer\PHPMailer;
USE PHPMailer\PHPMailer\Exception;

require_once __DIR__.'/../libs/PHPMailer/src/PHPMailer.php';
require_once __DIR__.'/../libs/PHPMailer/src/SMTP.php';
require_once __DIR__.'/../libs/PHPMailer/src/Exception.php';


function sendTaskUpdateEmail ($toEmail) {
    $mail = new PHPMailer(true);

    try {
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
        $mail->Subject = "There has been an update to your task.";
        $mail->Body = '<p>Hi,</p><p>A task you were assigned to has updates.</p>';

        $mail->send();
    
    } catch (Exception $e) {
        error_log("Mailtrap email faild: {$mail->ErrorInfo}");
    }
}
?>