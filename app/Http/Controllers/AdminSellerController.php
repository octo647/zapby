<?php

namespace App\Http\Controllers;

use App\Models\Seller;
use Illuminate\Http\Request;

class AdminSellerController extends Controller
{
    public function index()
    {
        $sellers = Seller::withCount('professionals')->orderBy('name')->get();

        return view('admin.sellers', compact('sellers'));
    }

    public function store(Request $request)
    {
        $request->merge(['code' => Seller::normalizeCode((string) $request->code)]);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:30|regex:/^[A-Z0-9_-]+$/|unique:sellers,code',
            'whatsapp' => 'nullable|string|max:20',
        ]);

        Seller::create($data);

        return back()->with('success', 'Vendedor cadastrado.');
    }

    public function toggle(Seller $seller)
    {
        $seller->update(['active' => !$seller->active]);

        return back()->with('success', 'Status do vendedor atualizado.');
    }
}
