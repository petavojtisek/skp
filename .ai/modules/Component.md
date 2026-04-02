# Component Module

The `Component` module manages modular components that can be placed on pages.

## Key Components
- **`ComponentEntity`**: Represents a base component.
    - Properties: `id`, `name`.
- **`ComponentDao` / `ComponentMapper`**: Data access for the `component` table.
- **`ComponentService`**: Logic for managing components.

## Database Interaction
- **Primary Table:** `component`
- **Related Tables:** `page_component`, `page_component_action`.
