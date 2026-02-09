<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class TeachersController extends Controller
{
    public function index()
    {
        $users = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['Teacher', 'User']);
        })
            ->orderBy('created_at', 'desc') // latest registered first
            ->get();
        return view('teachers.index', compact('users'));
    }
    public function grantTeacherRole($id)
    {
        $user = User::findOrFail($id);
        $user->syncRoles('Teacher'); // gives all permissions of teacher
        return back()->with('success', $user->name . ' is now a teacher.');
    }

    public function revokeTeacherRole($id)
    {
        $user = User::findOrFail($id);
        $user->syncRoles('User'); // revert to normal user
        return back()->with('success', $user->name . ' is now a user.');
    }
    public function search_teacher(Request $request)
    {
        $search = $request->search;
        $teacher = User::where('created_by', auth()->user()->id)
            ->where(function ($teacher) use ($search) {
                $teacher->where('name', 'LIKE', "%{$search}%")
                    ->where('email', 'LIKE', "%{$search}$")
                    ->where('role', 'LIKE', "%{$search}$");
            })
            ->get();
        return response()->json($teacher);
    }
    public function teacher_edit(Request $request, $id)
    {
        // dd($request->all());
        $user = User::find($id);
        if ($user) {
            return view('teachers.teachers-edit', compact('user'));
        } else {
            return redirect()->back()->with('error', 'User Not found in record !');
        }
    }
    public function create(){
        return view('teachers.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed',Password::defaults()], 
        ]);
        // dd('yy');
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $role = Role::firstOrCreate(['name' => 'Teacher']);
        $user->assignRole('Teacher');
      
        return redirect()->route('teachers.index')->with('success','Teacher Registered Successfully..');
    }
}

