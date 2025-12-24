# Melkart Product Audit Log (Magento 2)

Magento 2 module that logs backend changes made on product edit pages.
Designed for audit, traceability and ERP synchronization use cases.

---

## Features

- Logs product changes made from the Magento Admin
- Detects modified products by sku
- Detects modified attributes before and after save
- Stores old and new values
- Logs date and time of the modification
- ACL support to control access to audit logs
- Proper handling of select and multiselect product attributes
- Logs the administrator user who performed product changes

---

## Use cases

- Product data audit
- Debugging catalog changes

---

## Requirements

- Magento >= 2.4

---

## Installation

### Manual installation

```bash
mkdir -p app/code/Melkart
cd app/code/Melkart
git clone git@github.com:Melkart/melkart-magento2-product-audit-log.git ProductAuditLog
```

## Excluded attributes

The following attributes are excluded from audit logs to reduce noise:

- `updated_at`
- `created_at`
- `media_gallery`
- `options`
- `quantity_and_stock_status`
- `stock_item`
- `website_ids`