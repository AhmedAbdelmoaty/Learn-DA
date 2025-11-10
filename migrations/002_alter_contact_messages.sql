-- Add course round fields to contact_messages table (idempotent)
DO $$ 
BEGIN
    -- Add selected_round_id column if it doesn't exist
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_name = 'contact_messages' AND column_name = 'selected_round_id'
    ) THEN
        ALTER TABLE contact_messages ADD COLUMN selected_round_id INT NULL;
    END IF;

    -- Add selected_round_label column if it doesn't exist
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_name = 'contact_messages' AND column_name = 'selected_round_label'
    ) THEN
        ALTER TABLE contact_messages ADD COLUMN selected_round_label VARCHAR(255) NULL;
    END IF;
END $$;

-- Add index for better query performance
CREATE INDEX IF NOT EXISTS idx_contact_messages_round_id ON contact_messages(selected_round_id);
