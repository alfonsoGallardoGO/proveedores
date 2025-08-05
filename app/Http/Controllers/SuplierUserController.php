<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\SupplierUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage as FacadesStorage;

class SuplierUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $users = SupplierUser::withoutTrashed()->get();
        $suppliers = Supplier::withoutTrashed()->get();
        return Inertia::render('Users/Index', ['users' => $users, 'suppliers' => $suppliers]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function store(Request $request)
    {
        //dd($request->all());
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:supplier_users,email'],
            'password' => ['required', 'string', 'min:8'],
            'rfc' => ['nullable', 'string', 'max:255', 'unique:supplier_users,rfc'],
            'username' => ['required', 'string', 'max:255', 'unique:supplier_users,username'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'profile_photo_path' => ['nullable', 'image', 'max:2048'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
        ]);

        $validatedData['password'] = Hash::make($validatedData['password']);

        if ($request->hasFile('profile_photo_path')) {
            $file = $request->file('profile_photo_path');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('storage/profile-photos'), $filename);
            $validatedData['profile_photo_path'] = 'profile-photos/' . $filename;
        }

        SupplierUser::create($validatedData);
        $updatedUsers = SupplierUser::withoutTrashed()->get();
        return redirect()->route('users.index')->with([
            'users' => $updatedUsers,
            'message' => 'User created successfully!'
        ]);
        
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return void
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return void
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return void
     */
    public function update(Request $request, $id)
    {
        //dd($request->all());
        $user = SupplierUser::findOrFail($id);
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:supplier_users,email,' . $id],
            'password' => ['nullable', 'string', 'min:8'],
            'rfc' => ['nullable', 'string', 'max:255', 'unique:supplier_users,rfc,' . $id],
            'username' => ['required', 'string', 'max:255', 'unique:supplier_users,username,' . $id],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'profile_photo_path' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
        ]);

        if (!empty($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            unset($validatedData['password']);
        }

        if ($request->hasFile('profile_photo_path')) {
            if ($user->profile_photo_path) {
                FacadesStorage::disk('public')->delete($user->profile_photo_path);
            }
            $path = $request->file('profile_photo_path')->store('profile-photos', 'public');
            $validatedData['profile_photo_path'] = $path;
        } elseif ($request->input('profile_photo_path') === null && $user->profile_photo_path) {
            FacadesStorage::disk('public')->delete($user->profile_photo_path);
            $validatedData['profile_photo_path'] = null;
        } else {
            unset($validatedData['profile_photo_path']);
        }

        if (!empty($validatedData['password'])) {
            $validatedData['password'] = Hash::make($request->input('password'));
        } else {
            unset($validatedData['password']);
        }
        
        if ($request->hasFile('profile_photo_path')) {
            $file = $request->file('profile_photo_path');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('storage/profile-photos'), $filename);
            $validatedData['profile_photo_path'] = 'profile-photos/' . $filename;
        }

        $user->update($validatedData);
        $updatedUsers = SupplierUser::withoutTrashed()->get();
        return redirect()->route('users.index')->with([
            'users' => $updatedUsers,
            'message' => 'User updated successfully!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return void
     */
    public function destroy($id)
    {
        // Soft delete the user
        $user = SupplierUser::findOrFail($id);
        $user->delete();
        $updatedUsers = SupplierUser::withoutTrashed()->get();
        return redirect()->route('users.index')->with([
            'users' => $updatedUsers,
            'message' => 'User deleted successfully!'
        ]);
    }

    public function destroySelected(Request $request)
    {
        $userIds = $request->input('users', []);
       

        if (empty($userIds)) {
            return back()->with('error', 'No users selected for deletion.');
        }

        SupplierUser::whereIn('id', $userIds)->delete();

        return back()->with('message', 'Selected users deleted successfully!');
    }

}
