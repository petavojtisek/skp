# Object Module

The `Object` module provides a generic way to manage various entities (objects) within the system.

## Key Components
- **`ObjectEntity`**: Represents a generic system object.
    - Properties: `object_id`, `object_name`, `object_code`, `object_type`, `object_status`.
- **`ObjectFacade`**: Entry point for object management.
- **`ObjectService`**: Business logic for generic object operations.
- **`ObjectDao` / `ObjectMapper`**: Data access for objects.

## Core Functionality
- Management of generic system entities.
- Support for different object types and statuses.
