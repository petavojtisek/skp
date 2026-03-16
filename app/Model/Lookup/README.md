# Lookup Module

The `Lookup` module is a core system component that manages hierarchical constants, labels, and system-wide options.

## Key Components
- **`LookupEntity`**: Represents a lookup item or constant.
    - Properties: `lookup_id`, `parent_id`, `item` (label), `constant`.
- **`LookupFacade`**: Entry point for retrieving system constants and options.
- **`LookupService`**: Logic for hierarchical lookup retrieval and constant definition.
- **`LookupDao` / `LookupMapper`**: Data access for `lookup` and `lookup_lang`.

## Database Interaction
- **Primary Table:** `lookup`
- **Related Tables:** `lookup_lang` (translations for lookup labels).

## Core Functionality
- Defining PHP constants (prefixed with `L_`) from the database.
- Managing multi-level hierarchical data (e.g., categories, statuses).
- Providing data for select boxes and UI labels across the system.
