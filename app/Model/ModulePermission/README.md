# ModulePermission Module

The `ModulePermission` module defines the various granular permissions available within the system modules.

## Key Components
- **`ModulePermissionEntity`**: Represents a specific permission type.
    - Properties: `permission_id`, `name`, `right_code_name`.
- **`ModulePermissionFacade`**: Entry point for permission definitions.
- **`ModulePermissionService`**: Logic for managing system-wide permissions.
- **`ModulePermissionDao` / `ModulePermissionMapper`**: Data access for the `module_permission` table.

## Database Interaction
- **Primary Table:** `module_permission`

## Core Functionality
- Registry of all possible actions/permissions in the system.
- Mapping permissions to human-readable names and unique code names.
