<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Computer;
use App\Models\ComputerType;
use App\Http\Requests\StoreComputerRequest;
use App\Http\Requests\UpdateComputerRequest;

class ComputerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $computers = Computer::with('computerType')->withCount('rentalSessions')->latest()->paginate(10);

        $computerTypes = ComputerType::orderBy('nama')->get();

        return view('computers.index', compact('computers', 'computerTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect()->route('computers.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreComputerRequest $request)
    {
        $validated = $request->validated();

        Computer::create($validated);

         return redirect()->route('computers.index')->with('success', 'PC berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Computer $computer)
    {
        $computers = Computer::with('computerType')->withCount('rentalSessions')->latest()->paginate(10);

        $computerTypes = ComputerType::orderBy('nama')->get();

        $editingComputer = $computer;
         return view('computers.index', compact('computers', 'computerTypes', 'editingComputer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateComputerRequest $request, Computer $computer)
    {
        $data = $request->validated();
        if ($computer->status === 'dipakai'){
            $data['status'] = 'dipakai';
        } elseif (empty($data['status'])) { 
            $data['status'] = $computer->status; 
        }
        
        $computer->update($data);
        return redirect()->route('computers.index')->with('success', 'Data komputer berhasil diperbarui.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Computer $computer)
    {
        if ($computer->status === 'dipakai'){
            return redirect()->route('computers.index')->with('error', "Komputer '{$computer->nama_pc}' sedang dipakai dan tidak dapat dihapus.");
        }

        if ($computer->rentalSessions()->exists()){
            return redirect()->route('computers.index')->with('error', "Komputer '{$computer->nama_pc}' tidak dapat dihapus karena sudah memiliki riwayat transaksi.");
        }

        $computer->delete();
        return redirect()->route('computers.index')->with('success', 'Data komputer berhasil dihapus.');
    }
}
