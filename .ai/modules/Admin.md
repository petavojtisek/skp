# Admin Module

The `Admin` module handles administrator accounts, authentication, and permissions.

## Key Components
- **`AdministratorEntity`**: Base entity for admin accounts.
    - Properties: `admin_id`, `admin_group_id`, `user_name`, `email`, `status`, etc.
- **`LoggedUserEntity`**: Extended entity for the currently logged-in user, stored in session identity.
    - Properties: `group` (AdminGroupEntity), `presentations` (array), `rights` (nested array), `active_presentation_id`.
    - Methods: `hasGroupRight(string $code)` (checks for specific right or 'ALL').
- **`AdminAuthenticator`**: Minimal authenticator handling password verification and ban status.
- **`AdminFacade`**: Entry point for admin operations (listing, saving, deleting).
- **`AdminService`**: Business logic for administrator management.

## Authentication Flow
1. **`AdminAuthenticator`** validates credentials and returns a `SimpleIdentity` with basic DB row data.
2. **`LoginFacade::login`** enriches this identity with full rights and group data using `LoggedUserEntity`.
3. **`AdminPresenter::startup`** refreshes `LoggedUserEntity` from the database on every request and syncs it back to the session identity via `User::updateIdentityData()`.

## Permissions
- Rights are categorized into `groups_right`, `module_rights`, and `page_rights`.
- The `ALL` key in `groups_right` grants superadmin access across the system.
