<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $firstName = $request->first_name;
            $lastName = $request->last_name;
            $address = $request->address;
            $gender = $request->gender;
            $role = $request->role;

            $perPage = $request->per_page;

            $query = User::query();


            $query->when($firstName ?? false, fn($query, $firstName) =>
                $query->whereHas('user_detail', fn($query) =>
                    $query->where('first_name', $firstName)
                )
            );

            $query->when($lastName ?? false, fn($query, $lastName) =>
                $query->whereHas('user_detail', fn($query) =>
                    $query->where('last_name', $lastName)
                )
            );

            $query->when($address ?? false, fn($query, $address) =>
                $query->whereHas('user_detail', fn($query) =>
                    $query->where('address', $address)
                )
            );

            $query->when($gender ?? false, fn($query, $gender) =>
                $query->whereHas('user_detail', fn($query) =>
                    $query->where('gender', $gender)
                )
            );

            $query->when($role ?? false, fn($query, $role) =>
                $query->whereHas('role', fn($query) =>
                    $query->where('name', $role)
                )
            );

            $users = $query->paginate($perPage);


            return ResponseHelper::success($users,'Show all user');
        } catch (\Throwable $th) {
            return ResponseHelper::error($th->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
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
            // Define data
            $roleId = $request->role_id;
            $email = $request->email;
            $password = $request->password;
            $phoneNumber = $request->phone_number;

            $firstName = $request->first_name;
            $lastName = $request->last_name;
            $address = $request->address;
            $gender = $request->gender;
            $dateOfBirth = $request->date_of_birth;

            // Check if image exist
            if ($request->hasFile('img_ktp')) {
                $image = $request->file('img_ktp');
                $filename = Storage::disk('public')->put('user/image', $image);
                // Custom name
                $customName = '/user/image/'  . Str::slug($request->input('first_name')) . '-' . date('Ymdhis') . uniqid() . '.' . $image->getClientOriginalExtension();
                Storage::move($filename, 'public/user/image/' . $customName);
                $imgKtp = $customName;
            }

            // Create user and detail user
            $user = new User();
            $user->role_id = $roleId;
            $user->email = $email;
            $user->password = Hash::make($password);
            $user->phone_number = $phoneNumber;
            $user->save();

            $userDetail = new UserDetail();
            $userDetail->user_id = $user->id;
            $userDetail->first_name = $firstName;
            $userDetail->last_name = $lastName;
            $userDetail->address = $address;
            $userDetail->gender = $gender;
            $userDetail->date_of_birth = $dateOfBirth;

            if ($request->hasFile('img_ktp')) {
                $userDetail->img_ktp = $imgKtp;
            }

            $userDetail->save();


            DB::commit();
            return ResponseHelper::success(
                [
                    'user' => $user,
                    'user_detail' => $userDetail
                ]
                , 'User created');
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseHelper::error($th->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::find($id);
        return ResponseHelper::success($user, 'Show detail user');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'email' => 'required|email:dns|max:200|unique:users,email,'.$id,
            'password' => 'nullable|min:6|max:200',
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
            // Define data
            $roleId = $request->role_id;
            $email = $request->email;
            $password = $request->password;
            $phoneNumber = $request->phone_number;

            $firstName = $request->first_name;
            $lastName = $request->last_name;
            $address = $request->address;
            $gender = $request->gender;
            $dateOfBirth = $request->date_of_birth;

            // Check user exist
            $user = User::find($id);
            if (!$user) {
                return ResponseHelper::error('User not found', 404);
            }

            // Check if image exist
            if ($request->hasFile('img_ktp')) {
                $image = $request->file('img_ktp');
                $filename = Storage::disk('public')->put('user/image', $image);
                // Custom name
                $customName = '/user/image/'  . Str::slug($request->input('first_name')) . '-' . date('Ymdhis') . uniqid() . '.' . $image->getClientOriginalExtension();
                Storage::move($filename, 'public/user/image/' . $customName);
                $imgKtp = $customName;
            }

            // Update user and detail user
            $user->role_id = $roleId;
            $user->email = $email;

            if ($password) {
                $user->password = $password;
            }
            if ($phoneNumber) {
                $user->phone_number = $phoneNumber;
            }
            $user->save();

            $userDetail = UserDetail::where('user_id', $user->id)->first();

            $userDetail->first_name = $firstName;
            $userDetail->gender = $gender;
            $userDetail->date_of_birth = $dateOfBirth;
            $userDetail->updated_at = now();

            if ($lastName) {
                $user->last_name = $lastName;
            }
            if ($address) {
                $user->address = $address;
            }

            if ($request->hasFile('img_ktp')) {
                $userDetail->img_ktp = $imgKtp;
            }
            $userDetail->save();

            DB::commit();
            return ResponseHelper::success(
                [
                    'user' => $user,
                ]
                , 'User created');
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseHelper::error($th->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $user = User::find($id);
            if (!$user) {
                return ResponseHelper::error('User not found', 404);
            }

            $userDetail = UserDetail::where('user_id', $user->id)->first();



            $userDetail->delete();
            $user->delete();

            DB::commit();
            return ResponseHelper::success($userDetail);
        } catch (\Throwable $th) {
            DB::rollBack();
            return ResponseHelper::error($th->getMessage(), 500);
        }
        $user = User::find($id);

        return ResponseHelper::success($user);
    }
}
