-- CREATE MISSING TABLES FOR SUPABASE POSTGRESQL
-- This script creates all missing tables that Laravel expects

-- Create testimonials table
CREATE TABLE IF NOT EXISTS public.testimonials (
    id uuid NOT NULL DEFAULT uuid_generate_v4(),
    company_id uuid,
    name character varying NOT NULL,
    designation character varying,
    content text NOT NULL,
    rating integer CHECK (rating >= 1 AND rating <= 5),
    image character varying,
    is_active boolean DEFAULT true,
    created_at timestamp with time zone DEFAULT now(),
    updated_at timestamp with time zone DEFAULT now(),
    deleted_at timestamp with time zone,
    CONSTRAINT testimonials_pkey PRIMARY KEY (id),
    CONSTRAINT testimonials_company_id_fkey FOREIGN KEY (company_id) REFERENCES public.companies(id)
);

-- Create team table
CREATE TABLE IF NOT EXISTS public.team (
    id uuid NOT NULL DEFAULT uuid_generate_v4(),
    company_id uuid,
    name character varying NOT NULL,
    designation character varying NOT NULL,
    description text,
    image character varying,
    facebook_url character varying,
    twitter_url character varying,
    linkedin_url character varying,
    instagram_url character varying,
    is_active boolean DEFAULT true,
    created_at timestamp with time zone DEFAULT now(),
    updated_at timestamp with time zone DEFAULT now(),
    deleted_at timestamp with time zone,
    CONSTRAINT team_pkey PRIMARY KEY (id),
    CONSTRAINT team_company_id_fkey FOREIGN KEY (company_id) REFERENCES public.companies(id)
);

-- Create service_items table
CREATE TABLE IF NOT EXISTS public.service_items (
    id uuid NOT NULL DEFAULT uuid_generate_v4(),
    company_id uuid,
    title character varying NOT NULL,
    description text,
    icon character varying,
    is_active boolean DEFAULT true,
    sort_order integer DEFAULT 0,
    created_at timestamp with time zone DEFAULT now(),
    updated_at timestamp with time zone DEFAULT now(),
    deleted_at timestamp with time zone,
    CONSTRAINT service_items_pkey PRIMARY KEY (id),
    CONSTRAINT service_items_company_id_fkey FOREIGN KEY (company_id) REFERENCES public.companies(id)
);

-- Create notes table
CREATE TABLE IF NOT EXISTS public.notes (
    id uuid NOT NULL DEFAULT uuid_generate_v4(),
    company_id uuid,
    vehicle_id uuid,
    user_id uuid,
    title character varying NOT NULL,
    description text,
    note_type character varying DEFAULT 'general',
    is_important boolean DEFAULT false,
    created_at timestamp with time zone DEFAULT now(),
    updated_at timestamp with time zone DEFAULT now(),
    deleted_at timestamp with time zone,
    CONSTRAINT notes_pkey PRIMARY KEY (id),
    CONSTRAINT notes_company_id_fkey FOREIGN KEY (company_id) REFERENCES public.companies(id),
    CONSTRAINT notes_vehicle_id_fkey FOREIGN KEY (vehicle_id) REFERENCES public.vehicles(id),
    CONSTRAINT notes_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id)
);

-- Create cancellation_reasons table
CREATE TABLE IF NOT EXISTS public.cancellation_reasons (
    id uuid NOT NULL DEFAULT uuid_generate_v4(),
    company_id uuid,
    reason character varying NOT NULL,
    description text,
    is_active boolean DEFAULT true,
    created_at timestamp with time zone DEFAULT now(),
    updated_at timestamp with time zone DEFAULT now(),
    deleted_at timestamp with time zone,
    CONSTRAINT cancellation_reasons_pkey PRIMARY KEY (id),
    CONSTRAINT cancellation_reasons_company_id_fkey FOREIGN KEY (company_id) REFERENCES public.companies(id)
);

-- Create income_categories table
CREATE TABLE IF NOT EXISTS public.income_categories (
    id uuid NOT NULL DEFAULT uuid_generate_v4(),
    company_id uuid,
    name character varying NOT NULL,
    description text,
    is_active boolean DEFAULT true,
    created_at timestamp with time zone DEFAULT now(),
    updated_at timestamp with time zone DEFAULT now(),
    deleted_at timestamp with time zone,
    CONSTRAINT income_categories_pkey PRIMARY KEY (id),
    CONSTRAINT income_categories_company_id_fkey FOREIGN KEY (company_id) REFERENCES public.companies(id)
);

