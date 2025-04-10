<?php

namespace Modules\Client\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Laracasts\Flash\Flash;
use Modules\Branch\Entities\Branch;
use Modules\Client\Entities\Client;
use Modules\Client\Entities\ClientType;
use Modules\Client\Entities\ClientUser;
use Modules\Client\Entities\Profession;
use Modules\Client\Entities\Title;
use Modules\Core\Entities\Country;
use Modules\Core\Entities\Cities;
use Modules\Client\Entities\ClientIdentificationType;
use Modules\CustomField\Entities\CustomField;
use Modules\User\Entities\User;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class ClientController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:client.clients.index'])->only(['index', 'show', 'get_clients']);
        $this->middleware(['permission:client.clients.create'])->only(['create', 'store']);
        $this->middleware(['permission:client.clients.edit'])->only(['edit', 'update']);
        $this->middleware(['permission:client.clients.destroy'])->only(['destroy']);
        $this->middleware(['permission:client.clients.user.create'])->only(['store_user', 'create_user']);
        $this->middleware(['permission:client.clients.user.destroy'])->only(['destroy_user']);
        $this->middleware(['permission:client.clients.activate'])->only(['change_status']);
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
        $status = $request->status;
        // $auth = Auth::user()->roles->pluck('name');
        // echo ($auth);
        // if($auth == "admin" || "Partners")
        // {
        //      $loan_officer_id = $request->loan_officer_id;

        // }else{
        //     $loan_officer_id = Auth::id();
        // }
        $authUser = Auth::user();
        $request_forRestLoan_officer = $request->loan_officer_id;
        $loan_officer_id = determin_the_role_forLoanView_loan_officer($authUser, $request_forRestLoan_officer);
        $request_forRestBranch = $request->branch_id;
        //$branch_id = $request->branch_id;
        $branch_id = determin_the_role_forLoanView_branch($authUser, $request_forRestBranch);
        $request_forRestCity = $request->city_id;
        $city_id = $request->city_id;
        //$city_id = determin_the_role_forLoanView_city($authUser, $request_forRestCity);
        $data = Client::leftJoin("branches", "branches.id", "clients.branch_id")
            ->leftJoin("users", "users.id", "clients.loan_officer_id")
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->when($city_id, function ($query) use ($city_id) {
                $query->where("clients.city_id", $city_id);
            })
            ->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                $query->where("clients.loan_officer_id", $loan_officer_id);
            })
            ->when($branch_id, function ($query) use ($branch_id) {
                $query->where("clients.branch_id", $branch_id);
            })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('clients.first_name', 'like', "%$search%");
                $query->orWhere('clients.last_name', 'like', "%$search%");
                $query->orWhere('clients.account_number', 'like', "%$search%");
                $query->orWhere('clients.mobile', 'like', "%$search%");
                $query->orWhere('clients.external_id', 'like', "%$search%");
                $query->orWhere('clients.email', 'like', "%$search%");
            })
            ->when($status, function ($query) use ($status) {
                $query->where('clients.status', $status);
            })
            ->selectRaw("branches.name branch,concat(users.first_name,' ',users.middle_name, ' ' ,users.last_name) staff,clients.id,clients.loan_officer_id,clients.first_name,clients.middle_name,clients.last_name,clients.gender,clients.mobile,clients.email,clients.external_id,clients.status")
            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('client::client.index', compact('data'));
    }

    public function get_clients(Request $request)
    {
        $status = $request->status;
        $query = DB::table("clients")
            ->leftJoin("branches", "branches.id", "clients.branch_id")
            ->leftJoin("users", "users.id", "clients.loan_officer_id")
            ->selectRaw("branches.name branch,concat(users.first_name,' ',users.last_name) staff,clients.id,clients.loan_officer_id,concat(clients.first_name,' ',clients.middle_name' ',clients.last_name) name,clients.gender,clients.mobile,clients.email,clients.external_id,clients.status")
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            });
        return DataTables::of($query)->editColumn('staff', function ($data) {
            return $data->staff;
        })->editColumn('action', function ($data) {
            $action = '<div class="btn-group"><button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-navicon"></i></button> <ul class="dropdown-menu dropdown-menu-right" role="menu">';
            $action .= '<li><a href="' . url('client/' . $data->id . '/show') . '" class="">' . trans_choice('user::general.detail', 2) . '</a></li>';
            if (Auth::user()->hasPermissionTo('client.clients.edit')) {
                $action .= '<li><a href="' . url('client/' . $data->id . '/edit') . '" class="">' . trans_choice('user::general.edit', 2) . '</a></li>';
            }
            if (Auth::user()->hasPermissionTo('client.clients.destroy')) {
                $action .= '<li><a href="' . url('client/' . $data->id . '/destroy') . '" class="confirm">' . trans_choice('user::general.delete', 2) . '</a></li>';
            }
            $action .= "</ul></li></div>";
            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('client/' . $data->id . '/show') . '">' . $data->id . '</a>';
        })->editColumn('name', function ($data) {
            return '<a href="' . url('client/' . $data->id . '/show') . '">' . $data->name . '</a>';
        })->editColumn('gender', function ($data) {
            if ($data->gender == "male") {
                return trans_choice('core::general.male', 1);
            }
            if ($data->gender == "female") {
                return trans_choice('core::general.female', 1);
            }
            if ($data->gender == "other") {
                return trans_choice('core::general.other', 1);
            }
            if ($data->gender == "unspecified") {
                return trans_choice('core::general.unspecified', 1);
            }
        })->editColumn('status', function ($data) {
            if ($data->status == "pending") {
                return trans_choice('core::general.pending', 1);
            }
            if ($data->status == "active") {
                return trans_choice('core::general.active', 1);
            }
            if ($data->status == "inactive") {
                return trans_choice('core::general.inactive', 1);
            }
            if ($data->gender == "deceased") {
                return trans_choice('client::general.deceased', 1);
            }
            if ($data->gender == "unspecified") {
                return trans_choice('core::general.unspecified', 1);
            }
        })->rawColumns(['id', 'name', 'action'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $client_types = ClientType::all();
        $users = User::whereHas('roles', function ($query) {
            return $query->where('name', '!=', 'client');
        })->get();
        $branches = Branch::all();
        $countries = Country::all();
        $cities = Cities::all();
        $client_identification_types = ClientIdentificationType::all();
        $custom_fields = CustomField::where('category', 'add_client')->where('active', 1)->get();
        return theme_view('client::client.create', compact('branches', 'cities', 'countries', 'client_identification_types', 'client_types', 'users', 'custom_fields'));
    }
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'phone' => ['required', 'numeric'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'client_type_id' => ['required'],
            'branch_id' => ['required'],
            'first_name' => ['required'],
            'last_name' => ['required'],
            'middle_name' => ['required'],
            'mother_name' => ['required'],
            'gender' => ['required'],
            'dob' => ['required'],
            'city_id' => ['required', 'string'],
            'zone' => ['required', 'string'],
            'kebele' => ['required', 'string'],
            'state' => ['required', 'string'],
            'country_id' => ['required', 'string'],
            'client_identification_type_id' => ['required', 'string'],
            'identification_no'  => ['required', 'string'],
            'identification_issued' => ['required', 'string'],
            'identification_expire' => ['required', 'string'],
            'created_date' => ['required', 'date'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png'],
        ]);

        if ($request->client_type_id == 1) {
            $request->validate([
                'total_children' => ['required', 'string'],
                'saving_account' => ['required', 'string'],
                'exist_loan' => ['required', 'string'],
            ]);
        }
        if ($request->client_type_id == 2) {
            $request->validate([
                'company_name' => ['required', 'string'],
                'business_type' => ['required', 'string'],
                'registration_date' => ['required', 'date'],
                'lic_number' => ['required', 'string'],
                'lic_renewal_date' => ['required', 'date'],
                'tin_number' => ['required', 'string'],
                'reg_capital' => ['required', 'number'],
                'c_house_no' => ['required', 'string'],
                'c_address' => ['required', 'string'],
                'c_region' => ['required', 'string'],
                'c_city_id' => ['required', 'string'],
                'c_zone' => ['required', 'string'],
                'c_kebele' => ['required', 'string'],
                'c_state' => ['required', 'string'],
                'c_country_id' => ['required', 'string'],
            ]);
        }
        $user = \Modules\User\Entities\User::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'gender' =>  $request->gender,
            'dob' => $request->dob,
            'phone' => $request->phone,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'email_verified_at' => date("Y-m-d H:i:s")
        ]);
        //attach client role
        $role = Role::findByName("client");
        $user->assignRole($role);
        $client = new Client();
        //$client->id = IdGenerator::generate(['table' => 'clients', 'length' => 6, 'prefix' => $request->city_id]);
        //$id = IdGenerator::generate(['table' => 'clients', 'length' => 6, 'prefix' => $request->city_id]);
        $client->client_type_id = $request->client_type_id;
        $client->house_no =    $request->house_no;
        $client->address = $request->address;
        $client->region = $request->region;
        $client->city_id = $request->city_id;
        $client->zone = $request->zone;
        $client->kebele = $request->kebele;
        $client->state = $request->state;
        $client->country_id = $request->country_id;
        $client->branch_id = $request->branch_id;
        $client->first_name = $user->first_name;
        $client->middle_name = $user->middle_name;
        $client->last_name = $user->last_name;
        $client->mother_name = $request->mother_name;
        $client->client_type_id = (isset($request->clientType) && !empty($request->clientType) && $request->clientType == "company") ? 2 : 1;

        if ($request->client_type_id == 2) {
            $client->company_name = $request->company_name;
            $client->business_type = $request->business_type;
            $client->registration_date = date("Y-m-d", strtotime($request->registration_date));
            $client->lic_number = $request->lic_number;
            $client->lic_renewal_date = date("Y-m-d", strtotime($request->lic_renewal_date));
            $client->tin_number = $request->tin_number;
            $client->reg_capital = $request->reg_capital;
            $client->c_house_no = $request->c_house_no;
            $client->c_address = $request->c_address;
            $client->c_region = $request->c_region;
            $client->c_city_id = $request->c_city_id;
            $client->c_zone = $request->c_zone;
            $client->c_kebele = $request->c_kebele;
            $client->c_state = $request->c_state;
            $client->c_country_id = $request->c_country_id;
        }
        $client->dob = date("Y-m-d", strtotime($request->dob));
        $client->client_identification_type_id = $request->client_identification_type_id;        
        $client->identification_no = $request->identification_no;
        $client->identification_issued = date("Y-m-d", strtotime($request->identification_issued));
        $client->identification_expire = date("Y-m-d", strtotime($request->identification_expire));
        if ($request->client_type_id == 1) {
            $client->total_children = $request->total_children;
            $client->saving_account = $request->saving_account;
            $client->exist_loan = $request->exist_loan;
        }
        $client->created_by_id = Auth::id();
        $authUser = Auth::user();
        $request_forRestLoan_officer = $request->loan_officer_id;
        $client->loan_officer_id = determin_the_role_forLoanView_loan_officer($authUser, $request_forRestLoan_officer);
        $client->gender = $user->gender;
        $client->branch_id = (isset($request->branch_id) && $request->branch_id != "") ? $request->branch_id : 1;
        $client->mobile = $user->phone;
        $client->email = $request->email;
        $client->created_date = date("Y-m-d");
        if ($request->hasFile('photo')) {
            $file_name = $request->file('photo')->store('public/uploads/clients');
            $client->photo = basename($file_name);
        }
        $client->save();
        $client_user = new ClientUser();
        $client_user->created_by_id = Auth::id();
        $client_user->user_id = $user->id;
        $client_user->client_id = $client->id;
        $client_user->save();
        session(['client_id' => $client->id]);
        custom_fields_save_form('add_client', $request, $client->id);
        activity()->on($client)
            ->withProperties(['id' => $client->id])
            ->log('Create Client');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('client');
        $user;
    }
    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $client = Client::with('loan_officer')->find($id);
        $custom_fields = CustomField::where('category', 'add_client')->where('active', 1)->get();
        return theme_view('client::client.show', compact('client', 'custom_fields'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $client = Client::find($id);
        $client_userID = DB::table('client_users')->where('client_id',$id)->pluck('user_id');
        $userID = $client_userID[0];
        $user = User::find($userID);
        $titles = Title::all();
        $professions = Profession::all();
        $client_types = ClientType::all();
        $cities = Cities::all();
        $client_identification_types = ClientIdentificationType::all();
        $users = User::whereHas('roles', function ($query) {
            return $query->where('name', '!=', 'client');
        })->get();
        $branches = Branch::all();
        $countries = Country::all();
        $custom_fields = CustomField::where('category', 'add_client')->where('active', 1)->get();
        return theme_view('client::client.edit', compact('user','client', 'cities', 'client_identification_types', 'titles', 'professions', 'client_types', 'users', 'branches', 'countries', 'custom_fields'));
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
            'client_type_id' => ['required'],
            'branch_id' => ['required'],
            'first_name' => ['required'],
            'middle_name' => ['required'],
            'last_name' => ['required'],
            'mother_name' => ['required'],
            'phone' => ['required', 'numeric'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'username' => ['required', 'string','max:255'],
            'gender' => ['required'],
            'city_id' => ['required', 'string'],
            'kebele' => ['required', 'string'],
            'country_id' => ['required', 'string'],
            'client_identification_type_id' => ['required', 'string'],
            'identification_no'  => ['required', 'string'],
            'identification_issued' => ['required', 'string'],
            'identification_expire' => ['required', 'string'],
            'created_date' => ['required', 'date'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png'],
        ]);

        if ($request->client_type_id == 1) {
            $request->validate([
                'total_children' => ['required', 'string'],
                'saving_account' => ['required', 'string'],
                'exist_loan' => ['required', 'string'],
            ]);
        }
        if ($request->client_type_id == 2) {
            $request->validate([
                'company_name' => ['required', 'string'],
                'business_type' => ['required', 'string'],
                'registration_date' => ['required', 'date'],
                'lic_number' => ['required', 'string'],
                'lic_renewal_date' => ['required', 'date'],
                'tin_number' => ['required', 'string'],
                'reg_capital' => ['required', 'number'],
                'c_house_no' => ['required', 'string'],
                'c_address' => ['required', 'string'],
                'c_region' => ['required', 'string'],
                'c_city_id' => ['required', 'string'],
                'c_zone' => ['required', 'string'],
                'c_kebele' => ['required', 'string'],
                'c_state' => ['required', 'string'],
                'c_country_id' => ['required', 'string'],
            ]);
        }
        $user = User::find($id);
        $user->first_name = $request->first_name;
        $user->middle_name = $request->middle_name;
        $user->last_name = $request->last_name;
        $user->gender =  $request->gender;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->email_verified_at = date("Y-m-d H:i:s");
        //attach client role
        $role = Role::findByName("client");
        $user->assignRole($role);
        $user->save();
        $client = Client::find($id);
        //$client->id = IdGenerator::generate(['table' => 'clients', 'length' => 6, 'prefix' => $request->city_id]);
        //$id = IdGenerator::generate(['table' => 'clients', 'length' => 6, 'prefix' => $request->city_id]);
        $client->client_type_id = $request->client_type_id;
        $client->house_no =    $request->house_no;
        $client->address = $request->address;
        $client->region = $request->region;
        $client->city_id = $request->city_id;
        $client->zone = $request->zone;
        $client->kebele = $request->kebele;
        $client->state = $request->state;
        $client->country_id = $request->country_id;
        $client->branch_id = $request->branch_id;
        $client->first_name = $user->first_name;
        $client->middle_name = $request->middle_name;
        $client->last_name = $user->last_name;
        $client->mother_name = $request->mother_name;
        $client->mobile = $request->phone;
        $client->marital_status = $request->marital_status;
        $client->client_type_id = (isset($request->clientType) && !empty($request->clientType) && $request->clientType == "company") ? 2 : 1;
        if ($request->client_type_id == 1) {
            $client->total_children = $request->total_children;
            $client->saving_account = $request->saving_account;
            $client->exist_loan = $request->exist_loan;
        }
        elseif ($request->client_type_id == 2) {
            $client->company_name = $request->company_name;
            $client->business_type = $request->business_type;
            $client->registration_date = date("Y-m-d", strtotime($request->registration_date));
            $client->lic_number = $request->lic_number;
            $client->lic_renewal_date = date("Y-m-d", strtotime($request->lic_renewal_date));
            $client->tin_number = $request->tin_number;
            $client->reg_capital = $request->reg_capital;
            $client->c_house_no = $request->c_house_no;
            $client->c_address = $request->c_address;
            $client->c_region = $request->c_region;
            $client->c_city_id = $request->c_city_id;
            $client->c_zone = $request->c_zone;
            $client->c_kebele = $request->c_kebele;
            $client->c_state = $request->c_state;
            $client->c_country_id = $request->c_country_id;
        }
        $client->dob = date("Y-m-d", strtotime($request->dob));
        $client->client_identification_type_id = $request->client_identification_type_id;
        $client->identification_no = $request->identification_no;
        $client->identification_issued = date("Y-m-d", strtotime($request->identification_issued));
        $client->identification_expire = date("Y-m-d", strtotime($request->identification_expire));
        $client->updated_by_id = Auth::id();
        $client->updated_at = date("Y-m-d");;
        $authUser = Auth::user();
        $request_forRestLoan_officer = $request->loan_officer_id;
        $client->loan_officer_id = determin_the_role_forLoanView_loan_officer($authUser, $request_forRestLoan_officer);
        $client->gender = $request->gender;
        $client->mobile = $request->phone;
        $client->email = $request->email;
        if ($request->hasFile('photo')) {
            $file_name = $request->file('photo')->store('public/uploads/clients');
            $client->photo = basename($file_name);
        }
        $client->save();
        custom_fields_save_form('add_client', $request, $client->id);
        activity()->on($client)
            ->withProperties(['id' => $client->id])
            ->log('Update Client');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('client');
    }
    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $client = Client::find($id);
        $user = User::find($id);
        $client->delete();
        $user->delete();
        activity()->on($client)
            ->withProperties(['id' => $client->id])
            ->log('Delete Client');
        activity()->on($user)
            ->withProperties(['id' => $user->id])
            ->log('Delete User');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect()->back();
    }

    public function create_user($id)
    {
        $users = User::role('client')->get();
        $client = Client::find($id);
        return theme_view('client::client.create_user', compact('users', 'client'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store_user(Request $request, $id)
    {
        if ($request->existing == 1) {
            $request->validate([
                'user_id' => ['required'],
            ]);
            if (ClientUser::where('client_id', $id)->where('user_id', $request->user_id)->get()->count() > 0) {
                \flash(trans_choice("client::general.user_already_added", 1))->error()->important();
                return redirect()->back();
            }
            $client_user = new ClientUser();
            $client_user->client_id = $id;
            $client_user->created_by_id = Auth::id();
            $client_user->user_id = $request->user_id;
            $client_user->save();
        } else {
            $request->validate([
                'first_name' => ['required'],
                'last_name' => ['required'],
                'gender' => ['required'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:6', 'confirmed'],
            ]);
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'gender' => $request->gender,
                'phone' => $request->phone,
                'email' => $request->email,
                'notes' => $request->notes,
                'address' => $request->address,
                'password' => Hash::make($request->password),
                'email_verified_at' => date("Y-m-d H:i:s")
            ]);
            //attach client role
            $role = Role::findByName('client');
            $user->assignRole($role);
            $client_user = new ClientUser();
            $client_user->client_id = $id;
            $client_user->created_by_id = Auth::id();
            $client_user->user_id = $user->id;
            $client_user->save();
        }
        activity()->log('Create Client User');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('client/' . $id . '/show');
    }

    public function destroy_user($id)
    {
        ClientUser::destroy($id);
        activity()->log('Delete Client User');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        return redirect()->back();
    }

    public function change_status(Request $request, $id)
    {
        $request->validate([
            'status' => ['required'],
            'date' => ['required', 'date'],
        ]);
        $client = Client::find($id);
        $client->status = $request->status;
        $client->save();
        activity()->on($client)
            ->withProperties(['id' => $client->id])
            ->log('Update Client Status');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect()->back();
    }
}