<?php
/*
-------------------------------------------------------------
Function: inc_email.php
Description:
- Sends an email notification to the provided email address.
- Notifies about task creation or updates with task details.
- Uses PHPMailer's SMTP configuration for Mailtrap.
-------------------------------------------------------------
*/

require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendTaskEmail($toEmail, $subject, $messageBody, $taskDetails)
{
    $mail = new PHPMailer(true);

    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io'; // Use SMTP server
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '435cea94b3e037'; // Mailtrap SMTP username
        $mail->Password = '3f751ba4355c4e'; // Mailtrap SMTP password

        // Email details
        $mail->setFrom('notifications@yhrocu.com', 'Task Notification');
        $mail->addAddress($toEmail);

        // Prepare task details
        $taskData = "
                <p><strong>Subject:</strong> {$taskDetails['subject']}</p>
                <p><strong>Project:</strong> {$taskDetails['project_name']}</p>
                <p><strong>Status:</strong> {$taskDetails['status']}</p>
                <p><strong>Priority:</strong> {$taskDetails['priority']}</p>
                <p><strong>Description:</strong> {$taskDetails['description']}</p>
                ";

        // Email body
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $messageBody . $taskData; // Append task details to the email body

        // Send email
        if ($mail->send()) {
            // Log success or return success message
            error_log("Email successfully sent to {$toEmail}");
            return true;
        }
    } catch (Exception $e) {
        // Log any errors
        error_log("Mailtrap email failed: {$mail->ErrorInfo}");
        return false;
    }
}
