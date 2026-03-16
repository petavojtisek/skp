# Presentation Module

The `Presentation` module manages different websites (presentations), their domains, languages, and specific component actions.

## Key Components
- **`PresentationEntity`**: Represents a website/presentation.
    - Properties: `presentation_id`, `presentation_lang`, `presentation_name`, `domain`, `directory`, `is_default`.
- **`ComponentActionEntity`**: Defines specific actions for components within a presentation.
- **`SpecParamEntity`**: Handles specific presentation-level parameters.
- **`PresentationFacade`**: Main entry point for presentation management.
- **`PresentationService`**: Core logic for handling multiple websites/domains.
- **`PresentationDao` / `PresentationMapper`**: Data access for `presentation` and related tables.

## Database Interaction
- **Primary Table:** `presentation`
- **Related Tables:** `presentation_component_action`, `spec_param_presentation`.

## Core Functionality
- Multi-domain and multi-presentation support within a single installation.
- Configuration of default languages and metadata for each presentation.
- Mapping component actions specifically for each website.
