<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Runner;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RunnerController extends Controller
{
    public function index()
    {
        $runners = Runner::with('zones')->latest()->get();
        return view('admin.runners.index', compact('runners'));
    }

    public function create()
    {
        $zones = Zone::all();
        return view('admin.runners.create', compact('zones'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'vehicle_no' => 'nullable|string|max:50',
            'zone_ids' => 'required|array|min:1',
            'zone_ids.*' => 'exists:zones,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'runner',
        ]);

        $runner = Runner::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'vehicle_no' => $validated['vehicle_no'] ?? null,
            'is_available' => true,
        ]);

        $runner->zones()->attach($validated['zone_ids']);

        return redirect()->route('admin.runners.index')->with('success', 'Runner account created.');
    }
}