<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\PsConsoleController;

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Room routes
    Route::get('/', [RoomController::class, 'index']);
    Route::post('/rooms', [RoomController::class, 'store']);
    Route::put('/rooms/{room}', [RoomController::class, 'update']);
    Route::delete('/rooms/{room}', [RoomController::class, 'destroy']);

    // PS Console routes
    Route::post('/ps/{psConsole}/timer', [PsConsoleController::class, 'updateTimer']);
    Route::get('/ps/{psConsole}/timer', [PsConsoleController::class, 'getTimer']);
    Route::delete('/ps/{psConsole}', [PsConsoleController::class, 'destroy']);

    // Dashboard
    Route::get('/dashboard', [RoomController::class, 'index']);
});

// PS Console route (non-auth)
Route::post('/rooms/{room}/ps', [PsConsoleController::class, 'store']);
Route::post('/ps', [PsConsoleController::class, 'store']);

// Register routes
Route::get('/register', function () {
    return view('register');
});

Route::post('/register', function (Request $request) {
    $validated = $request->validate([
        'username' => 'required|unique:users,username|max:255',
        'email'    => 'required|email|unique:users,email|max:255',
        'password' => 'required|min:6',
    ]);

    User::create([
        'username' => $validated['username'],
        'email'    => $validated['email'],
        'password' => Hash::make($validated['password']),
    ]);

    return redirect('/login')->with('success', 'Registration successful! Please login.');
});

// Login routes
Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->only('username', 'password');

    if (auth()->attempt($credentials)) {
        return redirect('/dashboard');
    }

    return back()->withErrors(['login' => 'Invalid credentials.']);
});

// Logout
Route::post('/logout', function () {
    auth()->logout();
    return redirect('/login')->with('success', 'You have been logged out.');
})->name('logout');
