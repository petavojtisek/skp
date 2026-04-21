# SKP Project Rules

## Core Mandates
1.  **Surgical Changes:** Focus strictly on requested changes. Avoid unrelated modifications.
2.  **Logic Integrity:** Text changes must not affect logic.
3.  **No Unrelated Logic Deletion:** If changing logic, do not delete or alter unrelated logic.
4.  **Base & Abstract Protection:** **NEVER** modify Base or Abstract classes (specifically those in `app/Model/Base/`).
5.  **No Autonomous Action:** Do not perform any actions (research, implementation, etc.) unless explicitly instructed by the user. Wait for a directive.
6.  **Mandatory Documentation Update:** After EVERY change to the database structure, model layer (Mappers, DAOs, Facades, Services), or entities, you MUST update the corresponding documentation in the `skp/.ai/` directory (e.g., `database.md`, files in `modules/`, and `README.md`).
7.  **No Mapper Exposure:** DAO classes MUST NOT contain a `getMapper()` method. The Mapper is internal to the DAO and must not be leaked or used in higher layers (Service, Facade, Presenter).

## Workflow
1.  **Aktualizace instalátoru (Aktualizuj install):** Tento proces znamená synchronizaci aktuálního kódu modulů z vývoje do instalačních balíčků. Pro každý modul v `app/Modules/<ModuleName>/` zkopírujte jeho obsah do `install/modules/<ModuleName>/src/`. Tím se zajistí, že instalátor obsahuje vždy nejnovější verzi kódu.

2.  **Hledání zapomenutého ladění (Najdi xdebug):** Před dokončením úkolu nebo na vyžádání prohledejte adresář `app/` na výskyt funkce `xdebug_break()`. Tento příkaz nesmí zůstat v produkčním kódu.

## Naming Conventions
- **Methods:** Always use `camelCase` for methods in entities and presenters (e.g., `getSessionId()`).
- **Properties & DB Columns:** Always use `snake_case` for entity properties and database columns (e.g., `session_id`).

## Entity & Model Guidelines
1.  **Encapsulation:** Always use getters for entity properties (e.g., `$entity->getId()`) instead of direct property access (`$entity->id`).
2.  **Type Safety:** Prioritize using Entity objects over arrays to maintain type clarity.
3.  **Collections:** Use Nette `ArrayHash` or `ArrayObject` for collections when appropriate.
4.  **DRY Mappers:** Utilize existing methods in `BaseMapper`/`AMapper` (like `findAllBy()`) instead of creating redundant new ones.
5.  **Structured DAOs:** DAOs should return structured data, such as arrays of entities indexed by their IDs, rather than plain arrays of arrays.
6.  **DB Mirroring:** Entities must strictly mirror the database table schema. Check the DB table structure before creating or modifying an entity. Do not add non-existent fields or omit existing ones.

## Architecture
- **Framework:** Nette 3.1, PHP 8.5+, Dibi 5.1.
- **Pattern:** Ebcar architecture (Entity, Dao, Mapper, Facade, Service).
- **Lookup Constants:** Constants prefixed with `L_` are automatically loaded from the `lookup` database table.

## Architectural Principles (Logic Placement)
1.  **Thin Presenters:** Presenters must contain minimal logic. Their primary role is to query Facades and handle UI flow.
2.  **Facade as Entry Point:** Presenters should almost exclusively communicate with Facades. Facades act as the public interface for the model layer (like a microservice).
3.  **Business Logic in Services:** Core business logic belongs in Services. Services can communicate with other Services to compose complex operations.
4.  **Database Access in Mappers/DAOs:** Direct database queries belong in Mappers. Use Base methods where possible.
5.  **Entities as Data Carriers:** Pass data between layers using Entities. Only return arrays when specifically required (e.g., `id => value` for select boxes).
6.  **Future-Proofing:** Decouple logic from the UI layer so that switching from a web presenter to a REST API does not require rewriting business logic.
