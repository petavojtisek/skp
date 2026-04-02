# ModuleGroupRight Module

The `ModuleGroupRight` module manages the association between administrator groups and specific module permissions.

## Key Components
- **`ModuleGroupRightEntity`**: Represents a permission granted to an admin group for a module.
    - Primary Key: `module_group_right_id`
    - Properties: `module_group_right_id`, `admin_group_id`, `module_id`, `permission_id`.
    - Methods: `getModuleGroupRightId()`, `setModuleGroupRightId()`.
- **`ModuleGroupRightDao` / `ModuleGroupRightMapper`**: Data access for the `module_group_right` table.
    - Table: `module_group_right`
    - Primary Key: `module_group_right_id`

## Core Functionality
- Define which administrator groups have access to which module features.
- Support for granular permission management at the group level through the `togglePermission` service method.
