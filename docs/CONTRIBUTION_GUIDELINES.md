# 👥 DineFlow AI - Team Collaboration & Git Contribution Guidelines

## 1. Branching Strategy & Workflow Model

DineFlow AI follows a modified GitFlow branching strategy:

- **`main`**: Production-ready, verified, and rock-solid code.
- **`develop`**: Central integration branch for ongoing sprint deliverables.
- **`feature-teamlead`**: Lead developer features, backend architecture, and core integrations.
- **`feature-krushank`**: Menu management and offline desktop modules.
- **`feature-fiyans`**: Inventory management and table QR generator modules.
- **`feature-veer`**: Customer cart and order tracking modules.

---

## 2. Commit Message Conventions

We follow the **Conventional Commits** specification:
- `feat:` New user-facing feature or API capability
- `fix:` Bug fix or error resolution
- `docs:` Documentation updates and architectural runbooks
- `test:` Adding or updating automated tests
- `refactor:` Code refactoring without behavioral alterations
- `chore:` Dependency maintenance, build assets, and directory sync

---

## 3. Public Asset Synchronization Rule

Whenever modifying files in `frontend/customer/` or `frontend/admin/`, always synchronize both Laravel public trees:
```powershell
xcopy "frontend\customer\*" "public\customer\" /Y /E /I
xcopy "frontend\customer\*" "public\frontend\customer\" /Y /E /I
xcopy "frontend\admin\*" "public\admin\" /Y /E /I
xcopy "frontend\admin\*" "public\frontend\admin\" /Y /E /I
xcopy "frontend\*" "public\frontend\" /Y /E /I
```
