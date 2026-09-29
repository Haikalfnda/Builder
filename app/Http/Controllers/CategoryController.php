<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CategoryField;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('fields')->get();
        return view('masters.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense,both',
            'fields' => 'nullable|array',
            'fields.*.label' => 'required|string',
            'fields.*.type' => 'required|string',
        ]);

        DB::transaction(function () use ($request) {
            $category = Category::create([
                'name' => $request->name,
                'type' => $request->type,
                'is_active' => $request->has('is_active'),
            ]);

            if ($request->has('fields')) {
                foreach ($request->fields as $index => $fieldData) {
                    $options = !empty($fieldData['options']) 
                        ? array_map('trim', explode(',', $fieldData['options'])) 
                        : null;

                    CategoryField::create([
                        'category_id' => $category->id,
                        'field_label' => $fieldData['label'],
                        'field_name'  => Str::slug($fieldData['label'], '_'),
                        'field_type'  => $fieldData['type'],
                        'options'     => $options,
                        'is_required' => isset($fieldData['is_required']),
                        'order'       => $index,
                    ]);
                }
            }
        });

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense,both',
            'fields' => 'nullable|array',
        ]);

        DB::transaction(function () use ($request, $category) {
            $category->update([
                'name' => $request->name,
                'type' => $request->type,
                'is_active' => $request->has('is_active'),
            ]);

            $category->fields()->delete();

            if ($request->has('fields')) {
                foreach ($request->fields as $index => $fieldData) {
                    $options = !empty($fieldData['options']) 
                        ? array_map('trim', explode(',', $fieldData['options'])) 
                        : null;

                    CategoryField::create([
                        'category_id' => $category->id,
                        'field_label' => $fieldData['label'],
                        'field_name'  => Str::slug($fieldData['label'], '_'),
                        'field_type'  => $fieldData['type'],
                        'options'     => $options,
                        'is_required' => isset($fieldData['is_required']),
                        'order'       => $index,
                    ]);
                }
            }
        });

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui!');
    }

    public function destroy(Category $category)
    {
        if ($category->transactions()->exists()) {
            return back()->with('error', 'Kategori gagal dihapus karena sudah memiliki data transaksi!');
        }

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus!');
    }

    public function getFields(Category $category)
    {
        return response()->json($category->fields()->orderBy('order', 'asc')->get());
    }
}