<?php
namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RoomController extends Controller
{
    public function index()
    {
        // Fetch only rooms belonging to the authenticated user
        $rooms = auth()->user()->rooms()->with('psConsoles')->get();

        return view('dashboard', compact('rooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Simpan room baru untuk pengguna yang sedang login
        auth()->user()->rooms()->create([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', 'Room created successfully!');
    }

    public function update(Request $request, $room)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $room = Room::findOrFail($room);
        $room->update(['name' => $validated['name']]);

        return redirect()->back()->with('success', 'Room updated successfully!');
    }

    public function destroy(Request $request, Room $room)
    {
        $request->validate([
            'password' => 'required',
        ]);

        if (!Hash::check($request->password, auth()->user()->password)) {
            return back()->withErrors(['password' => 'Password is incorrect.']);
        }

        $room->delete();

        return redirect()->back()->with('success', 'Room deleted successfully.');
    }
}
