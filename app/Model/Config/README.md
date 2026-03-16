# Config Module

The `Config` module manages global system settings and their translations.

## Key Components
- **`ConfigEntity`**: Represents a configuration item.
    - Properties: `config_id`, `item`, `value`.
- **`ConfigFacade`**: Entry point for accessing and managing configuration.
- **`ConfigService`**: Logic for retrieval and updates.
- **`ConfigDao` / `ConfigMapper`**: Data access for the `config` table.

## Database Interaction
- **Primary Table:** `config`
- **Related Tables:** `config_lang` (translations of configuration values).

## Core Functionality
- Retrieve configuration items by name or ID.
- Support for multi-language configuration values.
- Centralized access to system-wide parameters.
