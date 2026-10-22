<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Support\ValidationMessages;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoomController extends Controller
{
    public function index()
    {
        return view('rooms.index', [
            'rooms' => Room::withCount('bookings')->orderBy('room_number')->paginate(10),
        ]);
    }

    public function create()
    {
        return view('rooms.create');
    }

    public function store(Request $request)
    {
        Room::create($this->validated($request));

        return redirect()->route('rooms.index')->with('success', 'Kamar berhasil ditambahkan.');
    }

    public function edit(Room $room)
    {
        return view('rooms.edit', ['room' => $room]);
    }

    public function update(Request $request, Room $room)
    {
        $room->update($this->validated($request, $room));

        return redirect()->route('rooms.index')->with('success', 'Data kamar diperbarui.');
    }

    public function destroy(Room $room)
    {
        if ($room->bookings()->exists()) {
            return back()->with('error', 'Kamar tidak bisa dihapus karena sudah memiliki data booking.');
        }

        $room->delete();

        return redirect()->route('rooms.index')->with('success', 'Kamar dihapus.');
    }

    private function validated(Request $request, ?Room $room = null): array
    {
        return $request->validate([
            'room_number'     => ['required', 'string', 'max:10', Rule::unique('rooms', 'room_number')->ignore($room?->id)],
            'type'            => ['required', Rule::in(Room::TYPES)],
            'capacity'        => ['required', 'integer', 'min:1', 'max:20'],
            'price_per_night' => ['required', 'integer', 'min:0'],
            'status'          => ['required', Rule::in(array_keys(Room::STATUSES))],
            'description'     => ['nullable', 'string', 'max:500'],
        ], ValidationMessages::all(), ValidationMessages::attributes());
    }
}
