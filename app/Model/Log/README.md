# Log Module

The `Log` module provides comprehensive auditing and change tracking across the entire system.

## Key Components
- **`LogEntity`**: Represents a single audit entry.
    - Properties: `id`, `admin_id`, `module`, `code_name`, `action`, `name`, `element_id`, `after` (JSON), `before` (JSON), `created_ip`, `created_dt`.
- **`LogFacade`**: Entry point for viewing system logs.
- **`LogService`**: Logic for processing and retrieving audit data.
- **`LogDao` / `LogMapper`**: Data access for the `cms_log` table.

## Database Interaction
- **Primary Table:** `cms_log`

## Core Functionality
- Automatic logging of CRUD operations (via `BaseMapper`).
- Storing "before" and "after" states of changed entities in JSON format.
- Tracking which administrator performed each action and from which IP.