-- Create expense_categories table
CREATE TABLE IF NOT EXISTS public.expense_categories (
    id uuid NOT NULL DEFAULT uuid_generate_v4(),
    company_id uuid,
    name character varying NOT NULL,
    description text,
    is_active boolean DEFAULT true,
    created_at timestamp with time zone DEFAULT now(),
    updated_at timestamp with time zone DEFAULT now(),
    deleted_at timestamp with time zone,
    CONSTRAINT expense_categories_pkey PRIMARY KEY (id),
    CONSTRAINT expense_categories_company_id_fkey FOREIGN KEY (company_id) REFERENCES public.companies(id)
);

-- Create fare_settings table
CREATE TABLE IF NOT EXISTS public.fare_settings (
    id uuid NOT NULL DEFAULT uuid_generate_v4(),
    company_id uuid,
    key_name character varying NOT NULL,
    key_value text,
    created_at timestamp with time zone DEFAULT now(),
    updated_at timestamp with time zone DEFAULT now(),
    CONSTRAINT fare_settings_pkey PRIMARY KEY (id),
    CONSTRAINT fare_settings_company_id_fkey FOREIGN KEY (company_id) REFERENCES public.companies(id)
);

-- Create api_settings table
CREATE TABLE IF NOT EXISTS public.api_settings (
    id uuid NOT NULL DEFAULT uuid_generate_v4(),
    company_id uuid,
    key_name character varying NOT NULL,
    key_value text,
    created_at timestamp with time zone DEFAULT now(),
    updated_at timestamp with time zone DEFAULT now(),
    deleted_at timestamp with time zone,
    CONSTRAINT api_settings_pkey PRIMARY KEY (id),
    CONSTRAINT api_settings_company_id_fkey FOREIGN KEY (company_id) REFERENCES public.companies(id)
);

-- Create email_content table
CREATE TABLE IF NOT EXISTS public.email_content (
    id uuid NOT NULL DEFAULT uuid_generate_v4(),
    company_id uuid,
    key character varying NOT NULL,
    value text,
    created_at timestamp with time zone DEFAULT now(),
    updated_at timestamp with time zone DEFAULT now(),
    CONSTRAINT email_content_pkey PRIMARY KEY (id),
    CONSTRAINT email_content_company_id_fkey FOREIGN KEY (company_id) REFERENCES public.companies(id)
);

-- Create twilio_settings table
CREATE TABLE IF NOT EXISTS public.twilio_settings (
    id uuid NOT NULL DEFAULT uuid_generate_v4(),
    company_id uuid,
    name character varying NOT NULL,
    value text,
    created_at timestamp with time zone DEFAULT now(),
    updated_at timestamp with time zone DEFAULT now(),
    CONSTRAINT twilio_settings_pkey PRIMARY KEY (id),
    CONSTRAINT twilio_settings_company_id_fkey FOREIGN KEY (company_id) REFERENCES public.companies(id)
);

-- Create chat_settings table
CREATE TABLE IF NOT EXISTS public.chat_settings (
    id uuid NOT NULL DEFAULT uuid_generate_v4(),
    company_id uuid,
    name character varying NOT NULL,
    value text,
    created_at timestamp with time zone DEFAULT now(),
    updated_at timestamp with time zone DEFAULT now(),
    CONSTRAINT chat_settings_pkey PRIMARY KEY (id),
    CONSTRAINT chat_settings_company_id_fkey FOREIGN KEY (company_id) REFERENCES public.companies(id)
);

-- Create company_services table
CREATE TABLE IF NOT EXISTS public.company_services (
    id uuid NOT NULL DEFAULT uuid_generate_v4(),
    company_id uuid,
    title character varying NOT NULL,
    description text,
    image character varying,
    is_active boolean DEFAULT true,
    sort_order integer DEFAULT 0,
    created_at timestamp with time zone DEFAULT now(),
    updated_at timestamp with time zone DEFAULT now(),
    deleted_at timestamp with time zone,
    CONSTRAINT company_services_pkey PRIMARY KEY (id),
    CONSTRAINT company_services_company_id_fkey FOREIGN KEY (company_id) REFERENCES public.companies(id)
);

