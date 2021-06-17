<?php

namespace App\Http\Controllers;

use App\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $products = Product::orderBy('id', 'ASC')
            ->paginate(15);
        return view('admin.products.index', compact('products'))
            ->with('i', ($request->get('page', 1) - 1) * 15);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("admin.products.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'=> 'required',
            'description'=> 'required',
            'category'=> 'required',
            'mass'=> 'required',
            'price'=> 'required',
            'portion_count'=> 'required',
            'image_url'=> 'required',
            'site_url'=> 'required',
            'is_active'=> 'required',

        ]);
        $product = Product::create([
            'title'=>$request->get('title')??'',
            'description'=> $request->get('description')??'',
            'category'=> $request->get('category')??'',
            'mass'=> $request->get('mass')??'',
            'price'=> $request->get('price')??'',
            'portion_count'=> $request->get('portion_count')??'',
            'image_url'=> $request->get('image_url')??'',
            'site_url'=> $request->get('site_url')??'',
            'is_active'=> $request->get('is_active')??'',

            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        return redirect()
            ->route('products.index')
            ->with('success', 'Товар успешно добавлен');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::find($id);
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::find($id);
        return view('admin.products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title'=> 'required',
            'description'=> 'required',
            'category'=> 'required',
            'mass'=> 'required',
            'price'=> 'required',
            'portion_count'=> 'required',
            'image_url'=> 'required',
            'is_active'=> 'required',
        ]);

        $product = Product::find($id);
        $product->title = $request->get("title");
        $product->description = $request->get("description");
        $product->category = $request->get("category");
        $product->mass = $request->get("mass");
        $product->price = $request->get('price');
        $product->portion_count = $request->get('portion_count');
        $product->image_url = $request->get('image_url');
        $product->site_url = $request->get('site_url')??'';
        $product->is_active = $request->get('is_active')??true;
        $product->save();

        return redirect()
            ->route('products.index')
            ->with('success', 'Товар успешно отредактирован');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product= Product::find($id);
        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Товар успешно удален');
    }
}
