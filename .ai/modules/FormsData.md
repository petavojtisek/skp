# FormsData Module

The `FormsData` module handles data submitted from frontend forms. It belongs to the "Tools" category (like `Members` or `WebTexts`).

## Key Components
- **`FormsDataEntity`**: Entity for form submissions.
    - Properties: `id`, `form_name`, `data` (JSON), `ip_address`, `created_dt`, `status`.
    - Methods: `getData()` (returns decoded JSON), `setData(mixed $data)` (encodes to JSON).
- **`FormsDataFacade`**: Entry point for form data operations.
- **`FormsDataService`**: Business logic for form data management.
- **`FormsDataAdminControl`**: Administrative component for displaying submissions.
    - Implements `IToolsControl`.
    - **Features**: List view with searching/pagination, Detail view for viewing JSON data. No edit view.

## Database Integration
- Table: `form_data`
- Data is stored in a JSON column to allow flexible form structures without changing the database schema.

## Permissions
- Standard permissions: `list` (viewing lists and details), `delete` (removing records).
- Access code: `forms_data`.

## Usage for Developers
To save data from a frontend form, use `FormsDataFacade::saveFormData()` with a populated `FormsDataEntity`.
