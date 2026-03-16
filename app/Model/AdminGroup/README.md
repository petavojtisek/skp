# AdminGroup Module

The `AdminGroup` module handles the organization of administrators into groups for permission and access management.

## Key Components
- **`AdminGroupEntity`**: Represents an administrative group.
    - Properties: `admin_group_id`, `admin_group_name`, `pid` (parent group ID), `code_name`.
- **`AdminGroupFacade`**: Entry point for administrative group management.
- **`AdminGroupService`**: Contains business logic for group operations and relations.
- **`AdminGroupDao` / `AdminGroupMapper`**: Data access for groups and their assignments.

## Database Interaction
- **Primary Table:** `admin_group`
- **Related Tables:** `admin_in_group` (maps administrators to groups).

## Core Functionality
- Manage administrator group hierarchies (`pid`).
- Retrieve and save group assignments for individual administrators.
- Map group-level permissions (managed via `AdminGroupRight`).
