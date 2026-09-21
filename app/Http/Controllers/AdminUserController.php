<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required',
            'email'         => 'required|email|unique:users',
            'university_id' => 'required|unique:users',
            'role'          => 'required|in:student,admin',
            'password'      => 'required|min:6',
        ]);

        User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'university_id' => $request->university_id,
            'role'          => $request->role,
            'department'    => $request->department,
            'password'      => Hash::make($request->password),
        ]);

        return redirect()->route('admin.users.index')->with('status', 'User created successfully.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'  => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'role'  => 'required|in:student,admin',
        ]);

        $data = [
            'name'          => $request->name,
            'email'         => $request->email,
            'university_id' => $request->university_id,
            'role'          => $request->role,
            'department'    => $request->department,
            'phone'         => $request->phone,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('status', 'User updated successfully.');
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return redirect()->route('admin.users.index')->with('status', 'User deleted.');
    }
}
