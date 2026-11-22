<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ComputerType;
use App\Http\Requests\StoreComputerTypeRequest;
use App\Http\Requests\UpdateComputerTypeRequest;

class ComputerTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $computerTypes = ComputerType::withCount('computers')->latest()->paginate(10);

        return view('computer-types.index', compact('computerTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect()->route('computer-types.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreComputerTypeRequest $request)
    {
        $validated = $request->validated();

        ComputerType::create($validated);

        return redirect()->route('computer-types.index')->with('success', 'Jenis PC berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ComputerType $computerType)
    {
        $computerTypes = ComputerType::withCount('computers')->latest()->paginate(10);

        $editingType = $computerType;
        return view('computer-types.index', compact('computerTypes', 'editingType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateComputerTypeRequest $request, ComputerType $computerType)
    {
        $computerType->update($request->validated());

        return redirect()->route('computer-types.index')->with('success', 'Jenis PC berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ComputerType $computerType)
    {
        if ($computerType->computers()->exists()){
            return redirect()->route('computer-types.index')->with('error', "Jenis PC '{$computerType->nama}' tidak dapat dihapus karena masih digunakan oleh komputer.");
        }

        $computerType->delete();
        return redirect()->route('computer-types.index')->with('success', 'Jenis PC berhasil dihapus.');
    }
}
