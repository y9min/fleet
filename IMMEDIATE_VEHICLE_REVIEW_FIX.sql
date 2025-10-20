-- IMMEDIATE FIX FOR VEHICLE_REVIEW TABLE ERROR
-- Run this FIRST in Supabase SQL Editor to fix the current login error

-- Create the vehicle_review table immediately
CREATE TABLE IF NOT EXISTS vehicle_review (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    vehicle_id UUID NOT NULL,
    user_id UUID,
    reg_no VARCHAR(255),
    kms_outgoing INTEGER,
    kms_incoming INTEGER,
    fuel_level_out INTEGER,
    fuel_level_in INTEGER,
    datetime_outgoing TIMESTAMP,
    datetime_incoming TIMESTAMP,
    petrol_card TEXT,
    lights TEXT,
    invertor TEXT,
    car_mats TEXT,
    int_damage TEXT,
    int_lights TEXT,
    ext_car TEXT,
    tyre TEXT,
    ladder TEXT,
    leed TEXT,
    power_tool TEXT,
    ac TEXT,
    head_light TEXT,
    lock TEXT,
    windows TEXT,
    condition TEXT,
    oil_chk TEXT,
    suspension TEXT,
    tool_box TEXT,
    image VARCHAR(255),
    udf VARCHAR(255),
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    deleted_at TIMESTAMP
);

-- Add foreign key constraints (only if referenced tables exist)
DO $$
BEGIN
    -- Check if vehicles table exists and add constraint
    IF EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = 'vehicles' AND table_schema = 'public') THEN
        ALTER TABLE vehicle_review 
        ADD CONSTRAINT fk_vehicle_review_vehicle_id 
        FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE;
    END IF;
    
    -- Check if users table exists and add constraint
    IF EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = 'users' AND table_schema = 'public') THEN
        ALTER TABLE vehicle_review 
        ADD CONSTRAINT fk_vehicle_review_user_id 
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL;
    END IF;
END $$;

-- Create indexes for performance
CREATE INDEX IF NOT EXISTS idx_vehicle_review_vehicle_id_user_id ON vehicle_review(vehicle_id, user_id);
CREATE INDEX IF NOT EXISTS idx_vehicle_review_deleted_at ON vehicle_review(deleted_at);

-- Verify the table was created
SELECT 
    '✅ VEHICLE_REVIEW TABLE CREATED SUCCESSFULLY!' as status,
    'The login error should now be resolved' as message,
    COUNT(*) as table_exists
FROM information_schema.tables 
WHERE table_name = 'vehicle_review' 
AND table_schema = 'public';
