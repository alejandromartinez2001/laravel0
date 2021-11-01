<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();

        $title = 'Usuarios';

        return view('users.index')->with(compact('users', 'title'));

        /*return view('users.index')
            ->with('users', User::all())
            ->with('title', 'Listado de Usuarios');*/
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required' , 'regex:/^[\pL\s\-]+$/u', 'min:3', 'max:30'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x]).(?=.*[!$#%]).{8,}.*$/'],
        ], [
            'name.required' => 'El campo nombre es obligatorio',
            'name.regex' => 'El campo nombre no puede tener valores numéricos',
            'name.min' => 'El campo nombre debe tener como mínimo 3 carácteres',
            'name.max' => 'El campo nombre debe tener como máximo 30 carácteres',
            'email.required' => 'El campo email es obligatorio',
            'email.email' => 'El email introducido no tiene un formato válido',
            'email.unique' => 'Ese email ya existe en la BD',
            'password.required' => 'El campo contraseña es obligatorio',
            'password.regex' => 'La contraseña debe tener mínimo una mayúscula, una minúscula, un número y un dígito alfanumérico(!$#%])',
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password'])
        ]);

        return redirect()->route('users.index');
    }

    public function show(User $user)
    {
        if ($user == null) {
            return response()->view('errors.404', [], 404);
        }

        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(User $user)
    {
        $data = request()->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => '',
        ]);

        $data['password'] = bcrypt($data['password']);

        $user->update($data);

        return redirect()->route('users.show', $user);
    }
}
