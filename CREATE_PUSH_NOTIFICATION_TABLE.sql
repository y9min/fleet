-- Create push_notification table for PostgreSQL/Supabase
-- This table stores web push notification subscriptions for users

CREATE TABLE IF NOT EXISTS push_notification (
    id SERIAL PRIMARY KEY,
    user_id UUID REFERENCES users(id) ON DELETE CASCADE,
    user_type VARCHAR(10),
    authtoken VARCHAR(255),
    contentencoding VARCHAR(255),
    endpoint VARCHAR(500),
    publickey VARCHAR(255),
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Create indexes for better query performance
CREATE INDEX IF NOT EXISTS push_notification_user_id_idx ON push_notification(user_id);
CREATE INDEX IF NOT EXISTS push_notification_user_type_idx ON push_notification(user_type);
CREATE INDEX IF NOT EXISTS push_notification_endpoint_idx ON push_notification(endpoint);

-- Add comment to table
COMMENT ON TABLE push_notification IS 'Stores web push notification subscription details for users';

