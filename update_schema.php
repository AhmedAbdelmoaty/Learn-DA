<?php
require_once 'includes/db.php';

echo "Starting database schema update...\n\n";

try {
    // Drop old topics table if it exists with wrong schema
    $pdo->exec("DROP TABLE IF EXISTS topics CASCADE");
    echo "✓ Old topics table dropped\n";
    
    // Topics table (Excel, Power BI, Statistics, SQL)
    $pdo->exec("CREATE TABLE topics (
        id SERIAL PRIMARY KEY,
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
        is_tool BOOLEAN DEFAULT false,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✓ Topics table created\n";

    // Drop and recreate content_items table
    $pdo->exec("DROP TABLE IF EXISTS content_items CASCADE");
    echo "✓ Old content_items table dropped\n";
    
    // Content items (Explainers for each topic)
    $pdo->exec("CREATE TABLE content_items (
        id SERIAL PRIMARY KEY,
        topic_id INT REFERENCES topics(id) ON DELETE CASCADE,
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
        UNIQUE(topic_id, slug)
    )");
    echo "✓ Content items table created\n";

    // Drop and recreate page_sections table
    $pdo->exec("DROP TABLE IF EXISTS page_sections CASCADE");
    echo "✓ Old page_sections table dropped\n";
    
    // Page sections (for About page and Home page sections)
    $pdo->exec("CREATE TABLE page_sections (
        id SERIAL PRIMARY KEY,
        page_name VARCHAR(50) NOT NULL,
        section_key VARCHAR(50) NOT NULL,
        title_en VARCHAR(255),
        title_ar VARCHAR(255),
        subtitle_en VARCHAR(255),
        subtitle_ar VARCHAR(255),
        body_en TEXT,
        body_ar TEXT,
        image VARCHAR(500),
        is_enabled BOOLEAN DEFAULT true,
        display_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE(page_name, section_key)
    )");
    echo "✓ Page sections table created\n";

    // Drop and recreate footer_settings table
    $pdo->exec("DROP TABLE IF EXISTS footer_settings CASCADE");
    echo "✓ Old footer_settings table dropped\n";
    
    // Footer settings
    $pdo->exec("CREATE TABLE footer_settings (
        id SERIAL PRIMARY KEY,
        setting_key VARCHAR(100) UNIQUE NOT NULL,
        setting_value TEXT,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✓ Footer settings table created\n";

    echo "\n✅ Database schema updated successfully!\n";
    echo "\nRun this script from command line with: php update_schema.php\n";

} catch (PDOException $e) {
    echo "❌ Error updating schema: " . $e->getMessage() . "\n";
    exit(1);
}
?>
