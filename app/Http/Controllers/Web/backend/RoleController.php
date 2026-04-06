<?php

namespace App\Http\Controllers\Web\backend;

use App\Http\Controllers\Controller;
use App\Traits\AuthorizesRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        // Middleware: manager, admin, superadmin can access
        $this->middleware(['auth', 'role_or_permission:admin|superadmin']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Authorize: Only admin can view roles
        if (!Auth::user()->hasAnyRole(['admin', 'superadmin'])) {
            abort(403, 'You do not have permission to view roles');
        }

        $roles = Role::get();
        return view('backend.layout.role_permissions.role.index', get_defined_vars());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Authorize: Only admin can create roles
        if (!Auth::user()->hasAnyRole(['admin', 'superadmin'])) {
            abort(403, 'You do not have permission to create roles');
        }

        $permissions = Permission::get();
        return view('backend.layout.role_permissions.role.create', get_defined_vars());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->hasAnyRole(['admin', 'superadmin'])) {
            abort(403, 'You do not have permission to store roles');
        }
        $request->validate([
            'name' => 'required|unique:roles,name',
        ]);
        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permissions);
        flash()->success('Role Created Successfully!');

        return redirect()->route('admin.roles.list');
    }


    /**
     * Display the specified resource.
     */
    public function show() {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if (!Auth::user()->hasAnyRole(['admin', 'superadmin'])) {
            abort(403, 'You do not have permission to edit roles');
        }
        $permissions = Permission::get();
        $role = Role::find($id);
        $rolePermissions = DB::table('role_has_permissions')
            ->where('role_has_permissions.role_id', $role->id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();
        return view('backend.layout.role_permissions.role.add_permissions', get_defined_vars());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (!Auth::user()->hasAnyRole(['admin', 'superadmin'])) {
            abort(403, 'You do not have permission to update roles');
        }
        $request->validate([
            'permissions' => 'required|array',
        ]);
        $role = Role::find($id);
        $role->syncPermissions($request->permissions);
        flash()->success('Add Permissions Succefully!');
        return redirect()->route('admin.roles.list');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!Auth::user()->hasAnyRole(['admin', 'superadmin'])) {
            abort(403, 'You do not have permission to delete roles');
        }
        try {
            $role = Role::find($id);

            if (!$role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role not found.',
                ], 404);
            }
            $role->delete();

            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully!',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the role. Please try again.',
            ], 500);
        }
    }
}
