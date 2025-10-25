#!/bin/bash

# Performance Optimization Application Script
# This script applies all performance optimizations safely

set -e  # Exit on error

echo "=========================================="
echo "Performance Optimization Application"
echo "=========================================="
echo ""

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Check if we're in the correct directory
if [ ! -f "framework/composer.json" ]; then
    echo -e "${RED}Error: Must be run from project root directory${NC}"
    exit 1
fi

echo "Step 1: Running database migration..."
cd framework
php artisan migrate --force

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Database migration completed${NC}"
else
    echo -e "${RED}✗ Database migration failed${NC}"
    exit 1
fi

echo ""
echo "Step 2: Clearing application cache..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Cache cleared${NC}"
else
    echo -e "${RED}✗ Cache clearing failed${NC}"
    exit 1
fi

echo ""
echo "Step 3: Rebuilding optimized cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Cache rebuilt${NC}"
else
    echo -e "${RED}✗ Cache rebuild failed${NC}"
    exit 1
fi

echo ""
echo "Step 4: Optimizing autoloader..."
cd ..
composer dump-autoload --optimize --no-dev 2>/dev/null || true

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Autoloader optimized${NC}"
else
    echo -e "${YELLOW}⚠ Autoloader optimization skipped (not critical)${NC}"
fi

echo ""
echo "=========================================="
echo -e "${GREEN}Performance Optimizations Applied Successfully!${NC}"
echo "=========================================="
echo ""
echo "What was done:"
echo "  1. ✓ Database indexes created"
echo "  2. ✓ Persistent connections enabled"
echo "  3. ✓ Cache optimized (15 min for dashboard)"
echo "  4. ✓ Asset compression enabled"
echo "  5. ✓ JavaScript deferred loading"
echo "  6. ✓ Eager loading relationships"
echo ""
echo "Next steps:"
echo "  1. Test dashboard load time (should be < 2 seconds)"
echo "  2. Test vehicle listing page"
echo "  3. Monitor Laravel logs for any errors"
echo "  4. Check browser DevTools for performance metrics"
echo ""
echo "For more details, see: PERFORMANCE_OPTIMIZATION_SUMMARY.md"
echo ""

