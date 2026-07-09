<?php
// includes/EmailHelper.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';

class EmailHelper {
    private $mail;

    public function __construct() {
        $this->mail = new PHPMailer(true);
        
        try {
            // Server settings (Update these with actual SMTP details)
            $this->mail->isSMTP();
            $this->mail->Host       = 'smtp.example.com'; // Set the SMTP server to send through
            $this->mail->SMTPAuth   = true;               // Enable SMTP authentication
            $this->mail->Username   = 'your_email@example.com'; // SMTP username
            $this->mail->Password   = 'your_password';    // SMTP password
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
            $this->mail->Port       = 587;                // TCP port to connect to

            // Default sender
            $this->mail->setFrom('noreply@dnsc.edu.ph', 'NSTP Office DNSC');
        } catch (Exception $e) {
            error_log("Mailer configuration error: {$this->mail->ErrorInfo}");
        }
    }

    public function sendPassNotification($studentEmail, $studentName, $serialNumber, $courseInfo) {
        try {
            $this->mail->addAddress($studentEmail, $studentName);

            // Content
            $this->mail->isHTML(true);
            $this->mail->Subject = 'NSTP Completion & Certificate Serial Number';
            $this->mail->Body    = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e5e7eb; border-radius: 8px;'>
                    <h2 style='color: #111827;'>Congratulations, $studentName!</h2>
                    <p style='color: #4b5563; font-size: 16px; line-height: 1.5;'>
                        We are pleased to inform you that you have officially <strong>passed</strong> the NSTP program ($courseInfo).
                    </p>
                    <div style='background-color: #f3f4f6; padding: 15px; border-radius: 6px; margin: 20px 0;'>
                        <p style='margin: 0; color: #374151; font-size: 14px;'>Your Certificate Serial Number:</p>
                        <h3 style='margin: 5px 0 0 0; color: #4f46e5; letter-spacing: 2px;'>$serialNumber</h3>
                    </div>
                    <p style='color: #4b5563; font-size: 14px; line-height: 1.5;'>
                        Please keep this serial number for your records. It will be used for verification purposes. Your official certificate will be available shortly.
                    </p>
                    <hr style='border: none; border-top: 1px solid #e5e7eb; margin: 20px 0;'>
                    <p style='color: #9ca3af; font-size: 12px;'>
                        This is an automated message from the DNSC NSTP Office. Please do not reply to this email.
                    </p>
                </div>
            ";
            $this->mail->AltBody = "Congratulations $studentName! You have passed $courseInfo. Your serial number is: $serialNumber.";

            // Currently catching exceptions so the app doesn't crash if SMTP is not configured.
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$this->mail->ErrorInfo}");
            return false;
        }
    }
}
