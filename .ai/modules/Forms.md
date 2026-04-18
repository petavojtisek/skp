# Forms Module (Content)

The `Forms` module is a content module (linked to the `element` table) that allows adding dynamic forms to a page.

## Key Components
- **`FormsEntity`**: Stores configuration for the form on a specific page.
    - Properties: `element_id`, `form_component`.
    - Joined: `name`, `status_id`, `created_dt`.
- **`FormsFacade`**: Entry point for form configuration.
- **`FormsFrontControl`**: Frontend component that dynamically loads specific forms.
    - Uses `FormControlFactory` to instantiate form components based on the `form_component` field.
- **`FormControlFactory`**: Creates specific form instances (implementing `IFormControl`).
- **`FormsComponents/`**: Contains individual form logic (e.g., `ContactForm.php`).
    - Forms typically extend `BaseForm` and implement `IFormControl`.
- **`templates/Forms/`**: Contains Latte templates for individual forms.

## Usage
1. Define a new form class in `FormsComponents/` extending `BaseForm`.
2. Create its template in `templates/Forms/`.
3. Register the form factory in `config.neon`.
4. Select the form in the admin panel on the desired page.

## Database Integration
- Table: `forms`
- Primary Key: `element_id` (FK to `element`)
- Field: `form_component` (name of the class in `FormsComponents/`)
