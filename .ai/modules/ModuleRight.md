# ModuleRight Module

The `ModuleRight` module defines which permissions are applicable to which modules.

## Key Components
- **`ModuleRightEntity`**: Maps a permission to a module.
    - Primary Key: `module_right_id`
    - Properties: `module_right_id`, `module_id`, `permission_id`.
    - Methods: `getModuleRightId()`, `setModuleRightId()`.
- **`ModuleRightDao` / `ModuleRightMapper`**: Data access for the `module_right` table.
    - Table: `module_right`
    - Primary Key: `module_right_id`

## Core Functionality
- Linking granular permissions to specific modules.
- Enabling discovery of available features for each module.
