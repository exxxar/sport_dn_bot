<?php

namespace App\Http\Controllers;

use App\Prize;
use App\Promocode;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UsersController extends Controller
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
        $users = User::orderBy('id', 'DESC')
            ->paginate(15);
        return view('admin.users.index', compact('users'))
            ->with('i', ($request->get('page', 1) - 1) * 15);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view("admin.users.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'phone' => 'required',
            'is_admin' => 'required',
        ]);

        $user = User::create([
            'name' => $request->get('name') ?? '',
            'email' => $request->get('email') ?? '',
            'password' => bcrypt($request->get('password')) ?? '',
            'phone' => $request->get('phone') ?? '',
            'birthday' => $request->get('birthday') ?? '',
            'is_admin' => $request->get('is_admin') ?? false,

            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        return redirect()
            ->route('promocodes.index')
            ->with('success', 'Промокод успешно добавлен');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Promocode $promocodes
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Promocode $promocodes
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        return view('admin.users.edit', compact('user'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Promocode $promocodes
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'is_admin' => 'required',
        ]);

        $user = User::find($id);
        $user->is_admin = $request->get("is_admin");
        $user->save();

        return redirect()
            ->route('users.index')
            ->with('success', 'Пользователь успешно отредактирован');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Promocode $promocodes
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'Пользователь успешно удален');
    }

}
