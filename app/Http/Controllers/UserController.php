<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class UserController extends Controller
{
    protected $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function index(Request $request)
    {
        $paging = $request->perPage;
        $users = User::select('id', 'name', 'email', 'phone', 'address')->latest()->paginate($paging);
        return response()->json([
            'data' => $users->items(), 
            'currentPage' => $users->currentPage(),
            'totalPages' => $users->lastPage(),
        ]); 
    }

    public function store(Request $request)
    {
        $this->doValidate($request);
        $data = $request->all();
        $user = $this->userService->store(new User(), $data);
        if ($user) {
            return response()->json([
                'message' => 'User created successfully',
                'product' => $user,
            ], 201);
        } else {
            return response()->json([
                'message' => 'User created successfully',
                'product' => $user,
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }

    protected function doValidate(Request $request)
    {
        return $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
    }

    
}
