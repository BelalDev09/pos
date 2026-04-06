# Spatie Role & Permission Implementation Guide

## Overview
This document outlines the comprehensive application of Spatie Role & Permission middleware across all routes and controllers in the application.

## Files Modified

### 1. **routes/backend.php**
**Purpose**: Admin panel routes
**Middleware Applied**: `['auth', 'verified', 'role_or_permission:admin|superadmin']` (Global)

**Protected Routes**:
- Dashboard (`/admin/dashboard`)
- Settings (`/admin/general/setting`, `/admin/setting`, etc.)
- Profile Settings (`/admin/profile`, `/admin/profile/update`)
- FAQ Management `/admin/faq/*`
- User Management `/admin/users/*`
- Staff Management `/admin/staffs/*`
- Restaurant Management `/admin/restaurants/*`
- Category Management `/admin/categories/*`
- Order Management `/admin/order/*`
- Customer Management `/admin/customers/*`
- Menu Items `/admin/menu_item*`
- Order History `/admin/order_status_history`
- Activity Logs `/admin/activity_logs`
- Menu Ingredients `/admin/menu_item_ingredients*`
- Dynamic Pages `/admin/dynamicpages*`
- Permissions `/admin/permissions/*`
- Roles `/admin/role/*`
- Restaurant Tables `/admin/restaurant_tables/*`

### 2. **routes/web.php**
**Purpose**: Web application routes for different user roles

**Manager Routes** (`/manager/*`)
- Middleware: `['auth', 'verified', 'role_or_permission:manager']`
- Dashboard: `/manager-dashboard`
- Operations: Approvals, Discounts, Staff Control, Kitchen Monitor, Reports, Cash Management

**Cashier Routes** (`/cashier/*`)
- Middleware: `['auth', 'verified', 'role_or_permission:cashier']`
- Dashboard: `/cashier/dashboard`
- Operations: Orders, Payments, Discounts, Tips, Refunds

**Admin Dashboard** (`/dashboard`)
- Middleware: `['auth', 'verified', 'role_or_permission:admin|superadmin']`

**Public Routes** (No authentication required):
- Home page: `/`
- Error pages: `/error/*`
- QR Table Display: `/table/{token}`
- QR Image: `/qr/image/{token}`
- Table Order Submit: `/table/{token}/order`

### 3. **routes/api.php**
**Purpose**: API endpoints for mobile/external apps

**JWT Authentication**: All protected API routes use `jwt.verify` middleware

**Cashier API Routes** (`/api/user/cashier/*`)
- Middleware: `role_or_permission:cashier`
- Orders, Payments, Discounts, Tips, Refunds, Reports

**Manager API Routes** (`/api/user/manager/*`)
- Middleware: `role_or_permission:manager`
- Dashboard, Approvals, Promotions, Cash Management, Shifts, Kitchen, Reports

**Public API Routes**:
- User Auth: `/api/user/login`, `/api/user/register`
- Customer Operations: `/api/customer/*`
- Profile: `/api/profile/*`

### 4. **routes/manager.php**
**Purpose**: Manager-specific routes (legacy/separate file)
**Middleware Applied**: `['auth', 'verified', 'role_or_permission:manager']`

### 5. **routes/user.php**
**Purpose**: User account operations
**Middleware Applied**: `['jwt.verify']` (JWT authentication only, no role restriction)

## Middleware Configuration

Located in `bootstrap/app.php`:

```php
$middleware->alias([
    'admin' => AdminMiddleware::class,
    'role_or_admin' => \App\Http\Middleware\RoleOrAdmin::class,
    'jwt.verify' => JWTMiddleware::class,
    'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
    'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
    'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
]);
```

## Available Roles

| Role | Permissions | Access Level |
|------|-----------|--------------|
| `admin` | All admin operations | Full backend access |
| `superadmin` | All operations | Full system access |
| `manager` | Approvals, Reports, Staff, Kitchen, Cash | Manager dashboard |
| `cashier` | Orders, Payments, Discounts, Tips, Refunds | Cashier dashboard |
| `customer` | Browse menu, place orders | Customer portal |

## Authentication Methods

### 1. **Web Authentication** (Session-based)
- Routes: `web.php`, `backend.php`
- Guard: `web` (Laravel default)
- Middleware: `auth`, `verified`
- Login Redirect: See [login redirect logic](../app/Http/Controllers/Auth/AuthenticatedSessionController.php)

### 2. **API Authentication** (Token-based JWT)
- Routes: `api.php`, `user.php`
- Guard: `api` (JWT)
- Middleware: `jwt.verify`
- Token: Bearer token in Authorization header

## Login Redirects

The application automatically redirects users to relevant dashboards after login based on their role:
- **Admin/Superadmin** → `/admin/dashboard`
- **Manager** → `/manager-dashboard`
- **Cashier** → `/cashier/dashboard`

See: [AuthenticatedSessionController.php](../app/Http/Controllers/Auth/AuthenticatedSessionController.php)

## Authorization Checks

### Route-level (Middleware)
All routes have role/permission middleware applied at the route level preventing unauthorized access.

### Controller-level (Optional)
For fine-grained control, use Gate or Policy authorization:

```php
// In controller methods
if (!auth()->user()->can('manage.category')) {
    abort(403);
}

// Or using Gate
if (Gate::denies('edit-post', $post)) {
    abort(403);
}
```

## Examples

### Accessing Admin Routes
```
GET /admin/dashboard
Headers: 
  - Cookie: XSRF-TOKEN=..., laravel_session=...
  - User: Must have 'admin' or 'superadmin' role
```

### Accessing API Routes
```
GET /api/user/cashier/orders
Headers:
  - Authorization: Bearer {jwt_token}
  - User (from token): Must have 'cashier' role
```

### Accessing Manager Routes
```
GET /manager-dashboard
Headers:
  - Cookie: XSRF-TOKEN=..., laravel_session=...
  - User: Must have 'manager' role
```

## Testing

### Verify Middleware is Applied
```bash
php artisan route:list | grep "role_or_permission"
```

### Test Route Protection
```php
// In test
$this->actingAs($cashierUser)
     ->get('/admin/dashboard')
     ->assertForbidden(); // Should be 403

$this->actingAs($adminUser)
     ->get('/admin/dashboard')
     ->assertOk(); // Should be 200
```

## Troubleshooting

### 403 Forbidden Errors
1. Verify user has correct role assigned in database
2. Check middleware is applied to route: `php artisan route:list`
3. Verify role name matches exactly (case-sensitive)

### JWT Token Issues
1. Verify token is valid: `JWTAuth::parseToken()->check()`
2. Check token expiration
3. Verify secret key in `.env` matches JWT configuration

### Role Not Recognized
1. Verify role exists in `roles` table
2. Verify user has role assigned in `model_has_roles` table
3. Clear cache: `php artisan cache:clear`

## Security Notes

1. **Always use `role_or_permission`** for flexibility - allows role OR permission matching
2. **Never trust client-side validation** - always validate on server
3. **Use policies** for complex authorization logic
4. **Log unauthorized access** attempts for security monitoring
5. **Implement rate limiting** on sensitive endpoints
6. **Use HTTPS only** for production authentication

## Future Enhancements

1. Add permission-level checks in addition to roles
2. Implement audit logging for sensitive operations
3. Add 2FA for admin accounts
4. Add role-based API rate limiting
5. Implement activity dashboard showing role-based access patterns
