<?php

namespace App\Http\Controllers\Web\Backend;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\MembershipPlan;
use App\Models\User;
use App\Services\Service;
use App\Services\UserService;
use App\Traits\apiresponse;
use App\Traits\AuthorizesRequest;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    use apiresponse;

    public $userServiceObj;

    public function __construct()
    {
        $this->middleware(['auth', 'role_or_permission:admin|superadmin']);
        $this->userServiceObj = new UserService();
    }

    public function index(Request $request)
    {
        // Authorize: Only admin can view users
        if (!Auth::user()->hasAnyRole(['admin', 'superadmin'])) {
            abort(403, 'You do not have permission to view users');
        }

        if ($request->ajax()) {

            $data = User::where('id', '!=', Auth::id())->latest();

            return DataTables::of($data)

                ->addIndexColumn()

                ->addColumn('role', function ($data) {
                    return $data->getRoleNames()->first();
                })

                ->addColumn('status', function ($data) {

                    $checked = $data->status == 'active' ? 'checked' : '';

                    return '
                 <div class="form-check form-switch form-check-success">
                    <input class="form-check-input"
                        type="checkbox"
                        ' . $checked . '
                        onclick="changeStatus(' . $data->id . ')">
                </div>';
                })

                ->addColumn('bulk_check', function ($data) {
                    return '
                <input type="checkbox"
                class="form-check-input select_data"
                value="' . $data->id . '">';
                })

                ->addColumn('action', function ($data) {

                    $edit = route('admin.user.edit', $data->id);
                    $view = route('admin.user.show', $data->id);

                    return '
<div class="d-flex gap-2">

    <a href="' . $edit . '"
       class="btn btn-sm btn-soft-info btn-icon"
       title="Edit">
        <i class="ri-edit-2-line"></i>
    </a>

    <a href="' . $view . '"
       class="btn btn-sm btn-soft-primary btn-icon"
       title="View">
        <i class="ri-eye-line"></i>
    </a>

    <button type="button"
            onclick="deleteCms(' . $data->id . ')"
            class="btn btn-sm btn-soft-danger btn-icon"
            title="Delete">
        <i class="ri-delete-bin-line"></i>
    </button>

</div>
';
                })

                ->rawColumns(['status', 'action', 'bulk_check'])
                ->make(true);
        }
        return view('backend.layout.user.index');
    }
    public function create()
    {
        // Authorize: Only admin can create users
        if (!Auth::user()->hasAnyRole(['admin', 'superadmin'])) {
            abort(403, 'You do not have permission to create users');
        }

        $roles = Role::all();

        return view('backend.layout.user.create', compact('roles'));
    }

    public function store(Request $request)
    {
        // Authorize: Only admin can store users
        if (!Auth::user()->hasAnyRole(['admin', 'superadmin'])) {
            abort(403, 'You do not have permission to create users');
        }


        $request->validate([
            'role' => 'required',
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6'
        ]);

        $user = User::create([

            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)

        ]);

        $user->assignRole($request->role);

        return redirect()->route('user.list')
            ->with('success', 'User Created Successfully');
    }




    public function edit($id)
    {
        if (!Auth::user()->hasAnyRole(['admin', 'superadmin'])) {
            abort(403, 'You do not have permission to edit users');
        }
        $user = User::findOrFail($id);

        $roles = Role::all();

        return view(
            'backend.layout.user.edit',
            compact('user', 'roles')
        );
    }

    public function update(Request $request)
    {
        if (!Auth::user()->hasAnyRole(['admin', 'superadmin'])) {
            abort(403, 'You do not have permission to update users');
        }
        $user = User::findOrFail($request->id);

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id
        ]);

        $user->update([

            'name' => $request->name,
            'email' => $request->email

        ]);

        if ($request->password) {

            $user->update([
                'password' => Hash::make($request->password)
            ]);
        }

        if ($request->role) {

            $user->syncRoles([$request->role]);
        }

        return redirect()->route('admin.user.list')
            ->with('success', 'User Updated');
    }
    public function show($id)
    {
        return $this->userServiceObj->show($id);
    }

    public function changeStatus($id)
    {
        if (!Auth::user()->hasAnyRole(['admin', 'superadmin'])) {
            abort(403, 'You do not have permission to change users status');
        }
        $user = User::findOrFail($id);

        if ($user->status == 'active') {

            $user->status = 'inactive';
        } else {

            $user->status = 'active';
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Status Updated'
        ]);
    }

    public function destroy(Request $request)
    {
        if (!Auth::user()->hasAnyRole(['admin', 'superadmin'])) {
            abort(403, 'You do not have permission to delelte users');
        }
        if (!Hash::check($request->password, Auth::user()->password)) {

            return response()->json([
                'success' => false,
                'message' => 'Password incorrect'
            ]);
        }

        $user = User::find($request->id);

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User Deleted'
        ]);
    }
    public function bulkDelete(Request $request)
    {

        User::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Users Deleted'
        ]);
    }
}
