<?php

namespace Modules\Core\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laracasts\Flash\Flash;
use Modules\Core\Entities\Currency;
use Modules\User\Entities\PaymentType;
use Modules\Branch\Entities\Branch;
use Yajra\DataTables\Facades\DataTables;

class NgoAssignController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:core.NGOAssign.index'])->only(['index', 'show']);
        $this->middleware(['permission:core.NGOAssign.create'])->only(['create', 'store']);
        $this->middleware(['permission:core.NGOAssign.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:core.NGOAssign.destroy'])->only(['destroy']);
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
        $user_id = $request->user_id;
        $branch_id = $request->branch_id;
        $data = NGOAssign::leftjoin("users", "users.id", "ngo_assign.user_id")->leftjoin("branches", "branches.id", "ngo_assign.branch_id")
            ->when($branch_id, function ($query) use ($branch_id) {
                $query->where("ngo_assign.branch_id", $branch_id);
            })
            ->when($user_id, function ($query) use ($user_id) {
                $query->where("ngo_assign.user_id", $user_id);
            })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('branches.name', 'like', "%$search%");
                $query->orWhere('user.first_name', 'like', "%$search%");
                $query->orWhere('user.middle_name', 'like', "%$search%");
            })
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })->selectRaw("concat(user.first_name,' ',user.middle_name) user, branches.name branch")
            ->groupBy("ngo_assign.branch_id")
            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('core::NGOAssign.index', compact('data'));
    }
    public function get_userNGO_assign(Request $request)
    {
        $query = NGOAssign::query();
        return DataTables::of($query)->editColumn('action', function ($data) {
            $action = '<div class="btn-group"><button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-navicon"></i></button> <ul class="dropdown-menu dropdown-menu-right" role="menu">';
            if (Auth::user()->hasPermissionTo('core.NGOAssign.edit')) {
                $action .= '<li><a href="' . url('core.NGOAssign/' . $data->id . '/edit') . '" class="">' . trans_choice('core::general.edit', 2) . '</a></li>';
            }
            if (Auth::user()->hasPermissionTo('core.NGOAssign.edit.destroy')) {
                $action .= '<li><a href="' . url('NGOAssign/' . $data->id . '/destroy') . '" class="confirm">' . trans_choice('core::general.delete', 2) . '</a></li>';
            }
            $action .= "</ul></li></div>";
            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('NGOAssign/' . $data->id . '/show') . '">' . $data->id . '</a>';
        })->rawColumns(['id', 'action'])->make(true);
    }
    public function create()
    {
        $branches = Branch::all();
        $users = User::whereHas('roles', function ($query) {
            return $query->where('name', '!=', 'client');
        })->get();
        JavaScript::put([
            'branches' => $branches,
            'users' => $users
        ]);
        return
            theme_view('core::userNGOAssign.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => ['required'],
            'branch_id' => ['required'],
        ]);
        $userNGO = new NGOAssign();
        $userNGO->user_id = $request->user_id;
        $userNGO->branch_id = $request->branch_id;
        $userNGO->save();
        activity()->on($userNGO)
            ->withProperties(['id' => $userNGO->id])
            ->log('Assign User to NGO');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('userNGOAssign');
    }
    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $userNGO = NGOAssign::find($id);
        return theme_view('client::client_city.show', compact('userNGO'));
    }
    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $userNGO = NGOAssign::find($id);
        return theme_view('core::userNGOAssign.edit', compact('userNGO'));
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
            'user_id' => ['required'],
            'branch_id' => ['required']
        ]);
        $userNGO = new NGOAssign();
        $userNGO->user_id = $request->user_id;
        $userNGO->branch_id = $request->branch_id;
        $userNGO->save();
        activity()->on($userNGO)
            ->withProperties(['id' => $userNGO->id])
            ->log('Update Assign User to NGO');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('userNGOAssign');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $userNGO = NGOAssign::find($id);
        $userNGO->delete();
        activity()->on($userNGO)
            ->withProperties(['id' => $userNGO->id])
            ->log('Delete Assign User to NGO');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect()->back();
    }
}