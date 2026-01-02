#!/bin/bash

echo "╔═══════════════════════════════════════════════════════╗"
echo "║   VoIP System - Automated Fix & Setup Script          ║"
echo "╚═══════════════════════════════════════════════════════╝"
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo -e "${RED}✗ Error: artisan file not found. Please run this script from the Laravel root directory.${NC}"
    exit 1
fi

echo -e "${GREEN}Step 1: Running VoIP migrations...${NC}"
php artisan migrate --path=packages/Ispecia/Voip/src/Database/Migrations --force

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Migrations completed successfully${NC}"
else
    echo -e "${RED}✗ Migrations failed${NC}"
    exit 1
fi

echo ""
echo -e "${GREEN}Step 2: Checking for existing VoIP provider...${NC}"

# Check if provider exists
PROVIDER_COUNT=$(php artisan tinker --execute="echo \Ispecia\Voip\Models\VoipProvider::count();" 2>/dev/null | tail -n 1)

if [ "$PROVIDER_COUNT" = "0" ]; then
    echo -e "${YELLOW}⚠ No provider found. Let's create one...${NC}"
    echo ""
    
    # Check if .env has Twilio credentials
    if grep -q "TWILIO_SID=" .env && grep -q "TWILIO_TOKEN=" .env; then
        echo -e "${GREEN}Found Twilio credentials in .env file${NC}"
        echo "Running migration command..."
        php artisan voip:migrate-config --force
        
        if [ $? -eq 0 ]; then
            echo -e "${GREEN}✓ Provider created from .env configuration${NC}"
        else
            echo -e "${YELLOW}⚠ Auto-configuration failed. Please run manually:${NC}"
            echo "  php artisan voip:setup --interactive"
        fi
    else
        echo -e "${YELLOW}⚠ No Twilio credentials found in .env${NC}"
        echo ""
        echo "Please run the setup wizard:"
        echo "  php artisan voip:setup --interactive"
        echo ""
        echo "Or add credentials to .env and run:"
        echo "  php artisan voip:migrate-config"
    fi
else
    echo -e "${GREEN}✓ Found $PROVIDER_COUNT provider(s) already configured${NC}"
fi

echo ""
echo -e "${GREEN}Step 3: Clearing caches...${NC}"
php artisan cache:clear
php artisan config:clear
echo -e "${GREEN}✓ Caches cleared${NC}"

echo ""
echo -e "${GREEN}Step 4: Checking frontend assets...${NC}"
if [ -f "package.json" ]; then
    echo "Frontend build detected. You may need to rebuild:"
    echo "  npm run build"
else
    echo -e "${YELLOW}⚠ No package.json found${NC}"
fi

echo ""
echo "╔═══════════════════════════════════════════════════════╗"
echo "║   Setup Summary                                        ║"
echo "╚═══════════════════════════════════════════════════════╝"
echo ""
echo -e "${GREEN}✓ Migrations: Complete${NC}"
echo -e "${GREEN}✓ Caches: Cleared${NC}"

# Check active provider
ACTIVE_PROVIDER=$(php artisan tinker --execute="echo \Ispecia\Voip\Models\VoipProvider::active()->first()?->name ?? 'None';" 2>/dev/null | tail -n 1)
if [ "$ACTIVE_PROVIDER" != "None" ]; then
    echo -e "${GREEN}✓ Active Provider: $ACTIVE_PROVIDER${NC}"
else
    echo -e "${YELLOW}⚠ Active Provider: None configured${NC}"
fi

echo ""
echo "Next steps:"
echo "1. Visit: Admin > VoIP > Providers (if no provider active)"
echo "2. Test the softphone (phone icon in sidebar)"
echo "3. Make a test call"
echo ""
echo "For detailed instructions, see: packages/Ispecia/Voip/README.md"
echo ""