-- Insert default data for essential tables (only if not exists)
INSERT INTO public.frontend (key_name, key_value, created_at, updated_at) 
SELECT 'enable', '1', now(), now()
WHERE NOT EXISTS (SELECT 1 FROM public.frontend WHERE key_name = 'enable');

INSERT INTO public.frontend (key_name, key_value, created_at, updated_at) 
SELECT 'language', 'en', now(), now()
WHERE NOT EXISTS (SELECT 1 FROM public.frontend WHERE key_name = 'language');

INSERT INTO public.frontend (key_name, key_value, created_at, updated_at) 
SELECT 'currency', '£', now(), now()
WHERE NOT EXISTS (SELECT 1 FROM public.frontend WHERE key_name = 'currency');

INSERT INTO public.frontend (key_name, key_value, created_at, updated_at) 
SELECT 'app_name', 'PCO Flow', now(), now()
WHERE NOT EXISTS (SELECT 1 FROM public.frontend WHERE key_name = 'app_name');

-- Insert default cancellation reasons (only if not exists)
INSERT INTO public.cancellation_reasons (company_id, reason, description, created_at, updated_at) 
SELECT NULL, 'Customer Request', 'Cancelled by customer request', now(), now()
WHERE NOT EXISTS (SELECT 1 FROM public.cancellation_reasons WHERE reason = 'Customer Request');

INSERT INTO public.cancellation_reasons (company_id, reason, description, created_at, updated_at) 
SELECT NULL, 'Driver Unavailable', 'No driver available for the booking', now(), now()
WHERE NOT EXISTS (SELECT 1 FROM public.cancellation_reasons WHERE reason = 'Driver Unavailable');

INSERT INTO public.cancellation_reasons (company_id, reason, description, created_at, updated_at) 
SELECT NULL, 'Vehicle Issue', 'Vehicle technical issue', now(), now()
WHERE NOT EXISTS (SELECT 1 FROM public.cancellation_reasons WHERE reason = 'Vehicle Issue');

INSERT INTO public.cancellation_reasons (company_id, reason, description, created_at, updated_at) 
SELECT NULL, 'Weather Conditions', 'Cancelled due to weather conditions', now(), now()
WHERE NOT EXISTS (SELECT 1 FROM public.cancellation_reasons WHERE reason = 'Weather Conditions');

-- Insert default income categories (only if not exists)
INSERT INTO public.income_categories (company_id, name, description, created_at, updated_at) 
SELECT NULL, 'Booking Income', 'Income from customer bookings', now(), now()
WHERE NOT EXISTS (SELECT 1 FROM public.income_categories WHERE name = 'Booking Income');

INSERT INTO public.income_categories (company_id, name, description, created_at, updated_at) 
SELECT NULL, 'Service Income', 'Income from additional services', now(), now()
WHERE NOT EXISTS (SELECT 1 FROM public.income_categories WHERE name = 'Service Income');

INSERT INTO public.income_categories (company_id, name, description, created_at, updated_at) 
SELECT NULL, 'Other Income', 'Miscellaneous income', now(), now()
WHERE NOT EXISTS (SELECT 1 FROM public.income_categories WHERE name = 'Other Income');

-- Insert default expense categories (only if not exists)
INSERT INTO public.expense_categories (company_id, name, description, created_at, updated_at) 
SELECT NULL, 'Fuel', 'Vehicle fuel expenses', now(), now()
WHERE NOT EXISTS (SELECT 1 FROM public.expense_categories WHERE name = 'Fuel');

INSERT INTO public.expense_categories (company_id, name, description, created_at, updated_at) 
SELECT NULL, 'Maintenance', 'Vehicle maintenance and repairs', now(), now()
WHERE NOT EXISTS (SELECT 1 FROM public.expense_categories WHERE name = 'Maintenance');

INSERT INTO public.expense_categories (company_id, name, description, created_at, updated_at) 
SELECT NULL, 'Insurance', 'Vehicle insurance costs', now(), now()
WHERE NOT EXISTS (SELECT 1 FROM public.expense_categories WHERE name = 'Insurance');

