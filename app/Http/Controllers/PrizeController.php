<?php

namespace App\Http\Controllers;

use App\Prize;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PrizeController extends Controller
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
        $prizes = Prize::orderBy('position', 'ASC')
            ->paginate(20);
        return view('admin.prizes.index', compact('prizes'))
            ->with('i', ($request->get('page', 1) - 1) * 20);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("admin.prizes.create");
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
            'image_url'=> 'required',
            'position'=> 'required',
            'type'=> 'required',
        ]);
        $prize = Prize::create([
            'title'=>$request->get('title')??'',
            'description'=> $request->get('description')??'',
            'image_url'=> $request->get('image_url')??'',
            'position'=> $request->get('position')??'',
            'as_default'=> 0,
            'type'=> $request->get("type")??0,
            'virtual_amount'=> $request->get("virtual_amount")??0,

            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        return redirect()
            ->route('prizes.index')
            ->with('success', 'Приз успешно добавлен');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Prize  $prize
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $prize = Prize::find($id);
        return view('admin.prizes.show', compact('prize'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Prize  $prize
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $prize = Prize::find($id);
        return view('admin.prizes.edit', compact('prize'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Prize  $prize
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title'=> 'required',
            'description'=> 'required',
            'image_url'=> 'required',
            'position'=> 'required',
        ]);

        $prize = Prize::find($id);
        $prize->title = $request->get("title");
        $prize->description = $request->get("description");
        $prize->image_url = $request->get("image_url");
        $prize->position = $request->get("position");
        $prize->as_default = 0;
        $prize->type =  $request->get("type")??0;
        $prize->virtual_amount = $request->get("virtual_amount")??0;

        $prize->save();

        return redirect()
            ->route('prizes.index')
            ->with('success', 'Приз успешно отредактирован');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Prize  $prize
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $prize = Prize::find($id);
        $prize->delete();

        return redirect()
            ->route('prizes.index')
            ->with('success', 'Приз успешно удален');
    }
}
