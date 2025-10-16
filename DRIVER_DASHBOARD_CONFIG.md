# Driver Dashboard Configuration

## Upcoming Payments Card

The upcoming payments card in the driver dashboard is hidden by default but can be enabled through configuration.

### Configuration

To show the upcoming payments card, add the following to your `.env` file:

```env
SHOW_UPCOMING_PAYMENTS=true
```

### Default Behavior

- **Default**: `SHOW_UPCOMING_PAYMENTS=false` (card is hidden)
- **When enabled**: `SHOW_UPCOMING_PAYMENTS=true` (card becomes visible with toggle functionality)

### How to Enable

1. Create or edit your `.env` file in the project root
2. Add the line: `SHOW_UPCOMING_PAYMENTS=true`
3. Clear config cache: `php artisan config:clear`
4. Refresh the driver dashboard

### Features When Enabled

- Upcoming payments card is visible by default
- Toggle button to show/hide the card
- Dynamic button text and styling
- Responsive design maintained

### Backend Configuration

The setting is defined in `framework/config/app.php`:

```php
'show_upcoming_payments' => env('SHOW_UPCOMING_PAYMENTS', false),
```

This allows for easy configuration management through environment variables.
