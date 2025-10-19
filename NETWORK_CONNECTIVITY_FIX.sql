-- This script creates the missing users_meta table that Laravel expects
-- Run this in Supabase SQL Editor

-- Create users_meta table if it doesn't exist
CREATE TABLE IF NOT EXISTS public.users_meta (
    id uuid NOT NULL DEFAULT uuid_generate_v4(),
    user_id uuid NOT NULL,
    key character varying NOT NULL,
    value text,
    created_at timestamp with time zone DEFAULT now(),
    updated_at timestamp with time zone DEFAULT now(),
    deleted_at timestamp with time zone,
    CONSTRAINT users_meta_pkey PRIMARY KEY (id),
    CONSTRAINT users_meta_user_id_key_unique UNIQUE (user_id, key)
);

-- Add foreign key constraint to users table
DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.table_constraints 
        WHERE constraint_name = 'users_meta_user_id_fkey' 
        AND table_name = 'users_meta'
    ) THEN
        ALTER TABLE public.users_meta 
        ADD CONSTRAINT users_meta_user_id_fkey 
        FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;
    END IF;
END $$;

-- Create index for better performance
CREATE INDEX IF NOT EXISTS idx_users_meta_user_id ON public.users_meta(user_id);
CREATE INDEX IF NOT EXISTS idx_users_meta_key ON public.users_meta(key);
CREATE INDEX IF NOT EXISTS idx_users_meta_deleted_at ON public.users_meta(deleted_at);

-- Insert some default user metadata
INSERT INTO public.users_meta (user_id, key, value, created_at, updated_at) 
SELECT 
    u.id,
    'LANGUAGE',
    'en',
    now(),
    now()
FROM public.users u
WHERE NOT EXISTS (
    SELECT 1 FROM public.users_meta um 
    WHERE um.user_id = u.id AND um.key = 'LANGUAGE'
);

-- Enable RLS
ALTER TABLE public.users_meta ENABLE ROW LEVEL SECURITY;

-- Create RLS policy for users_meta
DROP POLICY IF EXISTS "Users can access their own metadata" ON public.users_meta;
CREATE POLICY "Users can access their own metadata" ON public.users_meta
    FOR ALL USING (user_id::text = (select auth.jwt() ->> 'sub'));

-- Grant permissions
GRANT ALL ON public.users_meta TO anon;
GRANT ALL ON public.users_meta TO authenticated;
GRANT ALL ON public.users_meta TO service_role;
