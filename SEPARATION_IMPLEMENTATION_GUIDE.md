# Phone Issuance & Return Separation Implementation Guide

## Overview
This document outlines the database schema refactoring that separates phone issuance and return records into two distinct, relational tables while maintaining stability across the frontend and backend.

## New Database Schema

### Tables Structure

#### `phones` (unchanged)
```
id, brand, model, serial_num (unique), imei_one, imei_two, 
ram, rom, purchase_date, sim_no, status, remarks, timestamps
```

#### `phone_issuances` (new)
```
id, serial_num (FK → phones), issued_to, department, date_issued, 
issued_by, issued_accessories, cashout, timestamps
```

#### `phone_returns` (new)
```
id, phone_issuance_id (FK → phone_issuances), date_returned, 
returned_to, returned_by, returnee_department, returned_accessories, timestamps
```

#### `phone_transactions` (legacy, kept for backward compatibility)
Maintained as a fallback for any legacy code that still references it.

## Data Flow & Relationships

```
phones (1) ──→ (Many) phone_issuances (1) ──→ (1) phone_returns
```

- **One phone** can have **multiple issuances** over time
- **Each issuance** can have **at most one return**
- When a phone is issued, a `phone_issuance` record is created
- When a phone is returned, a `phone_return` record is created linked to the issuance

## Model Relationships

### Phone Model
```php
// Get all issuances for this phone
phone->issuances()

// Get the current active issuance (without return)
phone->currentIssuance()

// Get all returns through issuances
phone->returns()

// Legacy support
phone->transactions()
phone->currentTransaction()
```

### PhoneIssuance Model
```php
// Get the phone
issuance->phone()

// Get the associated return record (if exists)
issuance->return()

// Check if returned
issuance->isReturned()
```

### PhoneReturn Model
```php
// Get the issuance
return->issuance()

// Get the phone through issuance
return->phone()
```

## Controller Methods

### Issue a Phone
```php
public function issue(Request $request, Phone $phone)
{
    // Validates and creates PhoneIssuance record
    // Updates phone status to 'issued'
}
```

### Return a Phone
```php
public function return(Request $request, Phone $phone)
{
    // Finds the active issuance (without return)
    // Creates PhoneReturn record linked to that issuance
    // Updates phone status to 'available'
}
```

## Frontend Updates Required

### PhoneDetails.vue Properties
Update the props to accept both issuance and return data:

```javascript
const props = defineProps({
    phone: { type: Object, required: true },
    phone_issuance: { type: [Object, null], default: null },
    phone_return: { type: [Object, null], default: null },
});
```

### History Display
Update to use the new issuances array:

```javascript
const filteredHistory = computed(() => {
    if (!props.phone.issuances) return [];
    const searchTerm = historySearch.value.toLowerCase();
    
    return props.phone.issuances.map(issuance => ({
        ...issuance,
        return: issuance.return,
    })).filter((record) => {
        return (
            (record.issued_to?.toLowerCase() || '').includes(searchTerm) ||
            (record.issued_by?.toLowerCase() || '').includes(searchTerm) ||
            (record.return?.returned_by?.toLowerCase() || '').includes(searchTerm) ||
            (record.department?.toLowerCase() || '').includes(searchTerm)
        );
    });
});
```

### Issuance Details Display
Replace `props.phone_transaction?.issued_*` with `props.phone_issuance?.issued_*`

### Return Details Display
Replace `props.phone_transaction?.returned_*` with `props.phone_return?.returned_*`

## Migration Path

1. **New migration** creates `phone_issuances` and `phone_returns` tables
2. **Data migration** (within migration) automatically converts existing `phone_transactions` to new structure
3. **Backward compatibility** maintained through legacy `transactions()` methods on Phone model
4. **Gradual frontend update** - components can be updated incrementally while legacy support remains

## API Data Format

### When Retrieving a Phone with Details
```json
{
  "id": 1,
  "brand": "Apple",
  "model": "iPhone 14",
  "serial_num": "ABC123",
  "status": "issued",
  "issuances": [
    {
      "id": 10,
      "serial_num": "ABC123",
      "issued_to": "John Doe",
      "department": "Engineering",
      "date_issued": "2024-01-15",
      "issued_by": "IT Admin",
      "issued_accessories": "Charger, Case",
      "cashout": true,
      "return": {
        "id": 5,
        "phone_issuance_id": 10,
        "date_returned": "2024-02-20",
        "returned_to": "IT Department",
        "returned_by": "Jane Smith",
        "returnee_department": "HR",
        "returned_accessories": "Charger, Case"
      }
    }
  ]
}
```

## Key Benefits

1. **Clear Separation of Concerns**: Issuance and return data are logically separated
2. **Referential Integrity**: Returns are explicitly linked to their issuances
3. **Query Efficiency**: Easier to query active issuances or returns separately
4. **Maintainability**: Future modifications to issuance or return logic are simpler
5. **Backward Compatibility**: Existing code continues to work during migration
6. **Data Consistency**: Phone status always reflects current issuance state

## Testing Migration

After running migrations:
```bash
# Check that new tables exist
php artisan db:show

# Verify data migration
php artisan tinker
>>> DB::table('phone_issuances')->count()
>>> DB::table('phone_returns')->count()
>>> App\Models\Phone::first()->issuances
```

## Notes

- The `phone_transactions` table remains for backward compatibility but should eventually be deprecated
- All new code should use `PhoneIssuance` and `PhoneReturn` models
- The frontend should be gradually updated to use `issuances` instead of `transactions`
