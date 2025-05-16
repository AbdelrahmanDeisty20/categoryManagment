<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category as RequestsCategory;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        //here for search
        $search = $request->get('search');
        //bring al categories from database
        $allCategories = Category::all();
        $filtered = $search
            ? Category::where('name', 'like', "%$search%")->get()
            : Category::orderBy('id', 'desc')->get();

        $perPage = 5;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $paginated = $filtered->slice($offset, $perPage);

        return view('index', [
            'categories' => $paginated,
            'allCategories' => $allCategories,
            'current_page' => $currentPage,
            'search' => $search,
            'total_pages' => ceil($filtered->count() / $perPage)
        ]);
    }
    public function store(CategoryRequest $request)
    {
        //here save new category
        Category::create(
           $request->validated()
        );

        return response()->json(['success' => 'created succesfully']);
    }


    public function update(CategoryRequest $request)
    {
        //here find item category
        $category = Category::find($request->id);
        //here update category
        $category->update(
            $request->validated()
        );

        return response()->json(['success' => true]);
    }

    // CRUD functions (store, update, delete) هنا تضعهم كما في كودك السابق


    public function destroy(Request $request)
    {
        //here for button delete has name:ids
        if ($request->has('ids')) {
            // حذف جماعي
            Category::whereIn('id', $request->ids)->delete();
        } elseif ($request->has('id')) {
            // حذف فردي
            Category::where('id', $request->id)->delete();
        }

        return response()->json(['success' => true]);
    }
}
