<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\StoreOperatorRequest;
use App\Http\Requests\UpdateOperatorRequest;

class OperatorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $operators = User::withCount(['rentalSessions', 'topups'])->latest()->paginate(10);

        return view('operators.index', compact('operators'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect()->route('operators.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOperatorRequest $request)
    {
        $validated = $request->validated();
        User::create($validated);

        return redirect()->route('operators.index')->with('success', 'Operator berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $operator)
    {
        $operators = User::withCount(['rentalSessions', 'topups'])->latest()->paginate(10);

        $editingOperator = $operator;
        return view('operators.index', compact('operators', 'editingOperator'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOperatorRequest $request, User $operator)
    {
        $data = $request->validated();

        if($operator->id === auth()->id() && $data['role'] !== $operator->role){
            return redirect()->route('operators.index')->with('error', 'Peran akun yang sedang Anda gunakan tidak dapat diubah.');
        }

        if (empty($data['password'])){
            unset($data['password']);
        }

        $operator->update($data);

        return redirect()->route('operators.index')->with('success', 'Data operator berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $operator)
    {
        if ($operator->id === auth()->id()){
            return redirect()->route('operators.index')->with('error', 'Akun yang sedang Anda gunakan tidak dapat dihapus.');
        }

        if ($operator->rentalSessions()->exists() || $operator->topups()->exists()){
            return redirect()->route('operators.index')->with('error', 'Akun tidak dapat dihapus karena sudah memiliki riwayat transaksi.');
        }

        $operator->delete();
        return redirect()->route('operators.index')->with('success', 'Operator berhasil dihapus.');
    }
}
