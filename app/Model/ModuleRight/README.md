# ModuleRight Module

The `ModuleRight` module defines which permissions are applicable to which modules.

## Key Components
- **`ModuleRightEntity`**: Maps a permission to a module.
    - Properties: `module_id`, `permission_id`.
- **`ModuleRightFacade`**: Entry point for module-specific rights management.
- **`ModuleRightService`**: Logic for defining rights available for each module.
- **`ModuleRightDao` / `ModuleRightMapper`**: Data access for the `module_right` table.

## Database Interaction
- **Primary Table:** `module_right`

## Core Functionality
- Linking granular permissions to specific modules.
- Enabling discovery of available features for each module.
