<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\PsConsoleController;

Route::get('/', [RoomController::class, 'index'])->middleware('auth');
Route::post('/rooms', [RoomController::class, 'store'])->middleware('auth');
Route::put('/rooms/{room}', [RoomController::class, 'update'])->middleware('auth');
Route::post('/rooms/{room}/ps', [PsConsoleController::class, 'store']);
Route::post('/ps', [PsConsoleController::class, 'store']);
Route::post('/ps/{psConsole}/timer', [PsConsoleController::class, 'updateTimer'])->middleware('auth');
Route::get('/ps/{psConsole}/timer', [PsConsoleController::class, 'getTimer'])->middleware('auth');
Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->middleware('auth');
Route::delete('/ps/{psConsole}', [PsConsoleController::class, 'destroy'])->middleware('auth');

Route::get('/register', function () {
    return view('register');
});

Route::post('/register', function (Request $request) {
    $validated = $request->validate([
        'username' => 'required|unique:users,username|max:255',
        'email' => 'required|email|unique:users,email|max:255',
        'password' => 'required|min:6',
    ]);

    User::create([
        'username' => $validated['username'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
    ]);

    return redirect('/login')->with('success', 'Registration successful! Please login.');
});

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->only('username', 'password');

    if (auth()->attempt($credentials)) {
        return redirect('/dashboard'); // Replace with your dashboard route
    }

    return back()->withErrors(['login' => 'Invalid credentials.']);
});

Route::get('/dashboard', [RoomController::class, 'index'])->middleware('auth');

Route::post('/logout', function () {
    auth()->logout();
    return redirect('/login')->with('success', 'You have been logged out.');
})->name('logout');
