<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('game')->latest();

        // Filter berdasarkan game
        if ($request->filled('game_id')) {
            $query->where('game_id', $request->game_id);
        }
    
        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif' ? 1 : 0);
        }
    
        $products = $query->paginate(10)->withQueryString();
        $games = Game::orderBy('name')->get();
    
        return view('admin.products.index', compact('products', 'games'));
    }

    public function create()
    {
        $games = Game::where('is_active', true)->orderBy('name')->get();
        return view('admin.products.create', compact('games'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'game_id' => 'required|exists:games,id',
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'api_product_code' => 'nullable|string|max:255',
        ]);

        Product::create($request->all());

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $games = Game::where('is_active', true)->orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'games'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'game_id' => 'required|exists:games,id',
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'api_product_code' => 'nullable|string|max:255',
        ]);

        $productData = $request->all();
        $productData['is_active'] = $request->has('is_active');

        $product->update($productData);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
