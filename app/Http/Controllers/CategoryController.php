<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\CategoryRequest;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $categories = Category::withCount('items');

            return DataTables::of($categories)
                ->addColumn('action', function ($category) {
                    $actions = '<div class="btn-group">';
                    if (auth()->user()->can('categories.edit')) {
                        $actions .= '<a href="'.route('categories.edit', $category->id).'" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>';
                    }
                    if (auth()->user()->can('categories.delete')) {
                        $actions .= '<button class="btn btn-sm btn-danger delete-btn" data-id="'.$category->id.'"><i class="fas fa-trash"></i></button>';
                    }
                    $actions .= '</div>';
                    return $actions;
                })
                ->addColumn('status', function ($category) {
                    return $category->is_active 
                        ? '<span class="badge badge-success">Active</span>' 
                        : '<span class="badge badge-danger">Inactive</span>';
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('categories.index');
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(CategoryRequest $request)
    {
        try {
            DB::beginTransaction();
            
            Category::create($request->validated() + [
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('categories.index')
                ->with('success', 'Category created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error creating category: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(CategoryRequest $request, Category $category)
    {
        try {
            DB::beginTransaction();

            $category->update($request->validated() + [
                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('categories.index')
                ->with('success', 'Category updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error updating category: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Category $category)
    {
        try {
            DB::beginTransaction();

            if ($category->items()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete category with associated items.'
                ], 400);
            }

            $category->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting category: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCategories()
    {
        $categories = Category::active()->get(['id', 'name']);
        return response()->json($categories);
    }
}