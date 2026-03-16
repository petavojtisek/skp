# PageGroup Module

The `PageGroup` module allows for the grouping of pages for easier management and targeted permissions.

## Key Components
- **`PageGroupEntity`**: Represents a group of pages.
    - Properties: `id`, `name`.
- **`PageGroupFacade`**: Entry point for page group operations.
- **`PageGroupService`**: Logic for grouping pages and managing group members.
- **`PageGroupDao` / `PageGroupMapper`**: Data access for `page_group` and its relations.

## Database Interaction
- **Primary Table:** `page_group`
- **Related Tables:** `page_in_group`, `page_group_admin_group`.

## Core Functionality
- Create and manage logical groups of pages.
- Associate page groups with administrator groups for permission control.
