<?php

namespace App\Http\Controllers\Web\backend\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\FAQ;
use App\Traits\AuthorizesRequest;
use Exception;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class FAQController extends Controller
{
    use AuthorizesRequest;

    public function get(Request $request)
    {
        // Authorize: Only admin can get FAQs
        $this->authorizeAdmin('You do not have permission to access FAQs');

        $query = FAQ::query();

        if (!empty($request->id)) {
            $query->where('id', $request->id);
        }

        $faqs = $query->get();

        return response()->json($faqs);
    }

    public function index()
    {
        // Authorize: Only admin can view FAQs
        $this->authorizeAdmin('You do not have permission to view FAQs');

        $data['faqs'] = FAQ::all();
        return view('backend.layout.faq.index', $data);
    }

    public function store(Request $request)
    {
        // Authorize: Only admin can create FAQs
        $this->authorizeAdmin('You do not have permission to create FAQs');

        $data = $request->all();

        $validator = Validator::make($data, [
            'que' => 'required|string|max:255',
            'ans' => 'required|string|min:10',
        ], [
            'que.required' => 'The question field is required.',
            'que.string'   => 'The question must be a string.',
            'que.max'      => 'The question cannot exceed 255 characters.',
            // 'que.unique'   => 'This question already exists in the FAQ.',
            'ans.required' => 'The answer field is required.',
            'ans.string'   => 'The answer must be a string.',
            'ans.min'      => 'The answer must be at least 10 characters long.',
        ]);

        if ($validator->fails()) {
            // return response()->json(['errors' => $validator->errors()], 422);
            return back()->with('error', $validator->errors()->first())->withInput();
        }

        try {
            FAQ::create($data);
            // return response()->json(['message' => 'Service created successfully!', 'service' => $service], 201);
            return back()->with('success', 'faq successfully created');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        // Authorize: Only admin can update FAQs
        $this->authorizeAdmin('You do not have permission to update FAQs');

        $data = $request->all();

        $validator = Validator::make($data, [
            'que' => 'required|string|max:255',
            'ans' => 'required|string|min:10',
        ], [
            'que.required' => 'The question field is required.',
            'que.string'   => 'The question must be a string.',
            'que.max'      => 'The question cannot exceed 255 characters.',
            // 'que.unique'   => 'This question already exists in the FAQ.',
            'ans.required' => 'The answer field is required.',
            'ans.string'   => 'The answer must be a string.',
            'ans.min'      => 'The answer must be at least 10 characters long.',
        ]);

        if ($validator->fails()) {
            // return response()->json(['errors' => $validator->errors()], 422);
            return back()->with('error', $validator->errors()->first())->withInput();
        }

        try {
            FAQ::find($request->id)->update($data);
            // return response()->json(['message' => 'Service created successfully!', 'service' => $service], 201);
            return back()->with('success', 'faq successfully created');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        // Authorize: Only admin can delete FAQs
        $this->authorizeAdmin('You do not have permission to delete FAQs');

        $delete = FAQ::find($id)->update([
            'priority' => 0
        ]);
        $delete = FAQ::find($id)->delete();
        if ($delete) {
            return back()->with('success', 'Deleted Successfully');
        } else {
            return back()->with('error', 'Try Again!');
        }
    }

    public function status(Request $request)
    {
        // Authorize: Only admin can update FAQ status
        $this->authorizeAdmin('You do not have permission to update FAQ status');

        $faq = FAQ::find($request->id);


        if ($faq->status == 'active') {
            $faq->update([
                'status' => 'inactive',
            ]);
        } else {
            $faq->update([
                'status' => 'active',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status Updated'
        ]);
    }
}
