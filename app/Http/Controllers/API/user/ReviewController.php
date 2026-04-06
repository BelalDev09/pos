<?php

namespace App\Http\Controllers\API\user;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\user\ReviewResource;
use App\Models\Booking;
use App\Models\Review;
use App\Models\User;
use App\Traits\apiresponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    use apiresponse;
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'teaching_subject_id' => 'required|integer|exists:teaching_subjects,id',
            'comment'           => 'required|string',
            'rating'        => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->error([], [$validator->errors()->first()], 422);
        }

        $user = auth()->user();
        // Check if review already exists
        $existingReview = $user->userReviews()->where('teaching_subject_id', $request->teaching_subject_id)->first();

        if ($existingReview) {
            return $this->success([], ['You have already reviewed this tutor.'], 200);
        }
        $user->userReviews()->create([
            'teaching_subject_id' => $request->teaching_subject_id,
            'comment' => $request->comment,
            'rating' => $request->rating,
        ]);
        return $this->success([], 'Review added successfully', 200);
    }

    public function get()
    {
        $user = auth()->user();

        $reviews = $user->userReviews()
            ->with(['teachingSubjects.subject:id,subject_name', 'teachingSubjects.user:id,first_name,last_name,avatar'])
            ->latest()
            ->get();

        // Calculate summary
        $pendingCount = $reviews->where('status', 'pending')->count();
        $completedCount = $reviews->where('status', 'approved')->count();
        $averageRating = $reviews->where('status', 'approved')->avg('rating');

        return $this->success([
            'data' => $reviews,
            'summary' => [
                'pending_reviews' => $pendingCount,
                'completed_reviews' => $completedCount,
                'average_rating' => round($averageRating, 1),
            ]
        ], 'Reviews fetched successfully', 200);
    }




    public function edit($id)
    {
        $review = auth()->user()->userReviews()->find($id);

        if (!$review) {
            return $this->error([], 'Review not found', 404);
        }

        if ($review->status != 'pending') {
            return $this->error('Review already approved', 400);
        }
        return $this->success(['data' => $review], 'Review fetched successfully', 200);
    }


    public function update(Request $request, $id)
    {
        $review = auth()->user()->userReviews()->find($id);

        if (!$review) {
            return $this->error([], 'Review not found', 404);
        }

        if ($review->status == 'approved') {
            return $this->error([], 'Review already approved', 400);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'required|numeric|min:1|max:5',
            'comment' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return $this->success([
            'data' => ReviewResource::make($review->load([
                'teachingSubjects.subject',
                'teachingSubjects.user'
            ]))
        ], 'Review updated successfully', 200);
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'review_id' => 'required|integer|exists:reviews,id',
        ]);
        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }
        $review = auth()->user()->userReviews()->find($request->review_id);

        if (!$review) {
            return $this->error([], 'Review not found', 404);
        }

        if ($review->status == 'approved') {
            return $this->error('Review already approved', 400);
        }

        $review->delete();

        return $this->success([], 'Review deleted successfully', 200);
    }


    public function myCompletedReviews(Request $request)
    {
        $user = auth()->user();

        $reviews = $user->userReviews()
            ->with(['teachingSubjects.subject:id,subject_name', 'teachingSubjects.user:id,first_name,last_name,avatar'])
            ->where('status', 'approved')
            ->latest()
            ->get()
            ->map(function ($review) {
                $review->formatted_created_at = Carbon::parse($review->created_at)->format('d M Y'); // e.g., "15 Sep 2025"
                return $review;
            });

        return $this->success(['data' => $reviews], 'Reviews fetched successfully', 200);
    }


    public function index()
    {
        $user = auth()->user();
        $reviews = $user->userReviews()
            ->with(['teachingSubjects.subject:id,subject_name', 'teachingSubjects.user:id,first_name,last_name,avatar'])
            ->where('status', 'pending')
            ->latest()
            ->get()
            ->map(function ($review) {
                $review->formatted_created_at = Carbon::parse($review->created_at)->format('d M Y'); // e.g., "15 Sep 2025"
                return $review;
            });
        return $this->success(['data' => $reviews], 'Reviews fetched successfully', 200);
    }
}
