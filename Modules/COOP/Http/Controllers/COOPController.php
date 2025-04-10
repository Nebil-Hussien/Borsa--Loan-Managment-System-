<?php

namespace Modules\COOP\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laracasts\Flash\Flash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Modules\COOP\Entities\Coop;
use Modules\COOP\Entities\CoopUser;
use Modules\CustomField\Entities\CustomField;
use Yajra\DataTables\Facades\DataTables;


class COOPController extends Controller
{
    /**
     * CoopController constructor.
     */
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:coop.coops.index'])->only(['index', 'show']);
        $this->middleware(['permission:coop.coops.create'])->only(['create', 'store']);
        $this->middleware(['permission:coop.coops.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:coop.coops.destroy'])->only(['destroy']);
        $this->middleware(['permission:coop.coops.assign_user'])->only(['assign_user']);

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
        $data = Coop::when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
            $query->orderBy($orderBy, $orderByDir);
        })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('name', 'like', "%$search%");
            })
            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('coop::coop.index', compact('data'));
        //return view('coop::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response 
     */
    public function get_coops(Request $request)
    {
        $query = Coop::query();
        return DataTables::of($query)->editColumn('action', function ($data) {
            $action = '<div class="btn-group"><button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-navicon"></i></button> <ul class="dropdown-menu dropdown-menu-right" role="menu">';
            if (Auth::user()->hasPermissionTo('coop.coops.edit')) {
                $action .= '<li><a href="' . url('coop/' . $data->id . '/show') . '" class="">' . trans_choice('core::general.detail', 2) . '</a></li>';
            }
            if (Auth::user()->hasPermissionTo('coop.coops.edit')) {
                $action .= '<li><a href="' . url('coop/' . $data->id . '/edit') . '" class="">' . trans_choice('core::general.edit', 2) . '</a></li>';
            }
            if (Auth::user()->hasPermissionTo('coop.coops.destroy')) {
                $action .= '<li><a href="' . url('coop/' . $data->id . '/destroy') . '" class="confirm">' . trans_choice('core::general.delete', 2) . '</a></li>';
            }
            $action .= "</ul></li></div>";
            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('coop/' . $data->id . '/show') . '">' . $data->id . '</a>';

        })->editColumn('name', function ($data) {
            return '<a href="' . url('coop/' . $data->id . '/show') . '">' . $data->name . '</a>';

        })->rawColumns(['id', 'name', 'action'])->make(true);
    }




    public function create()
    {
        $custom_fields = CustomField::where('category', 'add_coop')->where('active', 1)->get();
        return theme_view('coop::coop.create', compact('custom_fields'));
        //return view('coop::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required'],
            'active' => ['required'],
        ]);
        $coop = new Coop();
        $coop->name = $request->name;
        $coop->open_date = $request->open_date;
        $coop->active = $request->active;
        $coop->notes = $request->notes;
        $coop->save();
        custom_fields_save_form('add_coop', $request, $coop->id);
        activity()->on($coop)
            ->withProperties(['id' => $coop->id])
            ->log('Create Cooporative');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('coop');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $coop = Coop::with('users')->find($id);
        $custom_fields = CustomField::where('category', 'add_coop')->where('active', 1)->get();
        return theme_view('coop::coop.show', compact('coop', 'custom_fields'));
        //return view('coop::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Resopons
     */
    public function edit($id)
    {
         $coop = Coop::find($id);
        $custom_fields = CustomField::where('category', 'add_coop')->where('active', 1)->get();
        return theme_view('coop::coop.edit', compact('coop', 'custom_fields'));
        //return view('coop::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required'],
            'active' => ['required'],
        ]);
        $coop = Coop::find($id);
        $coop->name = $request->name;
        $coop->open_date = $request->open_date;
        $coop->active = $request->active;
        $coop->notes = $request->notes;
        $coop->save();
        custom_fields_save_form('add_coop', $request, $coop->id);
        activity()->on($coop)
            ->withProperties(['id' => $coop->id])
            ->log('Update Coop');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('coop');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
         $coop = Coop::find($id);
        if ($coop->is_system == 1) {
            \flash(trans_choice("core::general.cannot_delete_system_branch", 1))->error()->important();
            return redirect()->back();
        }
        $coop->delete();
        activity()->on($coop)
            ->withProperties(['id' => $coop->id])
            ->log('Delete Coop');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect()->back();
    }
    public function add_user(Request $request, $id)
    {
        if (CoopUser::where('user_id', $request->user_id)->where('coop_id', $id)->get()->count() > 0) {
            Flash::warning(trans_choice("branch::general.user_already_added_to_branch", 1));
            return redirect()->back();
        }
        $coop_user = new CoopUser();
        $coop_user->branch_id = $id;
        $coop_user->user_id = $request->user_id;
        $coop_user->created_by_id = Auth::id();
        $coop_user->save();
        activity()->on($coop_user)
            ->withProperties(['id' => $coop_user->id])
            ->log('Add Coop User');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect()->back();
    }

    public function remove_user($id)
    {
        CoopUser::destroy($id);
        activity()->log('Remove Coop User');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect()->back();
    }
}