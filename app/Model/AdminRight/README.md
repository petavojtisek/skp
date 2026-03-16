# AdminRight Module

The `AdminRight` module defines and manages the core administrative rights available within the system. These rights are used across different administrator groups to control access and permissions.

## Key Components
- **`AdminRightEntity`**: Represents an administrative right, including properties like `admin_right_id`, `name`, and `right_code_name`.
- **`AdminRightFacade`**: Provides a high-level interface for managing administrative rights. It coordinates with `AdminRightService` and `AdminGroupRightService` for listing rights and managing group-right assignments.
- **`AdminRightService`**: Contains business logic for administrative rights, handling finding and listing existing rights.
- **`AdminRightDao` / `AdminRightMapper`**: Responsible for data persistence and mapping between the database and the `AdminRightEntity` for the `admin_right` table.

## Database Interaction
- **Primary Table:** `admin_right`
- **Related Tables:** `admin_group_right` (defines the mapping between rights and groups).

## Core Functionality
- Definition and listing of available system-wide administrative rights.
- Support for toggling and assigning rights to specific administrator groups.
- Retrieving and listing rights associated with administrator group levels.
