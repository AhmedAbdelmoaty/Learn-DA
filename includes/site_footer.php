<!-- Footer -->
<footer class="footer text-white py-5 mt-5" style="background: linear-gradient(180deg, #a8324e 0%, #8b2a42 100%);">
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
                    <?php if (!empty($settings['instagram_url'])): ?>
                        <a href="<?php echo htmlspecialchars($settings['instagram_url']); ?>" 
                           class="social-icon-link" target="_blank" rel="noopener">
                            <i class="fab fa-instagram"></i>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($settings['linkedin_url'])): ?>
                        <a href="<?php echo htmlspecialchars($settings['linkedin_url']); ?>" 
                           class="social-icon-link" target="_blank" rel="noopener">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($settings['facebook_url'])): ?>
                        <a href="<?php echo htmlspecialchars($settings['facebook_url']); ?>" 
                           class="social-icon-link" target="_blank" rel="noopener">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($settings['twitter_url'])): ?>
                        <a href="<?php echo htmlspecialchars($settings['twitter_url']); ?>" 
                           class="social-icon-link" target="_blank" rel="noopener">
                            <i class="fab fa-x-twitter"></i>
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
                        <a href="<?php echo preserveLang('excel.php', $lang); ?>" class="text-white text-decoration-none">
                            <?php echo t('excel', $lang); ?>
                        </a>
                    </li>
                    <li class="mb-3">
                        <a href="<?php echo preserveLang('power-bi.php', $lang); ?>" class="text-white text-decoration-none">
                            <?php echo t('power_bi', $lang); ?>
                        </a>
                    </li>
                    <li class="mb-3">
                        <a href="<?php echo preserveLang('statistics.php', $lang); ?>" class="text-white text-decoration-none">
                            <?php echo t('statistics', $lang); ?>
                        </a>
                    </li>
                    <li class="mb-3">
                        <a href="<?php echo preserveLang('sql.php', $lang); ?>" class="text-white text-decoration-none">
                            <?php echo t('sql', $lang); ?>
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Column 3: Contact Info -->
            <div class="col-md-4 mb-4">
                <h5 class="mb-4 fw-bold text-uppercase"><?php echo $lang === 'ar' ? 'معلومات التواصل' : 'CONTACT INFO'; ?></h5>
                
                <!-- U.A.E. Address -->
                <div class="contact-block mb-4">
                    <div class="d-flex align-items-start mb-2">
                        <i class="fas fa-map-marker-alt me-2 mt-1" style="font-size: 14px;"></i>
                        <div>
                            <strong><?php echo $lang === 'ar' ? 'الإمارات العربية المتحدة' : 'U.A.E.'; ?></strong><br>
                            <span style="font-size: 14px; line-height: 1.6;">
                                <?php 
                                echo htmlspecialchars($footer_settings['uae_address_' . $lang] ?? 
                                    ($lang === 'ar' 
                                        ? 'مركز الأعمال، نشر الشارقة، المنطقة الحرة، E311، الشيخ محمد بن زايد رود، الزاهية، الشارقة، الإمارات' 
                                        : 'Business Center, Sharjah Publishing City Free Zone, E311, Sheikh Mohammed Bin Zayed Rd, Al Zahia, Sharjah, U.A.E.'
                                    )
                                ); 
                                ?>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-phone me-2" style="font-size: 14px;"></i>
                        <a href="tel:+97150418002" class="text-white text-decoration-none" style="font-size: 14px;">
                            <?php echo htmlspecialchars($footer_settings['uae_phone'] ?? '+971 50 418 0021'); ?>
                        </a>
                    </div>
                </div>
                
                <!-- Egypt Address -->
                <div class="contact-block mb-4">
                    <div class="d-flex align-items-start mb-2">
                        <i class="fas fa-map-marker-alt me-2 mt-1" style="font-size: 14px;"></i>
                        <div>
                            <strong><?php echo $lang === 'ar' ? 'مصر' : 'Egypt'; ?></strong><br>
                            <span style="font-size: 14px; line-height: 1.6;">
                                <?php 
                                echo htmlspecialchars($footer_settings['egypt_address_' . $lang] ?? 
                                    ($lang === 'ar' 
                                        ? '37 عمان ش، الطابق الرابع، الدقي، الجيزة، مصر' 
                                        : '37 Amman St, Fourth Floor, Eldokki, Giza, Egypt'
                                    )
                                ); 
                                ?>
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-phone me-2" style="font-size: 14px;"></i>
                        <a href="tel:+201032244125" class="text-white text-decoration-none" style="font-size: 14px;">
                            <?php echo htmlspecialchars($footer_settings['egypt_phone'] ?? '+20 10 32244125'); ?>
                        </a>
                    </div>
                </div>
                
                <!-- Email -->
                <div class="d-flex align-items-center">
                    <i class="fas fa-envelope me-2" style="font-size: 14px;"></i>
                    <a href="mailto:<?php echo htmlspecialchars($footer_settings['contact_email'] ?? 'marketing@imanagementpro.com'); ?>" 
                       class="text-white text-decoration-none" style="font-size: 14px;">
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
        
        .footer .d-flex.align-items-start,
        .footer .d-flex.align-items-center {
            justify-content: center;
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
