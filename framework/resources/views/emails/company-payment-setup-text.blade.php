Hi {{ $superAdmin->name }},

Your company {{ $company->name }} has been added to PCO Flow.
To start using the platform, please complete your account setup by adding a payment method.

SUBSCRIPTION DETAILS
Company: {{ $company->name }}
Price per vehicle: £7/month

Complete Setup: {{ URL::temporarySignedRoute('admin.yamz.billing-portal', now()->addDays(7), ['id' => $company->id]) }}

This link opens a secure Stripe billing page where you can add your preferred payment method and manage your subscription.

If you have any questions, contact the PCO Flow team anytime.

— PCO Flow Team
© {{ date('Y') }} PCO Flow. All rights reserved.


