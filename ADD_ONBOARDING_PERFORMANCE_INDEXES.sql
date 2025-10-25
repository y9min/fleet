-- Add performance indexes for onboarding_drivers table
-- This will significantly improve query performance for the onboarding applications table

-- Index for status filtering (most common query)
CREATE INDEX IF NOT EXISTS idx_onboarding_status ON onboarding_drivers(status);

-- Index for vehicle_id filtering
CREATE INDEX IF NOT EXISTS idx_onboarding_vehicle ON onboarding_drivers(vehicle_id);

-- Index for created_at ordering
CREATE INDEX IF NOT EXISTS idx_onboarding_created ON onboarding_drivers(created_at);

-- Composite index for common queries (status + created_at)
CREATE INDEX IF NOT EXISTS idx_onboarding_status_created ON onboarding_drivers(status, created_at);

-- Composite index for vehicle + status queries
CREATE INDEX IF NOT EXISTS idx_onboarding_vehicle_status ON onboarding_drivers(vehicle_id, status);

-- Additional indexes for better performance
CREATE INDEX IF NOT EXISTS idx_onboarding_email ON onboarding_drivers(email);
CREATE INDEX IF NOT EXISTS idx_onboarding_phone ON onboarding_drivers(phone);
CREATE INDEX IF NOT EXISTS idx_onboarding_license ON onboarding_drivers(license_number);
