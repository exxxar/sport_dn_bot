<?php

namespace App\Http\Controllers;

use App\Enums\UseIngredientType;
use App\Ingredient;
use Carbon\Carbon;
use Illuminate\Http\Request;

class IngredientController extends Controller
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
        $ingredients = Ingredient::orderBy('id', 'DESC')
            ->paginate(15);
        return view('admin.ingredients.index', compact('ingredients'))
            ->with('i', ($request->get('page', 1) - 1) * 15);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("admin.ingredients.create");
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
            'mass'=> 'required',
            'quantity'=> 'required',
            'price'=> 'required',
            'use_type'=> 'required'

        ]);
        $ingredient = Ingredient::create([
            'title'=>$request->get('title')??'',
            'mass'=> $request->get('mass')??'',
            'quantity'=> $request->get('quantity')??'',
            'price'=> $request->get('price')??'',
            'use_type'=>UseIngredientType::getInstance(intval($request->get('use_type')))->value ??0,

            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        return redirect()
            ->route('ingredients.index')
            ->with('success', 'Ингридиент успешно добавлен');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Ingredient  $ingredient
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $ingredient = Ingredient::find($id);
        return view('admin.ingredients.show', compact('ingredient'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Ingredient  $ingredient
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $ingredient = Ingredient::find($id);
        return view('admin.ingredients.edit', compact('ingredient'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Ingredient  $ingredient
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title'=> 'required',
            'mass'=> 'required',
            'quantity'=> 'required',
            'price'=> 'required',
            'use_type'=> 'required'
        ]);
        $ingredient = Ingredient::find($id);
        $ingredient->title = $request->get("title");
        $ingredient->mass = $request->get("mass");
        $ingredient->quantity = $request->get("quantity");
        $ingredient->price = $request->get("price");
        $ingredient->use_type = UseIngredientType::getInstance(intval($request->get('use_type')))->value;
        $ingredient->save();

        return redirect()
            ->route('ingredients.index')
            ->with('success', 'Ингридиент успешно отредактирован');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Ingredient  $ingredient
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $ingredient = Ingredient::find($id);
        $ingredient->delete();

        return redirect()
            ->route('ingredients.index')
            ->with('success', 'Ингридиент успешно удален');
    }
}
