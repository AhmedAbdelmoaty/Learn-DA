<?php
// Translation function
function t($key, $lang, $settings = []) {
    $translations = [
        'en' => [
            'home' => 'Home',
            'about' => 'About Us',
            'training_program' => 'Training Program',
            'excel' => 'Excel',
            'power_bi' => 'Power BI',
            'statistics' => 'Statistics',
            'sql' => 'SQL',
            'contact' => 'Contact',
            'contact_us' => 'Contact Us',
            'send_message' => 'Send Message',
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'message' => 'Message',
            'submit' => 'Submit',
            'enroll_now' => 'Enroll Now',
            'read_more' => 'Read More',
            'explore_topics' => 'Explore Our Topics',
            'why_data_analysis' => 'Why Data Analysis?',
            'benefits' => 'Key Benefits',
            'testimonials' => 'What Our Students Say',
            'faq' => 'FAQ',
            'site_links' => 'Site Links',
            'follow_us' => 'Follow Us',
            'copyright' => '© ' . date('Y') . ' ' . ($settings['site_name_en'] ?? 'Learn Data Analysis') . '. All rights reserved.',
            'related_content' => 'Related Content',
            'back_to' => 'Back to',
            'choose_course_date' => 'Choose the course date',
            'no_rounds_available' => 'No upcoming rounds available at the moment.',
            'what_you_learn' => 'What You\'ll Learn',
            'value_bonuses' => 'Value & Bonuses',
            'student_outcomes' => 'Student Outcomes',
            'top_questions' => 'Top Questions',
            'all_questions' => 'All Questions',
        ],
        'ar' => [
            'home' => 'الرئيسية',
            'about' => 'من نحن',
            'training_program' => 'البرنامج التدريبي',
            'excel' => 'إكسل',
            'power_bi' => 'باور بي آي',
            'statistics' => 'الإحصاء',
            'sql' => 'SQL',
            'contact' => 'اتصل بنا',
            'contact_us' => 'تواصل معنا',
            'send_message' => 'إرسال رسالة',
            'name' => 'الاسم',
            'email' => 'البريد الإلكتروني',
            'phone' => 'رقم الهاتف',
            'message' => 'الرسالة',
            'submit' => 'إرسال',
            'enroll_now' => 'سجل الآن',
            'read_more' => 'اقرأ المزيد',
            'explore_topics' => 'استكشف مواضيعنا',
            'why_data_analysis' => 'لماذا تحليل البيانات؟',
            'benefits' => 'الفوائد الرئيسية',
            'testimonials' => 'ماذا يقول طلابنا',
            'faq' => 'الأسئلة الشائعة',
            'site_links' => 'روابط الموقع',
            'follow_us' => 'تابعنا',
            'copyright' => '© ' . date('Y') . ' ' . ($settings['site_name_ar'] ?? 'تعلم تحليل البيانات') . '. جميع الحقوق محفوظة.',
            'related_content' => 'محتوى ذو صلة',
            'back_to' => 'العودة إلى',
            'choose_course_date' => 'اختَر موعد الدورة',
            'no_rounds_available' => 'لا توجد مواعيد متاحة حاليًا.',
            'what_you_learn' => 'ماذا ستتعلم',
            'value_bonuses' => 'قيمة مضافة ومزايا',
            'student_outcomes' => 'لماذا هذا البرنامج',
            'top_questions' => 'أهم الأسئلة',
            'all_questions' => 'جميع الأسئلة',
        ]
    ];
    return $translations[$lang][$key] ?? $key;
}

// Get current page name from URL
function getCurrentPage() {
    $page = basename($_SERVER['PHP_SELF'], '.php');
    return $page;
}

// Preserve language parameter in URLs
function preserveLang($url, $lang) {
    $separator = (strpos($url, '?') !== false) ? '&' : '?';
    return $url . $separator . 'lang=' . $lang;
}
?>
