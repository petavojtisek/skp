# Page Module

The `Page` module manages the content and structure of public-facing pages.

## Key Components
- **`PageEntity`**: Represents a web page.
    - Properties: `page_id`, `page_parent_id`, `presentation_id`, `page_status`, `position`, `template_id`, `page_name`, `page_rewrite`, etc.
    - Extended Properties: 
        - `children`: Array of subpage entities.
        - `page_groups`: Entities from `page_in_group` (Administration groups).
        - `page_group_ids`: Flat array of IDs from `page_in_group` for permission checks.
        - `user_groups`: IDs from `page_in_group_user` (Frontend groups).
- **`PageFacade`**: Orchestrates page loading, tree construction, and association mapping.
- **`PageService`**: Contains core logic for tree building and DB operations.
- **`PageDao` / `PageMapper`**: Data access for the `page` table.

## Frontend Integration
- **`FrontendRunner`**: On the public site, `FrontendRunner` uses `PageFacade` to resolve the current page from the `page_id` parameter, handles redirects, checks page status, and sets up template variables like `activePage`, `menuTree`, and `pages`.

## Permissions Logic
- Access to edit/add/remove pages in the tree is determined by the intersection of the user's `page_rights` and the page's `page_group_ids`.
- Users with the `ALL` group right bypass these checks.

## Special Features
- **Special Parameters**: AJAX-driven key-value pairs stored in `spec_param_page`.
- **Automatic Rewrite**: Real-time URL slug generation from the page name.
