# Frontend Module

The `Frontend` module is responsible for orchestrating the public-facing website's logic, including presentation management, page loading, and component execution.

## Key Components
- **`FrontendRunner`**: The central orchestrator for the `FrontModule`. It decouples the business logic of running the frontend from the `FrontPresenter`.
    - **Responsibilities**:
        - Identifying the active `Presentation` based on the domain.
        - Loading the active `Page` based on request parameters.
        - Managing the menu tree and page collections.
        - Resolving and loading special parameters for presentations and pages.
        - Determining and setting the page template.
        - Resolving and triggering component actions (from DB configurations).
        - Managing physical components attached to pages.
    - **Integration**: Injected into `FrontPresenter` via `@inject`. It requires the presenter instance to be set via `setPresenter($this)` to handle UI-specific tasks like redirects, error handling, and component creation.

- **`FrontPresenter`**: A thin abstract base presenter for all frontend presenters.
    - **Logic**: It delegates almost all initialization and data gathering to `FrontendRunner`.
    - **Properties**: Provides access to `activePage`, `activePresentation`, and other template variables populated by the runner.

## Component Execution Flow
1. **Resolution**: `FrontendRunner` gathers component calls from three sources:
    - `Presentation` level actions (global components).
    - `Page` level actions (actions specific to a page).
    - Physical `Component` entities attached to a page.
2. **Configuration**: All calls are normalized and stored in `componentsConfig`.
3. **Processing**: `FrontendRunner` iterates through configurations and triggers `getComponent()` on the presenter.
4. **Creation**: `FrontPresenter::createComponent()` delegates back to `FrontendRunner::createComponent()`, which uses `FrontControlFactory` to instantiate the actual controls and apply the resolved actions/parameters.

## Benefits of this Architecture
- **Testability**: Logic is moved out of the presenter into a service-like class.
- **Reusability**: The same logic can be used across different frontend presenters or even outside a standard Nette presenter context.
- **Maintainability**: Clear separation between UI flow (Presenter) and business logic (Runner).
