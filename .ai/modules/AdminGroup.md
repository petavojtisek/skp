# AdminGroup Module

The `AdminGroup` module manages hierarchical administrator groups.

## Key Components
- **`AdminGroupEntity`**: Represents a group of administrators.
    - Properties: `admin_group_id`, `admin_group_name`, `pid` (parent ID), `code_name`.
- **`AdminGroupDao` / `AdminGroupMapper`**: Data access for the `admin_group` table.

## Hierarchy & Permissions
- **Tree Structure**: Groups are organized in a parent-child hierarchy.
- **Access Control**: Users are restricted to managing their own group and all groups below it in the hierarchy.
- **`getAvailableGroups(int $startGroupId)`**: Service method to retrieve all group entities in the allowed subtree.
- **`getGroupTree(int $startId)`**: Generates a nested array structure for tree visualization, starting from a specific node.

## Implementation Details
- `AdminPresenter` provides `isAllowedGroup(int $id)` helper to enforce these rules across the Admin module.
- Root-level group creation (`pid = 0`) is restricted to the Superadmin group (ID 1).
