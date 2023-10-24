<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function store(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'email' => 'required|unique:users|email:dns|max:200',
            'password' => 'required|min:6|max:200',
            'phone_number' => 'nullable|max:20',

            'first_name' => 'required|max:200',
            'last_name' => 'nullable|max:200',
            'address' => 'nullable|max:255',
            'gender' => 'required|max:20',
            'date_of_birth' => 'required|date',
            'img_ktp' => 'nullable|file|max:2048'
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


            DB::commit();
            return ResponseHelper::success();
        } catch (\Throwable $th) {
            DB::rollBack();
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