INSERT INTO public.expense_categories (company_id, name, description, created_at, updated_at) 
SELECT NULL, 'Other Expenses', 'Miscellaneous expenses', now(), now()
WHERE NOT EXISTS (SELECT 1 FROM public.expense_categories WHERE name = 'Other Expenses');

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_testimonials_company_id ON public.testimonials(company_id);
CREATE INDEX IF NOT EXISTS idx_testimonials_deleted_at ON public.testimonials(deleted_at);
CREATE INDEX IF NOT EXISTS idx_team_company_id ON public.team(company_id);
CREATE INDEX IF NOT EXISTS idx_team_deleted_at ON public.team(deleted_at);
CREATE INDEX IF NOT EXISTS idx_service_items_company_id ON public.service_items(company_id);
CREATE INDEX IF NOT EXISTS idx_service_items_deleted_at ON public.service_items(deleted_at);
CREATE INDEX IF NOT EXISTS idx_notes_company_id ON public.notes(company_id);
CREATE INDEX IF NOT EXISTS idx_notes_vehicle_id ON public.notes(vehicle_id);
CREATE INDEX IF NOT EXISTS idx_notes_user_id ON public.notes(user_id);
CREATE INDEX IF NOT EXISTS idx_notes_deleted_at ON public.notes(deleted_at);
CREATE INDEX IF NOT EXISTS idx_company_services_company_id ON public.company_services(company_id);
CREATE INDEX IF NOT EXISTS idx_company_services_deleted_at ON public.company_services(deleted_at);

-- Enable Row Level Security (RLS) for all tables
ALTER TABLE public.testimonials ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.team ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.service_items ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.notes ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.cancellation_reasons ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.income_categories ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.expense_categories ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.fare_settings ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.api_settings ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.email_content ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.twilio_settings ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.chat_settings ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.company_services ENABLE ROW LEVEL SECURITY;

-- Create RLS policies for all tables (allow all access for now)
-- Drop existing policies first to avoid conflicts
DROP POLICY IF EXISTS "Allow all access to testimonials" ON public.testimonials;
DROP POLICY IF EXISTS "Allow all access to team" ON public.team;
DROP POLICY IF EXISTS "Allow all access to service_items" ON public.service_items;
DROP POLICY IF EXISTS "Allow all access to notes" ON public.notes;
DROP POLICY IF EXISTS "Allow all access to cancellation_reasons" ON public.cancellation_reasons;
DROP POLICY IF EXISTS "Allow all access to income_categories" ON public.income_categories;
DROP POLICY IF EXISTS "Allow all access to expense_categories" ON public.expense_categories;
DROP POLICY IF EXISTS "Allow all access to fare_settings" ON public.fare_settings;
DROP POLICY IF EXISTS "Allow all access to api_settings" ON public.api_settings;
DROP POLICY IF EXISTS "Allow all access to email_content" ON public.email_content;
DROP POLICY IF EXISTS "Allow all access to twilio_settings" ON public.twilio_settings;
DROP POLICY IF EXISTS "Allow all access to chat_settings" ON public.chat_settings;
DROP POLICY IF EXISTS "Allow all access to company_services" ON public.company_services;

-- Create new policies
CREATE POLICY "Allow all access to testimonials" ON public.testimonials FOR ALL USING (true);
CREATE POLICY "Allow all access to team" ON public.team FOR ALL USING (true);
CREATE POLICY "Allow all access to service_items" ON public.service_items FOR ALL USING (true);
CREATE POLICY "Allow all access to notes" ON public.notes FOR ALL USING (true);
CREATE POLICY "Allow all access to cancellation_reasons" ON public.cancellation_reasons FOR ALL USING (true);
CREATE POLICY "Allow all access to income_categories" ON public.income_categories FOR ALL USING (true);
CREATE POLICY "Allow all access to expense_categories" ON public.expense_categories FOR ALL USING (true);
CREATE POLICY "Allow all access to fare_settings" ON public.fare_settings FOR ALL USING (true);
CREATE POLICY "Allow all access to api_settings" ON public.api_settings FOR ALL USING (true);
CREATE POLICY "Allow all access to email_content" ON public.email_content FOR ALL USING (true);
CREATE POLICY "Allow all access to twilio_settings" ON public.twilio_settings FOR ALL USING (true);
CREATE POLICY "Allow all access to chat_settings" ON public.chat_settings FOR ALL USING (true);
CREATE POLICY "Allow all access to company_services" ON public.company_services FOR ALL USING (true);

-- Success message
SELECT 'All missing tables created successfully!' as status;
