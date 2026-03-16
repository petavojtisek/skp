# ModuleGroupRight Module

The `ModuleGroupRight` module manages the association between administrator groups and specific module permissions.

## Key Components
- **`ModuleGroupRightEntity`**: Represents a permission granted to an admin group for a module.
    - Properties: `admin_group_id`, `module_id`, `permission_id`.
- **`ModuleGroupRightFacade`**: Entry point for group-module permission management.
- **`ModuleGroupRightService`**: Logic for assigning and verifying group rights.
- **`ModuleGroupRightDao` / `ModuleGroupRightMapper`**: Data access for the `module_group_right` table.

## Database Interaction
- **Primary Table:** `module_group_right`

## Core Functionality
- Define which administrator groups have access to which module features.
- Support for granular permission management at the group level.
