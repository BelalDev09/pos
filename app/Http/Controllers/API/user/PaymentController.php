<?php

namespace App\Http\Controllers\API\user;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentSummaryResource;
use App\Models\Payment;
use App\Traits\apiresponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    use apiresponse;
    protected $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        // === Filter bookings directly (transaction_id is in bookings table) ===
        $query = $user->userbookings()->whereHas('payment')->with('teachinSubject');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('payment', function ($q) use ($search) {
                $q->where('transaction_id', 'like', '%' . $search . '%');
            });
        }

        $bookings = $query
            ->with(['user:id,first_name,last_name,avatar', 'subject', 'payment'])
            ->latest()
            ->paginate(10);

        // === Calculate total amount from related payments ===
        $totalAmount = $bookings->sum(function ($booking) {
            return optional($booking->payment)->amount ?? 0;
        });

        return $this->success([
            'payments' => PaymentSummaryResource::collection($bookings),
            'summary' => [
                'total_amount' => round($totalAmount, 2),
            ]
        ], 'Payment data and summary fetched successfully.', 200);
    }
}
