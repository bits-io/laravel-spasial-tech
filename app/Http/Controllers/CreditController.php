<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Credit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CreditController extends Controller
{
    public function store(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'credit_type' => 'required|max:200',
            'name' => 'required|max:200',
            'total_transaction' => 'required',
            'tenor' => 'required',
            'total_credit' => 'required',
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
            // Check user
            if (Auth::user()->role_id != 3) {
                return ResponseHelper::error('Unauthorized', 401);
            }

            $userId = Auth::user()->id;
            $creditType = $request->credit_type;
            $name = $request->name;
            $totalTransaction = $request->total_transaction;
            $tenor = $request->tenor;
            $totalCredit = $request->total_credit;
            $status = 'PROCESSED';

            // create credits
            $credits = new Credit();
            $credits->user_id = $userId;
            $credits->credit_type = $creditType;
            $credits->name = $name;
            $credits->total_transaction = $totalTransaction;
            $credits->tenor = $tenor;
            $credits->total_credit = $totalCredit;
            $credits->status = $status;
            $credits->save();

            DB::commit();
            return ResponseHelper::success($credits, 'credit created', 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseHelper::error($th->getMessage(), 500);
        }
    }

    public function index()
    {
        try {
            $credits = Credit::all();
            return ResponseHelper::success($credits, 'Show all credit');
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $credit = Credit::find($id);
            return ResponseHelper::success($credit, 'Show detail credit');
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), 500);
        }
    }

    public function update()
    {

    }
}
