<?php

namespace App\Http\Controllers\Web\backend;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Traits\AuthorizesRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $category;
    public function __construct(Category $category)
    {
        $this->middleware(['auth', 'role_or_permission:admin|super_admin']);
        $this->category = $category;
    }

    public function index(Request $request)
    {
        // Authorize: Only admin can view categories
        if (!Auth::user()->hasAnyRole(['cashier', 'admin', 'super_admin'])) {
            abort(403, 'You do not have permission to view approval requests');
        } {
            if ($request->ajax()) {
                $data = $this->category->get();

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('bulk_check', function ($data) {
                        return '<div class="form-checkbox">
                                <input type="checkbox" class="form-check-input select_data"
                                       id="checkbox-' . $data->id . '"
                                       value="' . $data->id . '"
                                       onClick="select_single_item(' . $data->id . ')">
                                <label class="form-check-label" for="checkbox-' . $data->id . '"></label>
                            </div>';
                    })
                    ->addColumn('name', function ($data) {
                        return $data->name;
                    })
                    // ->editColumn('image', function ($data) {
                    //     $url = asset($data->image);
                    //     if ($url) {
                    //         return '<img src="' . $url . '" alt="Image" width="80" height="80" style="object-fit: cover;">';
                    //     }
                    //     return 'No images';
                    // })
                    ->editColumn('status', function ($data) {
                        return '<div class="form-check form-switch mb-2"><input type="checkbox" class="form-check-input"
                            onclick="changeStatus(event,' . $data->id . ')"
                            ' . ($data->status == "active" ? "checked" : "") . '></div>';
                    })
                    ->addColumn('action', function ($data) {
                        return '<a href="' . route('admin.categories.edit', $data->id) . '" class="btn btn-sm btn-primary">
                                <i class="ri-edit-box-line"></i>
                            </a>
                            <button type="button" onclick="showDeleteAlert(' . $data->id . ')" class="btn btn-sm btn-danger">
                                <i class="ri-delete-bin-line"></i>
                            </button>';
                    })
                    ->rawColumns(['bulk_check', 'status', 'action'])
                    ->make(true);
            }
            return view('backend.layout.categories.index');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!Auth::user()->hasAnyRole(['admin', 'super_admin'])) {
            abort(403, 'You do not have permission to create category');
        }
        $categories = Category::where('status', 'active')->get();
        return view('backend.layout.categories.create', compact('categories'));
        // return redirect()->route('categories.index', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        if (!Auth::user()->hasAnyRole(['admin', 'super_admin'])) {
            abort(403, 'You do not have permission to store category');
        }
        try {

            $file_url = null;

            if ($request->hasFile('image')) {
                $file_url = Helper::fileUpload(
                    $request->file('image'),
                    'categories',
                    $request->name . '_' . time()
                );
            }

            $category = new Category();
            $category->name = $request->name;
            $category->slug = Str::slug($request->name);
            $category->status = 'active';
            $category->save();

            flash()->success('Category created successfully.');
            return redirect()->route('admin.categories.index');
        } catch (Exception $e) {

            dd($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        if (!Auth::user()->hasAnyRole(['admin', 'super_admin'])) {
            abort(403, 'You do not have permission to edit category');
        }
        $data = $this->category->findOrFail($id);
        $categories = Category::where('status', 'active')->get();
        return view('backend.layout.categories.edit', compact('data', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, string $id)
    {
        if (!Auth::user()->hasAnyRole(['admin', 'super_admin'])) {
            abort(403, 'You do not have permission to update category');
        }
        $category = $this->category->find($id);
        // $file_url = $category->image;

        // if ($request->hasFile('image')) {
        //     if (!empty($category->image) && file_exists(public_path($category->image))) {
        //         @unlink(public_path($category->image));
        //     }
        //     $file_url = Helper::fileUpload(
        //         $request->file('image'),
        //         'categories',
        //         $request->name . '_' . time()
        //     );
        // }

        try {
            $category->name = $request->name;
            $category->slug = Str::slug($request->name);
            // $category->description = $request->description;
            // $category->image = $file_url;
            $category->save();

            flash()->success('category updated successfully.');
            return redirect()->route('categories.index');
        } catch (Exception $e) {
            dd($e->getMessage());
            flash()->error('Something went wrong! Please try again.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!Auth::user()->hasAnyRole(['admin', 'super_admin'])) {
            abort(403, 'You do not have permission to delete category');
        }
        try {
            $category = $this->category->findOrFail($id);

            // Assuming the file path is stored in $category->file or similar
            if ($category->file && file_exists(public_path($category->file))) {
                unlink(public_path($category->file));
            }

            $category->delete();

            flash()->success('category item deleted successfully');
            return response()->json([
                'success' => true,
                'message' => 'category item deleted successfully.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Failed to delete category item.'
            ]);
        }
    }

    public function changeStatus($id)
    {
        if (!Auth::user()->hasAnyRole(['admin', 'super_admin'])) {
            abort(403, 'You do not have permission to change status category');
        }
        $data = $this->category->find($id);
        if (empty($data)) {
            return response()->json([
                "success" => false,
                "message" => "Item not found."
            ], 404);
        }


        // Toggle status
        if ($data->status == 'active') {
            $data->status = 'inactive';
            $data->save();

            return response()->json([
                'success' => false,
                'message' => 'Unpublished Successfully.',
                'data' => $data,
            ]);
        } else {
            $data->status = 'active';
            $data->save();

            return response()->json([
                'success' => true,
                'message' => 'Published Successfully.',
                'data' => $data,
            ]);
        }
        $data->save();
        return response()->json([
            'success' => true,
            'message' => 'Item status changed successfully.'
        ]);
    }

    public function bulkDelete(Request $request)
    {
        if (!Auth::user()->hasAnyRole(['admin', 'super_admin'])) {
            abort(403, 'You do not have permission to bulk categories data');
        }
        $ids = $request->ids;
        try {
            $this->category->whereIn('id', $ids)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Selected items deleted successfully.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Failed to delete selected items.'
            ]);
        }
    }
}
