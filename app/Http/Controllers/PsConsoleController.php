<?php
namespace App\Http\Controllers;

use App\Models\PsConsole;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PsConsoleController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'name' => 'required',
            'status' => 'required|in:active,damaged',
        ]);

        PsConsole::create($request->all());

        return redirect()->back()->with('success', 'PS Console added successfully!');
    }

    public function destroy(Request $request, PsConsole $psConsole)
    {
        $request->validate([
            'password' => 'required', // Ensure password is provided
        ]);

        // Check if the provided password matches the authenticated user's password
        if (!Hash::check($request->password, $request->user()->password)) {
            return response()->json(['success' => false, 'message' => 'Invalid password.'], 403);
        }

        try {
            $psConsole->update(['timer' => null]); // Reset the timer
            return response()->json(['success' => true, 'message' => 'Timer deleted successfully!']);
        } catch (\Exception $e) {
            \Log::error('Failed to delete timer: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete timer.'], 500);
        }
    }

    public function updateTimer(Request $request, PsConsole $psConsole)
    {
        $request->validate([
            'timer' => 'nullable|integer|min:0', // Allow null to reset the timer
        ]);

        try {
            $psConsole->update(['timer' => $request->timer]); // Update or reset the timer field
            return response()->json(['success' => true, 'message' => 'Timer updated successfully!']);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Failed to update timer: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update timer.'], 500);
        }
    }

    public function getTimer(PsConsole $psConsole)
    {
        return response()->json(['timer' => $psConsole->timer]);
    }
}
