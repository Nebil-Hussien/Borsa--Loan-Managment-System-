<?php

namespace Modules\Client\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Client\Entities\ClientCity;
use Yajra\DataTables\Facades\DataTables;

class ClientCityController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:client.clients.client_city.index'])->only(['index', 'show']);
        $this->middleware(['permission:client.clients.client_city.create'])->only(['create', 'store']);
        $this->middleware(['permission:client.clients.client_city.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:client.clients.client_city.destroy'])->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $perPage = $request->per_page ?: 20;
        $orderBy = $request->order_by;
        $orderByDir = $request->order_by_dir;
        $search = $request->s;
        $data = ClientCity::when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
            $query->orderBy($orderBy, $orderByDir);
        })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('name', 'like', "%$search%");
            })
            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('client::client_city.index', compact('data'));
    }
    public function get_client_city(Request $request)
    {
        $query = ClientCity::query();
        return DataTables::of($query)->editColumn('action', function ($data) {
            $action = '<div class="btn-group"><button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-navicon"></i></button> <ul class="dropdown-menu dropdown-menu-right" role="menu">';
            if (Auth::user()->hasPermissionTo('client.clients.client_city.edit')) {
                $action .= '<li><a href="' . url('client/client_city/' . $data->id . '/edit') . '" class="">' . trans_choice('core::general.edit', 2) . '</a></li>';
            }
            if (Auth::user()->hasPermissionTo('client.clients.client_city.destroy')) {
                $action .= '<li><a href="' . url('client/client_city/' . $data->id . '/destroy') . '" class="confirm">' . trans_choice('core::general.delete', 2) . '</a></li>';
            }
            $action .= "</ul></li></div>";
            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('client/client_city/' . $data->id . '/show') . '">' . $data->id . '</a>';
        })->rawColumns(['id', 'action'])->make(true);
    }
    public function create()
    {
        return
            theme_view('client::client_city.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required'],
        ]);
        $client_city = new ClientCity();
        $client_city->shortName = $request->shortName;
        $client_city->name = $request->name;
        $client_city->save();
        activity()->on($client_city)
            ->withProperties(['id' => $client_city->id])
            ->log('Create Client City');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('client/client_city');
    }
    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $client_city = ClientCity::find($id);
        return theme_view('client::client_city.show', compact('client_city'));
    }
    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $client_city = ClientCity::find($id);
        return theme_view('client::client_city.edit', compact('client_city'));
    }
    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'shortName' => ['required'],
            'name' => ['required']
        ]);
        $client_city = ClientType::find($id);
        $client_city->shortName = $request->shortName;
        $client_city->name = $request->name;
        $client_city->save();
        activity()->on($client_city)
            ->withProperties(['id' => $client_city->id])
            ->log('Update Client City');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('client/client_city');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $client_city = ClientCity::find($id);
        $client_city->delete();
        activity()->on($client_city)
            ->withProperties(['id' => $client_city->id])
            ->log('Delete Client City');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect()->back();
    }
}