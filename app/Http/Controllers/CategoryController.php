<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Category::with('products')->get();
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return $category->load('products');
    }
}
