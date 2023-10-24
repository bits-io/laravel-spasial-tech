<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Credit;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function store(Request $request)
    {
        // Check user
        if (Auth::user()->role_id != 1) {
            return ResponseHelper::error('Unauthorized', 401);
        }

        // Validate request data
        $validator = Validator::make($request->all(), [
            'credit_id' => 'required',
            'amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return ResponseHelper::error(
                'Validation error',
                400,
                $validator->errors()
            );
        }

        DB::beginTransaction();
        try {
            $creditId = $request->credit_id;
            $amount = $request->amount;

            $credit = Credit::find($creditId);
            if (!$credit) {
                return ResponseHelper::error('Credit not found', 404);
            }

            $payment = new Payment();
            $payment->credit_id = $creditId;
            $payment->amount = $amount;
            $payment->status = 'PENDING';
            $payment->save();


            DB::commit();
            return ResponseHelper::success($payment, 'Payment credit created');
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseHelper::error($th->getMessage(), 500);
        }
    }

    public function index()
    {
        try {
            $payments = Payment::all();
            return ResponseHelper::success($payments, 'Show all payment');
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), 500);
        }
    }

    public function show()
    {

    }

    public function update()
    {

    }
}
