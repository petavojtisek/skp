# Model Base Documentation

This document summarizes the core classes located in `app/Model/Base/`. These classes form the foundation of the **Ebcar architecture** used in the project.

## Interfaces
- **`IEntity`**: Defines the contract for all entities.
    - `getId()`, `setId()`: Primary key management.
    - `getEntityData()`: Returns entity data as an array.
    - `fillEntity(array $data)`: Populates the entity from an array.
- **`IMapper`**: Defines basic mapper functionality.
    - `begin()`, `commit()`: Transaction management.

## Entities
- **`AEntity`**: The root abstract class for all entities.
    - Handles property management with type conversion (`setVariable`).
    - Supported types: `int`, `float`, `string`, `json`, `date`, `bool`.
    - Tracks changed values for diffing and logging (`valuesUpdated`, `valuesDiff`).
    - Built-in support for translations via `BaseTranslateEntity`.
- **`BaseEntity`**: Extends `AEntity` and adds Nette's `SmartObject` features.
    - Implements magic getters/setters via `__call`, `__get`, and `__set`.
    - Automatically maps `camelCase` method calls to `snake_case` properties.
    - Includes standard auditing fields: `session_id`, `created_ip`, `created_dt`.
- **`BaseTranslateEntity`**: A specialized entity for handling multi-language data.
    - Properties: `entity_id`, `lang_id`, `value`.
- **`SecurityEntity`**: Extends `BaseEntity` to handle authentication data.
    - `setPassword(string $password)`: Hashes and sets the password.
    - `verifyPassword(string $password)`: Verifies a password against the hash.

## Mappers
- **`AMapper`**: The root abstract class for database mappers.
    - Requires a `Dibi\Connection`.
    - Provides high-level CRUD operations: `find`, `findAll`, `findBy`, `findOneBy`, `insert`, `update`, `delete`, `save`.
    - Includes utility methods: `count`, `sum`, `rowExist`, `getIDBy`, `getTableFields`.
    - Built-in lookup system: `getLookupOptions`, `getLookupList`, `getLookupItem`.
- **`BaseMapper`**: Extends `AMapper` with logging and translation support.
    - Automatically logs changes (`save`, `delete`) using the project's logging module.
    - Manages database-level translations via `saveTranslation`, `deleteTranslations`, and `getTranslations`.

## DAOs (Data Access Objects)
- **`ADao`**: The root abstract class for DAOs, providing an entity-oriented API.
    - Wraps a Mapper and manages conversion between database rows and Entity objects.
    - Mirrors Mapper methods but returns Entities (or arrays of Entities) instead of raw rows.
    - `getEntity()`: Factory method to instantiate the correct Entity type.
- **`BaseDao`**: Extends `ADao` and adds support for retrieving translations.

## Services
- **`AService` / **`BaseService`**: Foundation for business logic layers. Currently uses Nette's `SmartObject`.

## Forms
- **`BaseForm`**: Extends Nette's `Form` with AJAX support and custom input types (`addEmail`, `addNumber`, `addDate`, etc.).
- **`FormFactory`**: Factory for creating `BaseForm` instances with translation support.

---

**Crucial Rules for Model Base:**
1. **NEVER** modify files in `app/Model/Base/` directly unless explicitly instructed.
2. Always use `getId()` instead of direct property access `$id`.
3. Follow the `camelCase` (methods) vs `snake_case` (properties/DB) convention.
