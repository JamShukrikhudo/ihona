# User Model Authorization

> 15 nodes

## Key Concepts

- **User** (15 connections) — `app/Models/User.php`
- **.hasRoleInAnyTeam()** (4 connections) — `app/Models/User.php`
- **.hasAdminAccess()** (3 connections) — `app/Models/User.php`
- **.isSuperAdmin()** (3 connections) — `app/Models/User.php`
- **.canAccessPanel()** (2 connections) — `app/Models/User.php`
- **.dashboardRole()** (2 connections) — `app/Models/User.php`
- **.isAdmin()** (2 connections) — `app/Models/User.php`
- **.profilePhotoUrl()** (1 connections) — `app/Models/User.php`
- **.getTenants()** (1 connections) — `app/Models/User.php`
- **.canAccessTenant()** (1 connections) — `app/Models/User.php`
- **.authorizationIdentifier()** (1 connections) — `app/Models/User.php`
- **.authorizationType()** (1 connections) — `app/Models/User.php`
- **.getDefaultTenant()** (1 connections) — `app/Models/User.php`
- **.latestTeam()** (1 connections) — `app/Models/User.php`
- **.getActivitylogOptions()** (1 connections) — `app/Models/User.php`

## Relationships

- [[Auth Views and Panel Commit History]] (1 shared connections)

## Source Files

- `app/Models/User.php`

## Audit Trail

- EXTRACTED: 39 (100%)
- INFERRED: 0 (0%)
- AMBIGUOUS: 0 (0%)

---

*Part of the graphify knowledge wiki. See [[index]] to navigate.*