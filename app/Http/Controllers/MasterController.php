<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CategoryField;
use App\Models\IncomeSource;
use App\Models\TourismPlace;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MasterController extends Controller
{
    public function index(): View
    {
        $defaultFields = [
            'date' => [
                'name' => __('messages.field_date'),
                'type' => 'Date',
            ],

            'quantity' => [
                'name' => __('messages.field_quantity'),
                'type' => 'Number',
            ],

            'unit_price' => [
                'name' => __('messages.field_unit_price'),
                'type' => 'Number',
            ],

            'amount' => [
                'name' => __('messages.field_amount'),
                'type' => 'Number',
            ],

            'tourism_place_id' => [
                'name' => __('messages.field_tourism_place'),
                'type' => 'Select',
            ],

            'income_source_id' => [
                'name' => __('messages.field_income_source'),
                'type' => 'Select',
            ],

            'payment_method' => [
                'name' => __('messages.field_payment_method'),
                'type' => 'Select',
            ],

            'package_name' => [
                'name' => __('messages.field_package_name'),
                'type' => 'Text',
            ],

            'description' => [
                'name' => __('messages.field_description'),
                'type' => 'Textarea',
            ],

            'proof' => [
                'name' => __('messages.field_proof'),
                'type' => 'File',
            ],

            'status' => [
                'name' => __('messages.field_status'),
                'type' => 'Select',
            ],
        ];

        return view('masters.index', [
            'categories' => Category::with('fields')
                ->orderBy('type')
                ->orderBy('name')
                ->get(),

            'places' => TourismPlace::orderBy('name')->get(),

            'sources' => IncomeSource::orderBy('name')->get(),

            'defaultFields' => $defaultFields,
        ]);
    }

    public function storePlace(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:120'], 
            'description' => ['nullable', 'string', 'max:255']
        ]);
        
        TourismPlace::create($data + ['is_active' => true]);
        
        return back()->with('success', __('alerts.place_created'));
    }

    public function updatePlace(Request $request, TourismPlace $place): RedirectResponse
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:120'], 
            'description' => ['nullable', 'string', 'max:255'], 
            'is_active'   => ['nullable', 'boolean']
        ]);
        
        $place->update($data + ['is_active' => $request->boolean('is_active')]);
        
        return back()->with('success', __('alerts.place_updated'));
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'            => ['required', 'string', 'max:120'], 
            'type'            => ['required', 'in:income,expense,both'],
            'required_fields' => ['nullable', 'array'],
        ]);

        $defaultFields = ['date', 'quantity', 'unit_price', 'payment_method', 'description'];
        $data['required_fields'] = $request->input('required_fields', $defaultFields);
        
        Category::create($data + ['is_active' => true]);
        
        return back()->with('success', __('alerts.category_created'));
    }

    public function updateCategory(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'type' => ['required', 'in:income,expense,both'],
            'is_active' => ['nullable', 'boolean'],

            'required_fields' => ['nullable', 'array'],
            'required_fields.*' => ['string'],

            'fields' => ['nullable', 'array'],
            'fields.*.id' => ['nullable', 'integer'],
            'fields.*.field_label' => ['required', 'string', 'max:255'],
            'fields.*.field_type' => [
                'required_with:fields',
                'string',
                'in:text,number,date,textarea,select,file'
            ],
            'fields.*.options' => ['nullable', 'string', 'max:1000'],
            'fields.*.is_required' => ['nullable', 'boolean'],
        ]);

        $category->update([
            'name' => $data['name'],
            'type' => $data['type'],
            'is_active' => $request->boolean('is_active'),
            'required_fields' => $request->input('required_fields', []),
        ]);

        $submittedFieldIds = [];

        foreach ($request->input('fields', []) as $index => $fieldData) {

            if (empty($fieldData['field_label'])) {
                continue;
            }

            $fieldName = Str::slug(
                $fieldData['field_label'],
                '_'
            );

            $options = null;

            if (
                ($fieldData['field_type'] ?? null) === 'select'
                && !empty($fieldData['options'])
            ) {
                $options = array_values(
                    array_filter(
                        array_map(
                            'trim',
                            explode(',', $fieldData['options'])
                        )
                    )
                );
            }

            if (!empty($fieldData['id'])) {

                $field = $category->fields()
                    ->where('id', $fieldData['id'])
                    ->first();

                if ($field) {
                    $field->update([
                        'field_label' => $fieldData['field_label'],
                        'field_name' => $fieldName,
                        'field_type' => $fieldData['field_type'],
                        'options' => $options,
                        'is_required' => !empty($fieldData['is_required']),
                        'order' => $index,
                    ]);

                    $submittedFieldIds[] = $field->id;
                }

            } else {

                $field = $category->fields()->create([
                    'field_label' => $fieldData['field_label'],
                    'field_name' => $fieldName,
                    'field_type' => $fieldData['field_type'],
                    'options' => $options,
                    'is_required' => !empty($fieldData['is_required']),
                    'order' => $index,
                ]);

                $submittedFieldIds[] = $field->id;
            }
        }

        $category->fields()
            ->whereNotIn('id', $submittedFieldIds)
            ->delete();

        return back()->with(
            'success',
            __('alerts.category_updated')
        );
    }

    public function storeSource(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:120']]);
        
        IncomeSource::create($data + ['is_active' => true]);
        
        return back()->with('success', __('alerts.source_created'));
    }

    public function updateSource(Request $request, IncomeSource $source): RedirectResponse
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:120'],
            'is_active' => ['nullable', 'boolean']
        ]);

        $source->update([
            'name'      => $data['name'],
            'is_active' => $request->boolean('is_active')
        ]);

        return back()->with('success', __('alerts.source_updated'));
    }

    public function destroyPlace(TourismPlace $place): RedirectResponse
    {
        if ($place->transactions()->exists()) {
            return back()->with('error', __('alerts.place_has_transactions'));
        }

        $place->delete();
        return back()->with('success', __('alerts.place_deleted'));
    }

    public function destroyCategory(Category $category): RedirectResponse
    {
        if ($category->transactions()->exists()) {
            return back()->with('error', __('alerts.category_has_transactions'));
        }

        $category->delete();
        return back()->with('success', __('alerts.category_deleted'));
    }

    public function destroySource(IncomeSource $source): RedirectResponse
    {
        if ($source->transactions()->exists()) {
            return back()->with('error', __('alerts.source_has_transactions'));
        }

        $source->delete();
        return back()->with('success', __('alerts.source_deleted'));
    }
}