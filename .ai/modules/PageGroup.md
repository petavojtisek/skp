# PageGroup Module

The `PageGroup` module handles the grouping of pages for both administration permissions and frontend access control.

## Key Components
- **`PageGroupEntity`**: Represents a group.
    - Properties: `id`, `name`.
- **`PageGroupDao` / `PageGroupMapper`**: Data access with explicit methods for each association table.
- **`PageGroupService`**: Logic for toggling and retrieving group memberships.

## Association Methods
The module uses explicit methods to avoid ambiguity across different relation tables:
- **Administration Access**: `page_in_group`
    - `togglePageInGroup(pageId, groupId, state)`
    - `getPageInGroupIds(pageId)`
- **Frontend Access**: `page_in_group_user`
    - `togglePageInGroupUser(pageId, groupId, state)`
    - `getPageInGroupUserIds(pageId)`
- **Admin Group Link**: `page_group_admin_group`
    - `toggleAdminGroup(pageGroupId, adminGroupId, state)`
    - `getAdminGroupIds(pageGroupId)`

## Database Interaction
- **Primary Table:** `page_group`
- **Association Tables:** `page_in_group`, `page_in_group_user`, `page_group_admin_group`.
