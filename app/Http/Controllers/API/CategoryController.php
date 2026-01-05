<?php

namespace App\Http\Controllers\API;

use App\Models\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index() {
        return Category::all();
    }

    public function store(Request $r) {
        return Category::create($r->all());
    }
}
