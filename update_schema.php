<?php
if (!getenv('ALLOW_MAINTENANCE_SCRIPTS')) {
    http_response_code(403);
    die("Forbidden");
}

require_once 'includes/db.php';

echo "Starting database schema update...\n\n";

try {
    // Temporarily disable foreign key checks to simplify table recreation
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0");

    // Drop tables in dependency order
    $pdo->exec("DROP TABLE IF EXISTS content_items");
    $pdo->exec("DROP TABLE IF EXISTS page_sections");
    $pdo->exec("DROP TABLE IF EXISTS footer_settings");
    $pdo->exec("DROP TABLE IF EXISTS topics");
    echo "✓ Existing tables dropped (if present)\n";

    // Re-enable foreign key checks for creation
    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");

    // Topics table (Excel, Power BI, Statistics, SQL)
    $pdo->exec("CREATE TABLE topics (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        slug VARCHAR(100) UNIQUE NOT NULL,
        title_en VARCHAR(255) NOT NULL,
        title_ar VARCHAR(255) NOT NULL,
        intro_en TEXT,
        intro_ar TEXT,
        hero_image VARCHAR(500),
        hero_overlay_color_start VARCHAR(9),
        hero_overlay_color_end VARCHAR(9),
        hero_overlay_opacity_start INT DEFAULT 90,
        hero_overlay_opacity_end INT DEFAULT 90,
        display_order INT DEFAULT 0,
        is_tool TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "✓ Topics table created\n";

    // Content items (Explainers for each topic)
    $pdo->exec("CREATE TABLE content_items (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        topic_id INT UNSIGNED NOT NULL,
        slug VARCHAR(100) NOT NULL,
        title_en VARCHAR(255) NOT NULL,
        title_ar VARCHAR(255) NOT NULL,
        summary_en TEXT,
        summary_ar TEXT,
        body_en TEXT,
        body_ar TEXT,
        cta_note_en TEXT,
        cta_note_ar TEXT,
        hero_image VARCHAR(500),
        status VARCHAR(20) DEFAULT 'published',
        display_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY topic_slug_unique (topic_id, slug),
        CONSTRAINT fk_content_topic FOREIGN KEY (topic_id) REFERENCES topics(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "✓ Content items table created\n";

    // Page sections (for About page and Home page sections)
    $pdo->exec("CREATE TABLE page_sections (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        page_name VARCHAR(50) NOT NULL,
        section_key VARCHAR(50) NOT NULL,
        title_en VARCHAR(255),
        title_ar VARCHAR(255),
        subtitle_en VARCHAR(255),
        subtitle_ar VARCHAR(255),
        body_en TEXT,
        body_ar TEXT,
        image VARCHAR(500),
        is_enabled TINYINT(1) DEFAULT 1,
        display_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY page_section_unique (page_name, section_key)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "✓ Page sections table created\n";

    // Footer settings
    $pdo->exec("CREATE TABLE footer_settings (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) UNIQUE NOT NULL,
        setting_value TEXT,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "✓ Footer settings table created\n";

    echo "\n✅ Database schema updated successfully!\n";
    echo "\nRun this script from command line with: php update_schema.php\n";

} catch (PDOException $e) {
    echo "❌ Error updating schema: " . $e->getMessage() . "\n";
    exit(1);
}
?>
