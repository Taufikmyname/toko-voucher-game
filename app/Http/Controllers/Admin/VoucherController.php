<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Voucher;
use Illuminate\Http\Request;
use App\Models\Game;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $query = Voucher::with('product.game')->latest();

        // Terapkan filter status
        if ($request->filled('status')) {
            if ($request->status == 'tersedia') {
                $query->where('is_used', false);
            } elseif ($request->status == 'terpakai') {
                $query->where('is_used', true);
            }
        }

        // Terapkan filter game
        if ($request->filled('game_id')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('game_id', $request->game_id);
            });
        }

        $vouchers = $query->paginate(20)->withQueryString();
        $games = Game::orderBy('name')->get();

        return view('admin.vouchers.index', compact('vouchers', 'games'));
    }

    public function create()
    {
        $products = Product::with('game')->get();
        return view('admin.vouchers.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'codes' => 'required|string',
        ]);

        $codes = preg_split('/[\s,]+/', $request->codes, -1, PREG_SPLIT_NO_EMPTY);
        $count = 0;
        foreach ($codes as $code) {
            // Cek duplikasi sebelum membuat
            if (!Voucher::where('code', $code)->exists()) {
                Voucher::create([
                    'product_id' => $request->product_id,
                    'code' => $code,
                ]);
                $count++;
            }
        }

        return redirect()->route('admin.vouchers.index')->with('success', "$count kode voucher berhasil ditambahkan.");
    }

    public function destroy(Voucher $voucher)
    {
        $voucher->delete();
        return redirect()->route('admin.vouchers.index')->with('success', 'Kode voucher berhasil dihapus.');
    }
}