# AdminGroupRight Module

The `AdminGroupRight` module manages the relationship between administrator groups and specific administrative rights. It serves as an access control layer for group-based permissions.

## Key Components
- **`AdminGroupRightEntity`**: Represents a specific right assigned to a group, identifying the `admin_group_id` and the `admin_right_id`.
- **`AdminGroupRightService`**: Handles the business logic for managing group-level rights, including toggling permissions and retrieving assigned right codes or IDs for a group.
- **`AdminGroupRightDao` / `AdminGroupRightMapper`**: Responsible for data persistence and mapping between the database and the `AdminGroupRightEntity`, specifically interacting with the association table.

## Database Interaction
- **Primary Table:** `admin_group_right`
- **Related Tables:** `admin_group`, `admin_right`.

## Core Functionality
- Assigning specific administrative rights to group levels.
- Removing administrative rights from specific groups.
- Retrieving and listing all rights assigned to an administrator group (by ID or code name).
