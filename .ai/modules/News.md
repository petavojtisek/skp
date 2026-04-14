# News Module

The `News` module manages news items that can be displayed on the frontend (e.g., in a slider or list).

## Key Components
- **`NewsEntity`**: Represents a news item.
    - Properties: `element_id`, `title`, `short_text`, `content`.
    - Joined from `element`: `name` (internal), `status_id`, `inserted` (as `created_dt`).
- **`NewsDao` / `NewsMapper`**: Data access for the `news` table.
- **`NewsService`**: Business logic for news management.
- **`NewsFacade`**: Entry point for presenters and components.
- **`NewsAdminControl`**: Admin UI for managing news (list, edit, copy, delete).
- **`NewsFrontControl`**: Frontend UI for displaying news.

## Database Interaction
- **Primary Table:** `news` (linked via `element_id` to `element`)
- **Related Tables:** `element` (contains status, validity dates, and component assignment).

## Functionality
- News items are linked to specific components via the `element` table.
- Frontend display filters items by `READY` status and current date validity.
- Admin allows CRUD operations, copying, and soft-linking to pages.
