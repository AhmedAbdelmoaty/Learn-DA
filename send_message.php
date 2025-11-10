<?php
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $message = $_POST['message'] ?? '';
    $selected_round_id = !empty($_POST['selected_round_id']) ? intval($_POST['selected_round_id']) : null;
    $lang = (isset($_POST['lang']) && $_POST['lang'] === 'ar') ? 'ar' : 'en';
    
    $selected_round_label = null;
    
    if ($selected_round_id) {
        $round_stmt = $pdo->prepare("SELECT label_en, label_ar FROM course_rounds WHERE id = ?");
        $round_stmt->execute([$selected_round_id]);
        $round = $round_stmt->fetch();
        if ($round) {
            $selected_round_label = $round['label_' . $lang];
        }
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, phone, message, selected_round_id, selected_round_label) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $message, $selected_round_id, $selected_round_label]);
        
        $admin_email_stmt = $pdo->query("SELECT setting_value FROM site_settings WHERE setting_key = 'admin_email'");
        $admin_email_row = $admin_email_stmt->fetch();
        $admin_email = $admin_email_row ? $admin_email_row['setting_value'] : null;
        
        if ($admin_email && filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {
            $subject = $lang === 'ar' ? 'رسالة جديدة من نموذج الاتصال' : 'New Contact Form Message';
            
            $email_body = $lang === 'ar' ? 
                "رسالة جديدة من نموذج الاتصال:\n\n" .
                "الاسم: {$name}\n" .
                "البريد الإلكتروني: {$email}\n" .
                "الهاتف: {$phone}\n" :
                "New message from contact form:\n\n" .
                "Name: {$name}\n" .
                "Email: {$email}\n" .
                "Phone: {$phone}\n";
            
            if ($selected_round_label) {
                $email_body .= $lang === 'ar' ? 
                    "ميعاد الدورة المختار: {$selected_round_label}\n" :
                    "Selected round: {$selected_round_label}\n";
            }
            
            $email_body .= $lang === 'ar' ? 
                "\nالرسالة:\n{$message}" :
                "\nMessage:\n{$message}";
            
            $headers = "From: {$email}\r\n";
            $headers .= "Reply-To: {$email}\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            
            @mail($admin_email, $subject, $email_body, $headers);
        }
        
        $success_message = $lang === 'ar' ? 'تم إرسال رسالتك بنجاح! سنتواصل معك قريباً.' : 'Your message has been sent successfully! We will contact you soon.';
        $button_text = $lang === 'ar' ? 'العودة للصفحة الرئيسية' : 'Back to Homepage';
        
        echo "<!DOCTYPE html>
<html lang='{$lang}' dir='" . ($lang === 'ar' ? 'rtl' : 'ltr') . "'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Message Sent</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>
</head>
<body class='bg-light'>
    <div class='container mt-5'>
        <div class='row justify-content-center'>
            <div class='col-md-6'>
                <div class='card shadow'>
                    <div class='card-body text-center p-5'>
                        <i class='fas fa-check-circle fa-5x text-success mb-4'></i>
                        <h3 class='mb-4'>{$success_message}</h3>
                        <a href='index.php?lang={$lang}' class='btn btn-primary'>
                            <i class='fas fa-home'></i> {$button_text}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>";
        
    } catch(PDOException $e) {
        $error_message = $lang === 'ar' ? 'عذراً، حدث خطأ أثناء إرسال رسالتك. الرجاء المحاولة مرة أخرى.' : 'Sorry, an error occurred while sending your message. Please try again.';
        echo "<!DOCTYPE html>
<html lang='{$lang}' dir='" . ($lang === 'ar' ? 'rtl' : 'ltr') . "'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Error</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body class='bg-light'>
    <div class='container mt-5'>
        <div class='row justify-content-center'>
            <div class='col-md-6'>
                <div class='alert alert-danger'>
                    {$error_message}
                </div>
                <a href='index.php?lang={$lang}' class='btn btn-primary'>Back</a>
            </div>
        </div>
    </div>
</body>
</html>";
    }
} else {
    header('Location: index.php');
    exit;
}
?>
