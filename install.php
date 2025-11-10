<?php
require_once 'includes/db.php';

if (file_exists('.installed')) {
    die('CMS is already installed. Delete .installed file to reinstall.');
}

try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS admin_users (
            id SERIAL PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS hero_section (
            id SERIAL PRIMARY KEY,
            title_en TEXT,
            title_ar TEXT,
            subtitle_en TEXT,
            subtitle_ar TEXT,
            button_text_en VARCHAR(50),
            button_text_ar VARCHAR(50),
            background_image VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS about_section (
            id SERIAL PRIMARY KEY,
            heading_en TEXT,
            heading_ar TEXT,
            content_en TEXT,
            content_ar TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS benefits (
            id SERIAL PRIMARY KEY,
            icon VARCHAR(50),
            title_en VARCHAR(100),
            title_ar VARCHAR(100),
            description_en TEXT,
            description_ar TEXT,
            display_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS course_details (
            id SERIAL PRIMARY KEY,
            heading_en TEXT,
            heading_ar TEXT,
            duration_en VARCHAR(100),
            duration_ar VARCHAR(100),
            format_en VARCHAR(100),
            format_ar VARCHAR(100),
            fee_en VARCHAR(50),
            fee_ar VARCHAR(50),
            modules_en TEXT,
            modules_ar TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS testimonials (
            id SERIAL PRIMARY KEY,
            name_en VARCHAR(100),
            name_ar VARCHAR(100),
            content_en TEXT,
            content_ar TEXT,
            display_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS faq (
            id SERIAL PRIMARY KEY,
            question_en TEXT,
            question_ar TEXT,
            answer_en TEXT,
            answer_ar TEXT,
            display_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS site_settings (
            id SERIAL PRIMARY KEY,
            setting_key VARCHAR(100) UNIQUE NOT NULL,
            setting_value TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS uploads (
            id SERIAL PRIMARY KEY,
            filename VARCHAR(255) NOT NULL,
            filepath VARCHAR(255) NOT NULL,
            uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS contact_messages (
            id SERIAL PRIMARY KEY,
            name VARCHAR(100),
            email VARCHAR(100),
            phone VARCHAR(50),
            message TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    $hashedPassword = password_hash('123456', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO admin_users (name, email, password) VALUES (?, ?, ?)");
    $stmt->execute(['Admin', 'admin@example.com', $hashedPassword]);

    $stmt = $pdo->prepare("INSERT INTO hero_section (title_en, title_ar, subtitle_en, subtitle_ar, button_text_en, button_text_ar, background_image) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        'Join Our Professional Course Now!',
        'انضم إلى دورتنا الاحترافية الآن!',
        'Start enhancing your skills with our specialized course tailored to the latest industry trends.',
        'ابدأ في تطوير مهاراتك مع دورتنا المتخصصة المصممة وفقًا لأحدث اتجاهات الصناعة.',
        'Enroll Now',
        'سجل الآن',
        'https://via.placeholder.com/1920x600'
    ]);

    $stmt = $pdo->prepare("INSERT INTO about_section (heading_en, heading_ar, content_en, content_ar) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        'Why Our Course?',
        'لماذا دورتنا؟',
        'Our course is designed to provide you with practical skills and knowledge that you can apply immediately in your career. With industry-expert instructors and hands-on projects, you will gain the confidence and expertise needed to excel in your field. We offer flexible learning options and comprehensive support to ensure your success.',
        'تم تصميم دورتنا لتزويدك بالمهارات والمعرفة العملية التي يمكنك تطبيقها فورًا في حياتك المهنية. مع مدربين خبراء في الصناعة ومشاريع عملية، ستكتسب الثقة والخبرة اللازمة للتفوق في مجالك. نحن نقدم خيارات تعليمية مرنة ودعمًا شاملاً لضمان نجاحك.'
    ]);

    $benefits = [
        ['fa-solid fa-laptop-code', 'Practical Learning', 'تدريب عملي', 'Hands-on experience with real-world projects and case studies.', 'خبرة عملية مع مشاريع ودراسات حالة من العالم الحقيقي.', 1],
        ['fa-solid fa-chart-line', 'Career Growth', 'تطوير مهني', 'Advance your career with industry-recognized certification.', 'قم بتطوير حياتك المهنية بشهادة معترف بها في الصناعة.', 2],
        ['fa-solid fa-headset', 'Continuous Support', 'دعم مستمر', 'Get ongoing support from instructors and peers throughout your journey.', 'احصل على دعم مستمر من المدربين والأقران طوال رحلتك.', 3],
        ['fa-solid fa-users', 'Expert Instructors', 'مدربون خبراء', 'Learn from professionals with years of industry experience.', 'تعلم من محترفين لديهم سنوات من الخبرة في الصناعة.', 4]
    ];

    $stmt = $pdo->prepare("INSERT INTO benefits (icon, title_en, title_ar, description_en, description_ar, display_order) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($benefits as $benefit) {
        $stmt->execute($benefit);
    }

    $modules = "Module 1: Introduction to the Field\nModule 2: Fundamentals and Core Concepts\nModule 3: Advanced Techniques\nModule 4: Practical Applications\nModule 5: Industry Best Practices\nModule 6: Final Project and Certification";
    $modules_ar = "الوحدة 1: مقدمة في المجال\nالوحدة 2: الأساسيات والمفاهيم الأساسية\nالوحدة 3: التقنيات المتقدمة\nالوحدة 4: التطبيقات العملية\nالوحدة 5: أفضل ممارسات الصناعة\nالوحدة 6: المشروع النهائي والشهادة";

    $stmt = $pdo->prepare("INSERT INTO course_details (heading_en, heading_ar, duration_en, duration_ar, format_en, format_ar, fee_en, fee_ar, modules_en, modules_ar) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        'Course Outline',
        'المنهج الدراسي',
        '8 weeks, 50 hours total',
        '8 أسابيع، 50 ساعة إجمالاً',
        'Online live sessions',
        'جلسات مباشرة عبر الإنترنت',
        '10,000 EGP',
        '10,000 جنيه مصري',
        $modules,
        $modules_ar
    ]);

    $testimonials = [
        ['Sarah Johnson', 'سارة جونسون', 'This course exceeded my expectations! The instructors were knowledgeable and the content was practical and relevant.', 'هذه الدورة تجاوزت توقعاتي! كان المدربون على دراية وكان المحتوى عمليًا وذا صلة.', 1],
        ['Ahmed Hassan', 'أحمد حسن', 'I highly recommend this course to anyone looking to advance their career. The support and resources provided were exceptional.', 'أوصي بشدة بهذه الدورة لأي شخص يتطلع إلى تطوير مسيرته المهنية. كان الدعم والموارد المقدمة استثنائية.', 2],
        ['Maria Garcia', 'ماريا غارسيا', 'The hands-on projects really helped me understand the concepts better. Great learning experience!', 'ساعدتني المشاريع العملية حقًا على فهم المفاهيم بشكل أفضل. تجربة تعليمية رائعة!', 3]
    ];

    $stmt = $pdo->prepare("INSERT INTO testimonials (name_en, name_ar, content_en, content_ar, display_order) VALUES (?, ?, ?, ?, ?)");
    foreach ($testimonials as $testimonial) {
        $stmt->execute($testimonial);
    }

    $faqs = [
        ['Who is this course for?', 'لمن هذه الدورة؟', 'This course is designed for beginners and professionals looking to upskill and advance their careers in the industry.', 'هذه الدورة مصممة للمبتدئين والمحترفين الذين يتطلعون إلى تطوير مهاراتهم وتقديم حياتهم المهنية في الصناعة.', 1],
        ['Do I get a certificate?', 'هل سأحصل على شهادة؟', 'Yes, upon successful completion of the course, you will receive an industry-recognized certificate.', 'نعم، عند إتمام الدورة بنجاح، ستحصل على شهادة معترف بها في الصناعة.', 2],
        ['What are the prerequisites?', 'ما هي المتطلبات المسبقة؟', 'No prior experience is required. However, basic computer skills and enthusiasm to learn are recommended.', 'لا يلزم خبرة سابقة. ومع ذلك، يوصى بمهارات الكمبيوتر الأساسية والحماس للتعلم.', 3],
        ['Is there any support available?', 'هل يوجد أي دعم متاح؟', 'Yes, we provide continuous support through instructors, discussion forums, and dedicated help desk.', 'نعم، نحن نقدم دعمًا مستمرًا من خلال المدربين ومنتديات النقاش ومكتب المساعدة المخصص.', 4]
    ];

    $stmt = $pdo->prepare("INSERT INTO faq (question_en, question_ar, answer_en, answer_ar, display_order) VALUES (?, ?, ?, ?, ?)");
    foreach ($faqs as $faq) {
        $stmt->execute($faq);
    }

    $settings = [
        ['site_name_en', 'Professional Training Institute'],
        ['site_name_ar', 'معهد التدريب المهني'],
        ['logo', 'https://via.placeholder.com/150x50?text=LOGO'],
        ['primary_color', '#a8324e'],
        ['contact_email', 'info@institute.com'],
        ['contact_phone', '+971 50 418 0021'],
        ['contact_address_en', 'E311 Road, New Industrial Area, Sharjah, UAE'],
        ['contact_address_ar', 'طريق E311، المنطقة الصناعية الجديدة، الشارقة، الإمارات العربية المتحدة'],
        ['facebook_url', 'https://facebook.com'],
        ['twitter_url', 'https://twitter.com'],
        ['linkedin_url', 'https://linkedin.com'],
        ['instagram_url', 'https://instagram.com']
    ];

    $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)");
    foreach ($settings as $setting) {
        $stmt->execute($setting);
    }

    file_put_contents('.installed', date('Y-m-d H:i:s'));

    echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Installation Complete</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body class='bg-light'>
    <div class='container mt-5'>
        <div class='row justify-content-center'>
            <div class='col-md-6'>
                <div class='card shadow'>
                    <div class='card-header bg-success text-white'>
                        <h3 class='mb-0'>Installation Successful!</h3>
                    </div>
                    <div class='card-body'>
                        <h5>CMS has been installed successfully.</h5>
                        <hr>
                        <h6>Default Admin Credentials:</h6>
                        <p>
                            <strong>Email:</strong> admin@example.com<br>
                            <strong>Password:</strong> 123456
                        </p>
                        <div class='alert alert-warning'>
                            <strong>Important:</strong> Please change your password after first login!
                        </div>
                        <a href='admin/login.php' class='btn btn-primary'>Go to Admin Login</a>
                        <a href='index.php' class='btn btn-secondary'>View Website</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>";

} catch(PDOException $e) {
    die("Installation failed: " . $e->getMessage());
}
?>
