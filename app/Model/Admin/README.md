# Admin Module

The `Admin` module manages system administrators, their authentication, and basic details. It provides functionalities for handling administrator profiles, status, and related metadata.

## Key Components
- **`AdministratorEntity`**: Represents an administrator, containing properties like `admin_id`, `user_name`, `user_password`, `name`, `surname`, `email`, and `status`.
- **`AdminFacade`**: The primary entry point for managing administrators. It coordinates between `AdminService`, `AdminGroupService`, and other related services to provide a high-level API for administrator operations.
- **`AdminService`**: Contains core business logic for administrators, including CRUD operations and session data management (through `LoggedUserEntity`).
- **`AdminDao` / `AdminMapper`**: Responsible for data persistence and mapping between the database and the `AdministratorEntity`.

## Database Interaction
- **Primary Table:** `admin`
- **Related Tables:** `admin_group`, `admin_in_group`, `admin_presentation`, `admin_right`.

## Core Functionality
- Administrator management (CRUD operations).
- Management of active and disabled administrators.
- Integration with authentication and authorization systems (e.g., loading logged-in user entities).
- Coordinating with group and presentation services to manage administrator scopes.
