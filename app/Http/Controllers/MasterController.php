<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\IncomeSource;
use App\Models\TourismPlace;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MasterController extends Controller
{
    public function index(): View
    {
        return view('masters.index', [
            'categories' => Category::orderBy('type')->orderBy('name')->get(),
            'places' => TourismPlace::orderBy('name')->get(),
            'sources' => IncomeSource::orderBy('name')->get(),
        ]);
    }

    public function storePlace(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'description' => ['nullable', 'string', 'max:255']]);
        TourismPlace::create($data + ['is_active' => true]);
        return back()->with('success', 'Tempat wisata berhasil ditambahkan.');
    }

    public function updatePlace(Request $request, TourismPlace $place): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'description' => ['nullable', 'string', 'max:255'], 'is_active' => ['nullable', 'boolean']]);
        $place->update($data + ['is_active' => $request->boolean('is_active')]);
        return back()->with('success', 'Tempat wisata berhasil diperbarui.');
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'type' => ['required', 'in:income,expense,both']]);
        Category::create($data + ['is_active' => true]);
        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function updateCategory(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'type' => ['required', 'in:income,expense,both'], 'is_active' => ['nullable', 'boolean']]);
        $category->update($data + ['is_active' => $request->boolean('is_active')]);
        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function storeSource(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:120']]);
        IncomeSource::create($data + ['is_active' => true]);
        return back()->with('success', 'Sumber pendapatan berhasil ditambahkan.');
    }

    public function updateSource(Request $request, IncomeSource $source): RedirectResponse
{
    $data = $request->validate([
        'name' => ['required', 'string', 'max:120'],
        'is_active' => ['nullable', 'boolean']
    ]);

    $source->update([
        'name' => $data['name'],
        'is_active' => $request->boolean('is_active')
    ]);

    return back()->with('success', 'Sumber pendapatan berhasil diperbarui.');
}

    public function destroyPlace(TourismPlace $place): RedirectResponse
    {
        if ($place->transactions()->exists()) {
            return back()->with('error', 'Tempat wisata tidak bisa dihapus karena sudah memiliki riwayat transaksi! Silakan nonaktifkan.');
        }

        $place->delete();
        return back()->with('success', 'Tempat wisata berhasil dihapus.');
    }

    public function destroyCategory(Category $category): RedirectResponse
    {
        if ($category->transactions()->exists()) {
            return back()->with('error', 'Kategori tidak bisa dihapus karena sudah memiliki riwayat transaksi! Silakan nonaktifkan.');
        }

        $category->delete();
        return back()->with('success', 'Kategori berhasil dihapus.');
    }

    public function destroySource(IncomeSource $source): RedirectResponse
    {
        if ($source->transactions()->exists()) {
            return back()->with('error', 'Sumber pendapatan tidak bisa dihapus karena sudah memiliki riwayat transaksi! Silakan nonaktifkan.');
        }

        $source->delete();
        return back()->with('success', 'Sumber pendapatan berhasil dihapus.');
    }
}
