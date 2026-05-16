<?php

namespace App\Http\Controllers\Web\Backend\Settings;

use App\Helper\Helper;
use App\Models\DynamicPage;
use App\Traits\AuthorizesRequest;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class DynamicPagesController extends Controller
{
    use AuthorizesRequest;

    public function index(Request $request)
    {
        // Authorize: Only admin can view dynamic pages
        $this->authorizeAdmin('You do not have permission to view dynamic pages');

        if ($request->ajax()) {
            $data = DynamicPage::latest();

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
                ->addColumn('row_id', function ($data) {
                    return 'row-' . $data->id;
                })
                ->editColumn('page_content', function ($data) {
                    return Str::limit(strip_tags($data->page_content), 50);
                })
                ->editColumn('status', function ($data) {

                    $checked = $data->status == "active" ? "checked" : "";

                    return '
            <div class="form-check form-switch form-check-success">

            <input class="form-check-input status-toggle"
                   type="checkbox"
                   role="switch"
                   data-id="' . $data->id . '"
                   ' . $checked . '>

        </div>
    ';
                })
                ->addColumn('action', function ($data) {

                    $edit = route('admin.dynamicpages.edit', $data->id);

                    return '
<div class="d-flex gap-2">

    <a href="' . $edit . '"
       class="btn btn-sm btn-soft-info btn-icon"
       title="Edit">
        <i class="ri-edit-2-line"></i>
    </a>

<button type="button"
        class="btn btn-sm btn-soft-danger btn-icon deleteBtn"
        data-id="' . $data->id . '"
        title="Delete">
    <i class="ri-delete-bin-line"></i>
</button>

</div>
';
                })
                ->rawColumns(['bulk_check', 'status', 'action'])
                ->make(true);
        }
        return view('backend.layout.setting.dynamic_page.index');
    }

    public function create()
    {
        // Authorize: Only admin can create dynamic pages
        $this->authorizeAdmin('You do not have permission to create dynamic pages');

        return view('backend.layout.setting.dynamic_page.create');
    }

    public function store(Request $request)
    {
        // Authorize: Only admin can store dynamic pages
        $this->authorizeAdmin('You do not have permission to store dynamic pages');

        $request->validate([
            'page_title'   => 'required|max:255|string',
            'page_content' => 'required',
        ], [
            'page_title.required' => 'The page title field is required.',
            'page_title.max'      => 'The page title may not be greater than 255 characters.',
            'page_title.string'   => 'The page title must be a valid string.',

            'page_content.required' => 'The page content field is required.',
        ]);

        try {
            $page = new DynamicPage();
            $page->page_title = $request->page_title;
            $page->page_content = $request->page_content;
            $slug = Str::slug($request->page_title);
            $count = DynamicPage::where('page_slug', 'like', $slug . '%')->count();

            $page->page_slug = $count ? $slug . '-' . $count : $slug;
            $page->status = 'active';
            $page->save();

            return redirect()->route('admin.dynamicpages.index')
                ->with('success', 'Page created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Something went wrong! Please try again.');
        }
    }


    public function show(string $id) {}

    public function edit(string $id)
    {
        // Authorize: Only admin can edit dynamic pages
        $this->authorizeAdmin('You do not have permission to edit dynamic pages');

        $data = DynamicPage::findOrFail($id);
        return view('backend.layout.setting.dynamic_page.edit', compact('data'));
    }

    public function update(Request $request, string $id)
    {
        // Authorize: Only admin can update dynamic pages
        $this->authorizeAdmin('You do not have permission to update dynamic pages');

        $request->validate([
            'page_title'   => 'required|max:255|string',
            'page_content' => 'required',
        ], [
            'page_title.required' => 'The page title field is required.',
            'page_title.max'      => 'The page title may not be greater than 255 characters.',
            'page_title.string'   => 'The page title must be a valid string.',

            'page_content.required' => 'The page content field is required.',
        ]);

        $page = DynamicPage::findOrFail($id);
        $page->page_title = $request->page_title;
        $page->page_content = $request->page_content;
        $slug = Str::slug($request->page_title);
        $count = DynamicPage::where('page_slug', 'like', $slug . '%')->count();
        $page->page_slug = $count ? $slug . '-' . $count : $slug;
        $page->status = 'active';
        $page->save();

        return redirect()->route('admin.dynamicpages.index')
            ->with('success', 'Page update successfully.');
    }

    public function destroy(string $id)
    {
        // Authorize: Only admin can delete dynamic pages
        $this->authorizeAdmin('You do not have permission to delete dynamic pages');

        try {
            $page = DynamicPage::findOrFail($id);
            $page->delete();
            return response()->json([
                'success' => true,
                'message' => 'Page deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([

                'error' => true,
                "message" => "Failed to delete page."

            ]);
        }
    }

    public function changeStatus($id)
    {
        // Authorize: Only admin can change dynamic page status
        $this->authorizeAdmin('You do not have permission to change dynamic page status');

        $data = DynamicPage::find($id);
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
                'success' => true,
                'message' => 'Unpublished Successfully.',
            ]);
        } else {
            $data->status = 'active';
            $data->save();

            return response()->json([
                'success' => true,
                'message' => $data->status === 'active'
                    ? 'Published Successfully.'
                    : 'Unpublished Successfully.'
            ]);
        }
    }


    public function bulkDelete(Request $request)
    {
        // Authorize: Only admin can bulk delete dynamic pages
        $this->authorizeAdmin('You do not have permission to bulk delete dynamic pages');

        if ($request->ajax()) {
            $result = DynamicPage::whereIn('id', $request->ids)->delete();

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pages deleted successfully',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Pages not found',
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
            ]);
        }
    }
}
