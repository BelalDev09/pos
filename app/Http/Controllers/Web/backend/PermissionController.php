<?php

namespace App\Http\Controllers\Web\Backend;

use App\Http\Controllers\Controller;
use App\Traits\AuthorizesRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function __construct()
    {
        // Middleware: manager, admin, superadmin can access
        $this->middleware(['auth', 'role_or_permission:admin|superadmin']);
    }

    /**
     * Display all permissions
     */
    public function index()
    {
        // Authorize: Only admin can view permissions
        if (!Auth::user()->hasAnyRole(['admin', 'superadmin'])) {
            abort(403, 'You do not have permission to view permissions');
        }

        $permissions = Permission::latest()->paginate(10);
        return view('backend.layout.role_permissions.permissions.index', compact('permissions'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        // Authorize: Only admin can create permissions
        if (!Auth::user()->hasAnyRole(['admin', 'superadmin'])) {
            abort(403, 'You do not have permission to create permissions');
        }

        return view('backend.layout.role_permissions.permissions.create');
    }

    /**
     * Store new permission
     */
    public function store(Request $request)
    {
        if (!Auth::user()->hasAnyRole(['admin', 'superadmin'])) {
            abort(403, 'You do not have permission to store permissions');
        }
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        Permission::create([
            'name' => strtolower($validated['name']),
        ]);

        return redirect()->route('admin.permissions.list')
            ->with('success', 'Permission Created Successfully!');
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        if (!Auth::user()->hasAnyRole(['admin', 'superadmin'])) {
            abort(403, 'You do not have permission to edit permissions');
        }
        $permission = Permission::findOrFail($id);
        return view('backend.layout.role_permissions.permissions.edit', compact('permission'));
    }

    /**
     * Update permission
     */
    public function update(Request $request, $id)
    {
        if (!Auth::user()->hasAnyRole(['admin', 'superadmin'])) {
            abort(403, 'You do not have permission to update permissions');
        }
        $permission = Permission::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $id,
        ]);

        $permission->update([
            'name' => strtolower($validated['name']),
        ]);

        return redirect()->route('admin.permissions.list')
            ->with('success', 'Permission Updated Successfully!');
    }

    /**
     * Delete permission
     */
    public function destroy($id)
    {
        if (!Auth::user()->hasAnyRole(['admin', 'superadmin'])) {
            abort(403, 'You do not have permission to delete permissions');
        }
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return response()->json([
            'status' => true,
            'message' => 'Permission deleted successfully!'
        ]);
    }
}
