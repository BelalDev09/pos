# Quick Reference - Spatie Authorization in Controllers

## Adding Authorization to a New Controller

### Step 1: Import the Trait
```php
use App\Traits\AuthorizesRequest;

class MyController extends Controller {
    use AuthorizesRequest;
}
```

### Step 2: Add Authorization Check to Each Method
```php
public function index(Request $request) {
    // First line of every public method
    $this->authorizeAdmin('You do not have permission to access this resource');
    
    // Rest of method logic...
}
```

## Authorization Methods

### For Admin Only
```php
$this->authorizeAdmin('Custom error message');
```
Checks: User has 'admin' OR 'superadmin' role

### For Manager Only
```php
$this->authorizeManager('Custom error message');
```
Checks: User has 'manager' role

### For Cashier Only
```php
$this->authorizeCashier('Custom error message');
```
Checks: User has 'cashier' role

### For Multiple Roles (OR condition)
```php
$this->authorizeRole(['admin', 'manager'], 'Custom error message');
```
Checks: User has ANY of the specified roles

### For Specific Permissions
```php
$this->authorizePermission(['create_user', 'edit_user'], 'Custom error message');
```
Checks: User has ANY of the specified permissions

### For Mixed Role+Permission (OR condition)
```php
$this->authorizeRoleOrPermission(
    ['admin', 'manager'],
    ['manage_orders'],
    'Custom error message'
);
```
Checks: User has (ANY role) OR (ANY permission)

## Error Response

When authorization fails:

1. **From Web Request** → Redirects to login with flash message
2. **From API Request** → Returns 403 JSON response
3. **In Controller** → Throws `AuthorizationException`

## Complete Controller Example

```php
<?php

namespace App\Http\Controllers\Web\backend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Traits\AuthorizesRequest;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use AuthorizesRequest;

    // View all products
    public function index()
    {
        $this->authorizeAdmin('You cannot view products');
        $products = Product::all();
        return view('products.index', compact('products'));
    }

    // Show create form
    public function create()
    {
        $this->authorizeAdmin('You cannot create products');
        return view('products.create');
    }

    // Store product
    public function store(Request $request)
    {
        $this->authorizeAdmin('You cannot create products');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        Product::create($validated);
        return redirect()->route('products.index')->with('success', 'Product created');
    }

    // Edit product
    public function edit($id)
    {
        $this->authorizeAdmin('You cannot edit products');
        $product = Product::findOrFail($id);
        return view('products.edit', compact('product'));
    }

    // Update product
    public function update(Request $request, $id)
    {
        $this->authorizeAdmin('You cannot edit products');
        
        $product = Product::findOrFail($id);
        $product->update($request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]));

        return redirect()->route('products.index')->with('success', 'Product updated');
    }

    // Delete product
    public function destroy($id)
    {
        $this->authorizeAdmin('You cannot delete products');
        Product::findOrFail($id)->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted');
    }
}
```

## Testing in Tinker

```php
// Start tinker
php artisan tinker

// Test as admin
$admin = \App\Models\User::role('admin')->first();
Auth::loginAs($admin);

// Try accessing protected route
$response = Auth::user()->can('do something');

// Check authorization trait
$controller = new \App\Http\Controllers\Web\backend\ProductController();
$controller->authorizeAdmin(); // Will pass for admin

// Test as different role
$manager = \App\Models\User::role('manager')->first();
Auth::loginAs($manager);
$controller->authorizeAdmin(); // Will throw exception
```

## Common Mistakes to Avoid

❌ **Wrong** - Authorization not first statement:
```php
public function index() {
    $product = Product::first(); // DON'T DO THIS
    $this->authorizeAdmin(); // Authorization comes too late
}
```

✅ **Right** - Authorization first:
```php
public function index() {
    $this->authorizeAdmin(); // Authorization check first
    $product = Product::first();
}
```

---

❌ **Wrong** - No authorization in public methods:
```php
public function store(Request $request) {
    // Missing authorization!
    Product::create($request->all());
}
```

✅ **Right** - Every public method has check:
```php
public function store(Request $request) {
    $this->authorizeAdmin('Cannot create products');
    Product::create($request->all());
}
```

---

❌ **Wrong** - Wrong method for role:
```php
$this->authorizeAdmin(); // User has 'manager' role
// This will fail - should use authorizeManager()
```

✅ **Right** - Correct method for role:
```php
$this->authorizeManager(); // User has 'manager' role
// This will pass
```

## Troubleshooting

### "AuthorizationException" or "Unauthorized" Error
- Check user has the correct role in database
- Verify `spatie_permission` tables populated correctly
- Confirm request authenticated (check auth middleware)

### Authorization works in browser, fails in API
- API responses return JSON 403 instead of redirect
- Check JWT token includes user roles
- Verify API middleware includes `jwt.verify`

### Need to check authorization without throwing
```php
if (auth()->user()->hasRole('admin')) {
    // User is admin
}

if (auth()->user()->hasPermissionTo('edit_product')) {
    // User can edit product
}
```

## See Also

- [Full Implementation Docs](./AUTHORIZATION_IMPLEMENTATION.md)
- [Spatie Permission Docs](https://spatie.be/docs/laravel-permission)
- [`AuthorizesRequest` Trait](./app/Traits/AuthorizesRequest.php)
