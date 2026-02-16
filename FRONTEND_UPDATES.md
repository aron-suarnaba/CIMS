# Vue Frontend Updates - Phone Separation Implementation

## File: PhoneDetails.vue

### Changes Made:

#### 1. **Props Definition Updated**
**Before:**
```javascript
const props = defineProps({
    phone: { type: Object, required: true },
    phone_transaction: { type: [Object, null], required: true, default: null },
});
```

**After:**
```javascript
const props = defineProps({
    phone: { type: Object, required: true },
    phone_issuance: { type: [Object, null], default: null },
    phone_return: { type: [Object, null], default: null },
});
```

#### 2. **History Filtering Logic Updated**
**Before:**
```javascript
const filteredHistory = computed(() => {
    if (!props.phone.transactions) return [];
    // Filter phone.transactions
    return props.phone.transactions.filter((tx) => {
        return (tx.issued_to?....includes(searchTerm) || ...);
    });
});
```

**After:**
```javascript
const filteredHistory = computed(() => {
    if (!props.phone.issuances) return [];
    // Map issuances with their returns
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

#### 3. **Issuance Details Section Updated**
- Replaced `props.phone_transaction?.issued_to` with `props.phone_issuance?.issued_to`
- Replaced `props.phone_transaction?.department` with `props.phone_issuance?.department`
- Replaced `props.phone_transaction?.issued_by` with `props.phone_issuance?.issued_by`
- Replaced `props.phone_transaction?.date_issued` with `props.phone_issuance?.date_issued`
- Replaced `props.phone_transaction?.issued_accessories` with `props.phone_issuance?.issued_accessories`

#### 4. **Return Details Section Updated**
- Replaced `props.phone_transaction?.returned_by` with `props.phone_return?.returned_by`
- Replaced `props.phone_transaction?.returnee_department` with `props.phone_return?.returnee_department`
- Replaced `props.phone_transaction?.returned_to` with `props.phone_return?.returned_to`
- Replaced `props.phone_transaction?.date_returned` with `props.phone_return?.date_returned`
- Replaced `props.phone_transaction?.returned_accessories` with `props.phone_return?.returned_accessories`

#### 5. **History Table Updated**
**Before:**
```html
<td class="text-nowrap">
    <span v-if="tx.date_returned" class="fw-medium mb-0 text-nowrap ps-2">
        {{ formatDate(tx.date_returned) }}
    </span>
    <span v-else class="badge rounded-pill bg-warning text-dark">In Use</span>
</td>
<td>
    <div class="fw-bold text-dark">{{ tx.returned_by || '—' }}</div>
    <div class="text-muted small ps-2">{{ tx.returnee_department }}</div>
</td>
<td>
    <div class="fw-bold text-dark">{{ tx.returned_to }}</div>
</td>
<td class="small text-muted text-wrap pe-3">{{ tx.returned_accessories || '—' }}</td>
```

**After:**
```html
<td class="text-nowrap">
    <span v-if="tx.return?.date_returned" class="fw-medium mb-0 text-nowrap ps-2">
        {{ formatDate(tx.return.date_returned) }}
    </span>
    <span v-else class="badge rounded-pill bg-warning text-dark">In Use</span>
</td>
<td>
    <div class="fw-bold text-dark">{{ tx.return?.returned_by || '—' }}</div>
    <div class="text-muted small ps-2">{{ tx.return?.returnee_department }}</div>
</td>
<td>
    <div class="fw-bold text-dark">{{ tx.return?.returned_to }}</div>
</td>
<td class="small text-muted text-wrap pe-3">{{ tx.return?.returned_accessories || '—' }}</td>
```

## Data Structure Flow

### Component receives:
```javascript
{
  phone: { id, brand, model, serial_num, status, issuances: [...] },
  phone_issuance: { id, issued_to, department, date_issued, issued_by, issued_accessories, ... },
  phone_return: { id, phone_issuance_id, returned_to, returned_by, date_returned, ... }
}
```

### History table displays:
```javascript
issuances.map(issuance => ({
  id: issuance.id,
  issued_to: issuance.issued_to,
  issued_by: issuance.issued_by,
  date_issued: issuance.date_issued,
  issued_accessories: issuance.issued_accessories,
  return: {
    id: issuance.return?.id,
    date_returned: issuance.return?.date_returned,
    returned_by: issuance.return?.returned_by,
    returnee_department: issuance.return?.returnee_department,
    returned_to: issuance.return?.returned_to,
    returned_accessories: issuance.return?.returned_accessories,
  }
}))
```

## Form Submissions (No Changes Needed)
The form submissions for issue, return, and update remain unchanged as they already use the correct routes:
- `form.post(route('phone.issue', props.phone.id))`
- `returnform.post(route('phone.return', props.phone.id))`
- `updateForm.put(route('phone.update', props.phone.id))`

## Backward Compatibility Notes
- The component still supports displaying current issuance/return state
- History table shows all issuances with their optional return records
- If a phone has no active return, the "In Use" badge is shown
- The form logic remains compatible with the backend separation implementation
