# Form Status Reference Table

## Overview
The `form_statuses` table provides a reference for form status values used in the `leave_details` table.

## Table Structure
- `form_stat_id` (Primary Key): Integer ID for the form status
- `form_status`: String name of the status
- `description`: Text description of what the status means
- `timestamps`: Created and updated timestamps

## Current Status Values

| ID | Status Name | Description |
|----|-------------|-------------|
| 1  | Draft       | Form is saved as draft and can be edited by the user |
| 2  | Complete    | Form is submitted and ready for processing by HOD |
| 3  | Returned    | Form is returned to user for corrections with remarks |

## Usage Examples

### In Controllers
```php
use App\Models\FormStatus;

// Get status name by ID
$statusName = FormStatus::getStatusName(1); // Returns "Draft"

// Get all statuses for dropdown
$statuses = FormStatus::getStatusesArray();

// Using constants
if ($leave->form_status == FormStatus::DRAFT) {
    // Handle draft logic
}
```

### In Queries
```php
// Join with form_statuses to get readable names
$leaves = DB::table('leave_details')
    ->join('form_statuses', 'leave_details.form_status', '=', 'form_statuses.form_stat_id')
    ->select('leave_details.*', 'form_statuses.form_status as status_name')
    ->get();
```

### In Blade Templates
```php
// Display status name instead of ID
{{ \App\Models\FormStatus::getStatusName($leave->form_status) }}
```

## Benefits
1. **Maintainability**: Easy to add new statuses or modify existing ones
2. **Readability**: Get human-readable status names instead of IDs
3. **Consistency**: Centralized reference for all form statuses
4. **Future-proof**: Easy to extend with additional fields like colors, icons, etc. 