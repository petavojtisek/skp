# Login Module

The `Login` module handles the process of user authentication and session initialization.

## Key Components
- **`CredentialEntity`**: Represents user credentials during the login process.
- **`LoginFacade`**: Entry point for the login process.
- **`LoginService`**: Core logic for verifying users and managing the login flow.

## Core Functionality
- Validate user credentials.
- Handle password verification (delegated to `Admin` and `SecurityEntity`).
- Coordinate with `AdminModule` for session setup.
