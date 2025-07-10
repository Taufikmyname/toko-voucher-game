<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GameController extends Controller
{
    public function index()
    {
        $games = Game::latest()->paginate(10);
        return view('admin.games.index', compact('games'));
    }

    public function create()
    {
        return view('admin.games.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:games,name',
            'category' => 'required|string|max:255',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $path = $request->file('thumbnail')->store('game_thumbnails', 'public');

        Game::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'category' => $request->category,
            'thumbnail' => $path,
        ]);

        return redirect()->route('admin.games.index')->with('success', 'Game berhasil ditambahkan.');
    }

    public function edit(Game $game)
    {
        return view('admin.games.edit', compact('game'));
    }

    public function update(Request $request, Game $game)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:games,name,' . $game->id,
            'category' => 'required|string|max:255',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $path = $game->thumbnail;
        if ($request->hasFile('thumbnail')) {
            if (Storage::disk('public')->exists($game->thumbnail)) {
                Storage::disk('public')->delete($game->thumbnail);
            }
            $path = $request->file('thumbnail')->store('game_thumbnails', 'public');
        }

        $game->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'category' => $request->category,
            'thumbnail' => $path,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.games.index')->with('success', 'Game berhasil diperbarui.');
    }

    public function destroy(Game $game)
    {
        if (Storage::disk('public')->exists($game->thumbnail)) {
            Storage::disk('public')->delete($game->thumbnail);
        }
        $game->delete();
        return redirect()->route('admin.games.index')->with('success', 'Game berhasil dihapus.');
    }
}