# Stripe Subscription Setup Guide

## Overview
This guide explains how to configure and use the Stripe subscription workflow for PCO Flow.

## Configuration

### 1. Database Migration

Run the migration first:

```bash
cd framework
php artisan migrate
```

### 2. Stripe API Keys

Add your Stripe test keys to your `.env` file in the `framework` directory:

```env
STRIPE_KEY=pk_test_your_publishable_key_here
STRIPE_SECRET=sk_test_your_secret_key_here
```

**Where to get test keys:**
1. Go to https://dashboard.stripe.com/test/apikeys
2. Copy your **Publishable key** (starts with `pk_test_`) → set as `STRIPE_KEY`
3. Copy your **Secret key** (starts with `sk_test_`) → set as `STRIPE_SECRET`

**For production:**
- Use production keys (starts with `pk_live_` and `sk_live_`)
- Update the same `.env` variables

### 2. Resend API Key (for email)

Add your Resend API key to send payment setup emails:

```env
RESEND_API_KEY=re_your_resend_api_key_here
MAIL_FROM_ADDRESS=noreply@yourdomain.com
```

### 3. Stripe Webhook Secret (optional but recommended)

For webhook event handling (payment succeeded, failed, subscription updates):

```env
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret_here
```

**Where to get webhook secret:**
1. Go to https://dashboard.stripe.com/test/webhooks
2. Create a webhook endpoint pointing to: `https://yourdomain.com/webhooks/stripe`
3. Select events: `invoice.payment_succeeded`, `invoice.payment_failed`, `customer.subscription.updated`, `customer.subscription.deleted`, `customer.subscription.created`
4. Copy the signing secret (starts with `whsec_`) → set as `STRIPE_WEBHOOK_SECRET`

**Where to get Resend API key:**
1. Go to https://resend.com/api-keys
2. Create a new API key
3. Copy the key (starts with `re_`) → set as `RESEND_API_KEY`

### 4. What the Migration Adds

The migration adds the following columns to the `companies` table:
- `stripe_customer_id` - Stripe customer ID
- `stripe_subscription_id` - Stripe subscription ID
- `stripe_subscription_item_id` - Stripe subscription item ID
- `subscription_status` - Current subscription status (active, incomplete, past_due, canceled, etc.)

## Webhook Setup (Optional but Recommended)

To receive real-time updates about payments and subscriptions:

1. In Stripe Dashboard: https://dashboard.stripe.com/test/webhooks
2. Click "Add endpoint"
3. Endpoint URL: `https://yourdomain.com/webhooks/stripe`
4. Select events:
   - `invoice.payment_succeeded`
   - `invoice.payment_failed`
   - `customer.subscription.updated`
   - `customer.subscription.deleted`
   - `customer.subscription.created`
   - `payment_method.attached` (NEW - for immediate payment method detection)
5. Copy the signing secret and add to `.env` as `STRIPE_WEBHOOK_SECRET`

## How It Works

### Flow

1. **Company Creation**: When yamz creates a company with a super admin user, a Stripe Customer is automatically created.

2. **First Vehicle**: When the first vehicle is added to a company, a Stripe Subscription is created with quantity=1 (£7/month). The subscription may start in "incomplete" status if no payment method exists yet.

3. **Payment Setup**: Yamz clicks the "Send Payment Setup Email" button on the company page, which:
   - Generates a Stripe Billing Portal link
   - Sends an email to the company super admin via Resend
   - Email contains link to add payment method and view subscription

4. **Payment Activation**: When the super admin adds a payment method via the Billing Portal:
   - The payment method is attached to the customer
   - The `payment_method.attached` webhook is triggered immediately
   - The system finds all incomplete subscriptions for that customer
   - The system ensures payment intent exists (creates if needed)
   - The system automatically confirms the payment intent with the new payment method
   - The `customer.subscription.updated` webhook is also triggered
   - Subscription status is synced from Stripe to ensure database is up to date
   - The subscription becomes active immediately after payment confirmation

5. **Vehicle Changes**: 
   - Adding vehicles → Subscription quantity increases (prorated)
   - Removing vehicles → Subscription quantity decreases (prorated)
   - All changes are automatically handled

6. **Billing**: Companies are charged £7/vehicle/month at the end of each month.

## Usage

### For Yamz (Admin)

1. Create a company with a super admin user assigned
2. View the company page
3. Click "Send Payment Setup Email" button (appears if company has super admin and Stripe customer)
4. The super admin will receive an email with payment setup instructions

### For Company Super Admins

1. Receive email from yamz
2. Click the "Complete Payment Setup" button in the email
3. Opens Stripe Billing Portal
4. Add payment method (credit/debit card)
5. View subscription details
6. Subscription activates automatically

## Features

- ✅ Automatic customer creation when company with super admin is created
- ✅ Automatic customer recovery if customer is deleted in Stripe
- ✅ Automatic subscription creation when first vehicle is added
- ✅ Automatic payment intent confirmation when payment method is added
- ✅ Payment method detection via `payment_method.attached` webhook
- ✅ Automatic subscription status synchronization
- ✅ Automatic quantity updates when vehicles are added/removed
- ✅ Proration for mid-cycle changes
- ✅ Billing cycle anchor set to end of month
- ✅ Email notification via Resend
- ✅ Stripe Billing Portal for payment management
- ✅ Error handling (won't fail company/vehicle operations if Stripe fails)
- ✅ Comprehensive logging

## Troubleshooting

### Subscription not created?
- Check that company has a super admin user (user_type='S')
- Check that company has `stripe_customer_id` in database
- Check Laravel logs for Stripe API errors
- Verify Stripe keys are correct in `.env`

### Email not sending?
- Verify `RESEND_API_KEY` is set in `.env`
- Check that `MAIL_FROM_ADDRESS` is configured
- Check Laravel logs for Resend API errors
- Verify the super admin email is valid

### Payment method not working?
- Check Stripe Dashboard for subscription status
- Verify payment method was added in Billing Portal and set as default
- Check subscription status in database (`subscription_status` column)
- Check Laravel logs for payment intent confirmation errors
- Note: Payment intent is automatically confirmed when payment method is added via webhook
- If customer was deleted in Stripe, the system will automatically recreate it when sending payment email

### Customer deleted in Stripe?
- The system automatically detects deleted customers
- When sending payment email, if customer is deleted, it will be automatically recreated
- Old customer ID is cleared and new customer is created
- Subscription will need to be recreated if it was also deleted

## Testing

### Test Cards (Stripe Test Mode)

Use these test card numbers in Stripe test mode:

- **Success**: `4242 4242 4242 4242`
- **Decline**: `4000 0000 0000 0002`
- **Requires Authentication**: `4000 0025 0000 3155`

Use any future expiry date and any 3-digit CVC.

## Monitoring

Check subscription status:
- In database: `companies.subscription_status` column
- In Stripe Dashboard: https://dashboard.stripe.com/subscriptions
- In company view: Shows subscription status on payment setup section

## Support

For issues:
1. Check Laravel logs: `framework/storage/logs/laravel.log`
2. Check Stripe Dashboard: https://dashboard.stripe.com/test/logs
3. Check Resend Dashboard: https://resend.com/emails (if email issues)

