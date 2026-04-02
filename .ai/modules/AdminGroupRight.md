# AdminGroupRight Module

The `AdminGroupRight` module manages the association between administrator groups and specific system rights.

## Key Components
- **`AdminGroupRightEntity`**: Represents a specific right assigned to a group.
    - Primary Key: `admin_group_right_id`
    - Properties: `admin_group_right_id`, `admin_group_id`, `admin_right_id`.
    - Methods: `getAdminGroupRightId()`, `setAdminGroupRightId()`.
- **`AdminGroupRightDao` / `AdminGroupRightMapper`**: Data access for the `admin_group_right` table.
    - Table: `admin_group_right`
    - Primary Key: `admin_group_right_id`

## Core Functionality
- Mapping granular rights to administrator groups.
- Toggling rights through the `toggleRight` method in the Mapper.
