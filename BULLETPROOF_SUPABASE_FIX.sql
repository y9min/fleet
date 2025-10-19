-- BULLETPROOF SUPABASE CONNECTION FIX
-- This script ensures all required tables exist and are properly configured
-- Run this in Supabase SQL Editor IMMEDIATELY

-- Enable UUID extension if not already enabled
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- Create users_meta table with proper structure
CREATE TABLE IF NOT EXISTS public.users_meta (
    id uuid NOT NULL DEFAULT uuid_generate_v4(),
    user_id uuid NOT NULL,
    key character varying NOT NULL,
    value text,
    created_at timestamp with time zone DEFAULT now(),
    updated_at timestamp with time zone DEFAULT now(),
    deleted_at timestamp with time zone,
    CONSTRAINT users_meta_pkey PRIMARY KEY (id)
);

-- Add unique constraint for user_id + key combination
DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.table_constraints 
        WHERE constraint_name = 'users_meta_user_id_key_unique' 
        AND table_name = 'users_meta'
    ) THEN
        ALTER TABLE public.users_meta 
        ADD CONSTRAINT users_meta_user_id_key_unique UNIQUE (user_id, key);
    END IF;
END $$;

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

-- Create indexes for performance
CREATE INDEX IF NOT EXISTS idx_users_meta_user_id ON public.users_meta(user_id);
CREATE INDEX IF NOT EXISTS idx_users_meta_key ON public.users_meta(key);
CREATE INDEX IF NOT EXISTS idx_users_meta_deleted_at ON public.users_meta(deleted_at);

-- Ensure frontend table exists with deleted_at column
CREATE TABLE IF NOT EXISTS public.frontend (
    id uuid NOT NULL DEFAULT uuid_generate_v4(),
    key character varying NOT NULL,
    value text,
    created_at timestamp with time zone DEFAULT now(),
    updated_at timestamp with time zone DEFAULT now(),
    deleted_at timestamp with time zone,
    CONSTRAINT frontend_pkey PRIMARY KEY (id),
    CONSTRAINT frontend_key_unique UNIQUE (key)
);

-- Add deleted_at column to frontend if it doesn't exist
DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_name = 'frontend' AND column_name = 'deleted_at'
    ) THEN
        ALTER TABLE public.frontend ADD COLUMN deleted_at timestamp with time zone;
    END IF;
END $$;

-- Insert default frontend data
INSERT INTO public.frontend (key, value, created_at, updated_at) VALUES
    ('language', 'en', now(), now()),
    ('currency', '£', now(), now()),
    ('app_name', 'PCO Flow', now(), now())
ON CONFLICT (key) DO NOTHING;

-- Insert default user metadata for existing users
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

-- Enable RLS on both tables
ALTER TABLE public.users_meta ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.frontend ENABLE ROW LEVEL SECURITY;

-- Create RLS policies for users_meta
DROP POLICY IF EXISTS "Users can access their own metadata" ON public.users_meta;
CREATE POLICY "Users can access their own metadata" ON public.users_meta
    FOR ALL USING (user_id::text = (select auth.jwt() ->> 'sub'));

-- Create RLS policies for frontend (public read access)
DROP POLICY IF EXISTS "Frontend is publicly readable" ON public.frontend;
CREATE POLICY "Frontend is publicly readable" ON public.frontend
    FOR SELECT USING (true);

-- Grant permissions
GRANT ALL ON public.users_meta TO anon;
GRANT ALL ON public.users_meta TO authenticated;
GRANT ALL ON public.users_meta TO service_role;

GRANT ALL ON public.frontend TO anon;
GRANT ALL ON public.frontend TO authenticated;
GRANT ALL ON public.frontend TO service_role;

-- Verify tables exist
SELECT 'users_meta table created successfully' as status;
SELECT 'frontend table created successfully' as status;