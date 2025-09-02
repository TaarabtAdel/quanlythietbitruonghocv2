<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeviceType;

class DeviceTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = DeviceType::paginate(20);
        return view('admin.device_types.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.device_types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        DeviceType::create($request->all());

        return redirect()->route('device-types.index')
                         ->with('success', 'Device Type created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = DeviceType::find($id);
        return view('admin.device_types.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $item = DeviceType::find($id);
        return view('admin.device_types.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $item = DeviceType::find($id);
        $item->update($request->all());

        return redirect()->route('device-types.index')
                         ->with('success', 'Device Type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = DeviceType::find($id);
        $item->delete();

        return redirect()->route('device-types.index')
                         ->with('success', 'Device Type deleted successfully.');
    }
}
