# Module Module

The `Module Module` manages the registry of system modules, their types, and active status within the application. It also serves as an orchestrator for module-related permissions and rights.

## Key Components
- **`ModuleEntity`**: Represents a registered system module.
    - Properties: `module_id`, `install_id`, `module_type`, `module_active`, `module_name`, `module_code_name`, `module_class_name`.
- **`ModuleFacade`**: Entry point for module management, rights discovery, and permission matrix generation.
- **`ModuleService`**: Core business logic. Orchestrates sub-services (`ModuleGroupRightService`, `ModulePermissionService`, `ModuleRightService`) to manage complex operations like permission matrices.
- **`ModuleDao` / `ModuleMapper`**: Data access for the `module` table.

## Database Interaction
- **Primary Table:** `module`
- **Related Tables:** 
    - `install` (linked via `install_id`)
    - `module_right`, `module_permission` (permission management via sub-services)
    - `module_group_right` (group-specific permissions)

## Core Functionality
- Registry of available system modules and their metadata.
- Management of module activation status.
- **Permission Matrix Generation:** Compiling a complete list of permissions for a specific module and administrator group.
- Discovery of module-specific rights and permissions.
