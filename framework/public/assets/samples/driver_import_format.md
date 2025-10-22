# Driver Import File Format

## Expected Excel/CSV Columns

The driver import file should contain the following columns (case-insensitive):

### Required Columns:
- **first_name** - Driver's first name
- **last_name** - Driver's last name  
- **email** - Driver's email address (must be unique)
- **password** - Driver's password (minimum 6 characters)

### Optional Columns:
- **middle_name** - Driver's middle name
- **address** - Driver's address
- **phone** - Driver's phone number
- **country_code** - Country code for phone (default: 44)
- **employee_id** - Employee ID
- **contract_number** - Contract number
- **licence_number** - Driver's license number
- **issue_date** - License issue date (YYYY-MM-DD format)
- **expiration_date** - License expiration date (YYYY-MM-DD format)
- **join_date** - Driver join date (YYYY-MM-DD format)
- **leave_date** - Driver leave date (YYYY-MM-DD format)
- **gender** - Gender (male/female, default: male)
- **emergency_contact_details** - Emergency contact information

## Sample Data Format:

| first_name | last_name | email | password | phone | address | licence_number |
|------------|-----------|-------|----------|-------|---------|----------------|
| John | Doe | john.doe@example.com | password123 | 1234567890 | 123 Main St | DL123456 |
| Jane | Smith | jane.smith@example.com | password123 | 0987654321 | 456 Oak Ave | DL789012 |

## Import Behavior:

1. **Duplicate Detection**: If an email already exists, the row will be skipped
2. **Validation**: Rows with missing required fields will be skipped
3. **Default Values**: Missing optional fields will use default values
4. **Permissions**: All imported drivers will receive standard driver permissions
5. **Status**: All imported drivers will be set as active but unavailable initially

## File Requirements:

- **Format**: Excel (.xlsx, .xls) or CSV (.csv)
- **Size**: Maximum 5MB
- **Headers**: First row must contain column headers
- **Encoding**: UTF-8 recommended for CSV files

## Error Handling:

The import process will provide detailed statistics:
- Total rows processed
- Successfully imported drivers
- Duplicates skipped
- Validation errors
- Import errors

Use the sample file (`drivers.xlsx`) as a template for your import data.
