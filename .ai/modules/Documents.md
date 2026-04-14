# Documents Module

The `Documents` module manages downloadable documents linked to components.

## Key Components
- **`DocumentsEntity`**: Represents a document.
    - Properties: `element_id`, `text` (label), `file_id`.
    - Joined from `element`: `name` (internal), `status_id`, `inserted` (as `created_dt`).
    - Joined from `file_manager`: `original_name`.
- **`DocumentsDao` / `DocumentsMapper`**: Data access for the `documents` table.
- **`DocumentsService`**: Business logic for document management.
- **`DocumentsFacade`**: Entry point for presenters and components.
- **`DocumentsAdminControl`**: Admin UI for managing documents (list, edit, copy, delete).
    - Includes a file picker integration that returns `file_id` from the central `FileManager`.
- **`DocumentsFrontControl`**: Frontend UI for displaying a list of documents with download links.

## Database Interaction
- **Primary Table:** `documents` (linked via `element_id` to `element`)
- **Related Tables:** `element`, `file_manager`.

## File Picker Integration
The module uses an enhanced `FilesPresenter:picker` that supports a `callback` parameter. 
When a file is selected in the picker, it sends a message back to the opener window containing the `fileId`. 
The admin interface listens for this message and updates the hidden `file_id` field and the visual file name display.
