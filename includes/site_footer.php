<?php
$footer_social_instagram = $footer_settings['social_instagram_url'] ?? ($settings['instagram_url'] ?? '');
$footer_social_linkedin  = $footer_settings['social_linkedin_url'] ?? ($settings['linkedin_url'] ?? '');
$footer_social_facebook  = $footer_settings['social_facebook_url'] ?? ($settings['facebook_url'] ?? '');
$footer_social_x         = $footer_settings['social_x_url'] ?? ($settings['twitter_url'] ?? '');
?>
<!-- Footer -->
<footer class="footer text-white py-5 mt-5" style="background: linear-gradient(180deg, var(--primary-gradient-start) 0%, var(--primary-gradient-end) 100%);">
    <div class="container">
        <div class="row">
            <!-- Column 1: About IMP -->
            <div class="col-md-4 mb-4">
                <h5 class="mb-3 fw-bold text-uppercase"><?php echo $lang === 'ar' ? 'عن المعهد' : 'ABOUT IMP'; ?></h5>
                <p class="mb-4" style="line-height: 1.8;">
                    <?php 
                    echo htmlspecialchars($footer_settings['footer_about_' . $lang] ?? 
                        ($lang === 'ar' 
                            ? 'معهد إدارة المحترفين (IMP) تأسس في 2014 ليكون رائداً في التدريب والتطوير في الشرق الأوسط وشمال أفريقيا. نركز على تطوير الموارد البشرية من خلال الاستخدام الاستراتيجي لأدوات الذكاء الاصطناعي.' 
                            : 'IMP (Institute of Management Professionals) was established in 2014 to be a leading training and development house in the Middle East and North Africa region. Our core emphasis is in the activation of human resources through the strategic utilization of artificial intelligence tools.'
                        )
                    ); 
                    ?>
                </p>
                
                <!-- Social Media Icons -->
                <div class="social-icons d-flex gap-2 mb-3">
                    <?php if (!empty($footer_social_instagram)): ?>
                        <a href="<?php echo htmlspecialchars($footer_social_instagram); ?>"
                           class="social-icon-link" target="_blank" rel="noopener" aria-label="Instagram">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                    <?php endif; ?>
                        <?php if (!empty($footer_social_linkedin)): ?>
                            <a href="<?php echo htmlspecialchars($footer_social_linkedin); ?>"
                               class="social-icon-link" target="_blank" rel="noopener" aria-label="LinkedIn">
                                <i class="fa-brands fa-linkedin-in"></i>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($footer_social_facebook)): ?>
                        <a href="<?php echo htmlspecialchars($footer_social_facebook); ?>"
                            class="social-icon-link" target="_blank" rel="noopener" aria-label="Facebook">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                    <?php endif; ?>
                        <?php if (!empty($footer_social_x)): ?>
                            <a href="<?php echo htmlspecialchars($footer_social_x); ?>"
                               class="social-icon-link" target="_blank" rel="noopener" aria-label="X (Twitter)">
                                <i class="fa-brands fa-x-twitter"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Column 2: Quick Links -->
            <div class="col-md-4 mb-4">
                <h5 class="mb-3 fw-bold text-uppercase invisible">Links</h5>
                <ul class="list-unstyled footer-links">
                    <li class="mb-3">
                        <a href="<?php echo preserveLang('index.php', $lang); ?>" class="text-white text-decoration-none">
                            <?php echo t('home', $lang); ?>
                        </a>
                    </li>
                    <li class="mb-3">
                        <a href="<?php echo preserveLang('about.php', $lang); ?>" class="text-white text-decoration-none">
                            <?php echo $lang === 'ar' ? 'عن المعهد' : 'About Us'; ?>
                        </a>
                    </li>
                    <li class="mb-3">
                        <a href="<?php echo preserveLang('training-program.php', $lang); ?>" class="text-white text-decoration-none">
                            <?php echo t('training_program', $lang); ?>
                        </a>
                    </li>
                    <li class="mb-3">
                        <a href="<?php echo preserveLang('tools.php', $lang); ?>" class="text-white text-decoration-none">
                            <?php echo t('tools', $lang); ?>
                        </a>
                    </li>
                    <li class="mb-3">
                        <a href="<?php echo preserveLang('faq.php', $lang); ?>" class="text-white text-decoration-none">
                            <?php echo t('faq', $lang); ?>
                        </a>
                    </li>    
                    <li class="mb-3">
                        <a href="<?php echo preserveLang('statistics.php', $lang); ?>" class="text-white text-decoration-none">
                            <?php echo t('statistics', $lang); ?>
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Column 3: Contact Info -->
            <div class="col-md-4 mb-4">
                <h5 class="mb-4 fw-bold text-uppercase"><?php echo $lang === 'ar' ? 'معلومات التواصل' : 'CONTACT INFO'; ?></h5>
                
                <!-- U.A.E. Address -->
                <div class="contact-block mb-4">
                     <div class="contact-item address-item mb-2">
                         <div class="address-heading">
                             <span class="contact-icon">
                                 <i class="fas fa-map-marker-alt"></i>
                            </span>
                             <strong><?php echo $lang === 'ar' ? 'الإمارات العربية المتحدة' : 'U.A.E.'; ?></strong>
                             </div>
                             <div class="contact-address">
                                 <?php
                                 echo htmlspecialchars($footer_settings['uae_address_' . $lang] ??
                                     ($lang === 'ar'
                                         ? 'مركز الأعمال، نشر الشارقة، المنطقة الحرة، E311، الشيخ محمد بن زايد رود، الزاهية، الشارقة، الإمارات'
                                         : 'Business Center, Sharjah Publishing City Free Zone, E311, Sheikh Mohammed Bin Zayed Rd, Al Zahia, Sharjah, U.A.E.'
                                     )
                                 );
                                 ?>
                        </div>
                    </div>
                    <div class="contact-item inline-align mb-2">
                        <span class="contact-icon">
                            <i class="fa-brands fa-whatsapp"></i>
                        </span>
                        <a href="tel:+97150418002" class="contact-link text-white text-decoration-none text-ltr" dir="ltr">
                            <?php echo htmlspecialchars($footer_settings['uae_phone'] ?? '+971 50 418 0021'); ?>
                        </a>
                    </div>
                </div>
                
                <!-- Egypt Address -->
                <div class="contact-block mb-4">
                    <div class="contact-item address-item mb-2">
                        <div class="address-heading">
                            <span class="contact-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </span>
                            <strong><?php echo $lang === 'ar' ? 'جمهورية مصر العربية' : 'Egypt'; ?></strong>
                            </div>
                            <div class="contact-address">
                                <?php
                                echo htmlspecialchars($footer_settings['egypt_address_' . $lang] ??
                                    ($lang === 'ar'
                                        ? '37 عمان ش، الطابق الرابع، الدقي، الجيزة، مصر'
                                        : '37 Amman St, Fourth Floor, Eldokki, Giza, Egypt'
                                    )
                                );
                                ?>
                        </div>
                    </div>
                    <div class="contact-item inline-align mb-3">
                        <span class="contact-icon">
                            <i class="fa-brands fa-whatsapp"></i>
                        </span>
                        <a href="tel:+201032244125" class="contact-link text-white text-decoration-none text-ltr" dir="ltr">
                            <?php echo htmlspecialchars($footer_settings['egypt_phone'] ?? '+20 10 32244125'); ?>
                        </a>
                    </div>
                </div>
                
                <!-- Email -->
                <div class="contact-item inline-align">
                    <span class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <a href="mailto:<?php echo htmlspecialchars($footer_settings['contact_email'] ?? 'marketing@imanagementpro.com'); ?>"
                       class="contact-link text-white text-decoration-none">
                        <?php echo htmlspecialchars($footer_settings['contact_email'] ?? 'marketing@imanagementpro.com'); ?>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Bottom Copyright Bar -->
        <hr class="my-4" style="border-color: rgba(255,255,255,0.2); opacity: 1;">
        <div class="row">
            <div class="col-12 text-center">
                <p class="mb-0" style="font-size: 14px;">
                    <?php 
                    if ($lang === 'ar') {
                        echo '© 2025 معهد إدارة المحترفين. جميع الحقوق محفوظة.';
                    } else {
                        echo '© 2025 Institute of Management Professionals. All rights reserved.';
                    }
                    ?>
                </p>
            </div>
        </div>
    </div>
</footer>

<!-- Custom Footer Styles -->
<style>
    .social-icon-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 50%;
        color: white;
        text-decoration: none;
        font-size: 16px;
        transition: all 0.3s ease;
    }
    
    .social-icon-link:hover {
        opacity: 0.7;
        color: white;
        border-color: rgba(255, 255, 255, 0.8);
        transform: translateY(-2px);
    }
    
    .footer-links li a {
        transition: opacity 0.3s ease;
    }
    
    .footer-links li a:hover {
        opacity: 0.8;
    }
    
    .contact-block strong {
        font-size: 15px;
    }
    
    @media (max-width: 768px) {
        .footer .col-md-4 {
            text-align: center;
        }
        
        .footer .social-icons {
            justify-content: center;
        }
        
        .footer .contact-item {
            justify-content: center;
            text-align: center;
        }
        .footer .contact-item.address-item .contact-address {
            text-align: center;
        }
        .footer-links {
            text-align: center;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>
