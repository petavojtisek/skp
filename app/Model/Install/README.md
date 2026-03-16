# Install Module

The `Install` module tracks the installation status and paths of various system modules.

## Key Components
- **`InstallEntity`**: Represents an installable module.
    - Properties: `install_id`, `module_name`, `installed` (flag), `path`.
- **`InstallFacade`**: Entry point for checking module installation status.
- **`InstallService`**: Business logic for module installation and verification.
- **`InstallDao` / `InstallMapper`**: Data access for the `install` table.

## Database Interaction
- **Primary Table:** `install`

## Core Functionality
- List all available/installed modules.
- Verify if a specific module is installed and active.
- Track local paths for module source code.
