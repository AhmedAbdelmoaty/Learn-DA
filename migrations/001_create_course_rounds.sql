-- Create course_rounds table (idempotent)
CREATE TABLE IF NOT EXISTS course_rounds (
    id SERIAL PRIMARY KEY,
    label_en VARCHAR(255) NOT NULL,
    label_ar VARCHAR(255) NOT NULL,
    start_at TIMESTAMP NULL,
    published SMALLINT NOT NULL DEFAULT 0,
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Add indexes for better performance
CREATE INDEX IF NOT EXISTS idx_course_rounds_published ON course_rounds(published);
CREATE INDEX IF NOT EXISTS idx_course_rounds_start_at ON course_rounds(start_at);
CREATE INDEX IF NOT EXISTS idx_course_rounds_sort_order ON course_rounds(sort_order);
