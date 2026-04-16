# SKP Project Documentation Index

Welcome to the project's knowledge base. This directory contains structured information about the system architecture, database schema, and modules.

## Documentation Index
- [**Rules.md**](rules.md): Core coding standards and architectural mandates.
- [**Model Base**](model_base.md): Detailed summary of the base classes in `app/Model/Base/`.
- [**Database Schema**](database.md): Complete documentation of the `skp` database tables and their structure.
- [**Frontend Module**](modules/Frontend.md): Documentation for the `FrontendRunner` orchestrator.
- [**Forms Module**](modules/Forms.md): Documentation for the `Forms` tool module.
- [**Modules Index**](modules/): Detailed documentation for core modules (Page, PageGroup, Component, Admin, etc.).

## Architecture Highlights
- **Framework:** Nette 3.1, PHP 8.5+, Dibi 5.1.
- **Pattern:** Ebcar architecture (Entity -> Mapper -> Dao -> Facade -> Service).
- **Frontend:** AdminLTE 3 (AdminModule) and public FrontModule.
- **Translations:** Built-in multi-language support for entities.
- **Auditing:** Automatic change logging integrated into the BaseMapper layer.

## Quick References
- **Domain:** `skp.dev`
- **Admin Dashboard:** `http://skp.dev/admin/dashboard`
- **DB Settings:** `config.local.neon` (localhost:3306, user: root, db: skp)

---
*This documentation is maintained by Gemini CLI to optimize codebase understanding and performance.*
