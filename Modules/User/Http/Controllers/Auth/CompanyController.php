<?php

namespace Modules\User\Http\Controllers\Auth;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Modules\Branch\Entities\Branch;
use Modules\Client\Entities\Client;
use Modules\Client\Entities\ClientUser;
use Spatie\Permission\Models\Role;
use Modules\Core\Entities\Country;
use Modules\Core\Entities\Cities;
use Modules\Client\Entities\ClientIdentificationType;

class CompanyController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        $branches = Branch::all();
        $countries = Country::all();
        $cities = Cities::all();
        $client_identification_types = ClientIdentificationType::all();
        return theme_view('user::auth.company_register', compact('branches', 'cities', 'countries', 'client_identification_types'));
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            /*'branch_id' => ['required'],*/
            'first_name' => ['required'],
            'last_name' => ['required'],
            'gender' => ['required'],
            'phone' => ['required', 'numeric'],
            'agree' => ['required'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return \Modules\User\Entities\User
     */
    protected function create(array $data)
    {

        $user = \Modules\User\Entities\User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'gender' => $data['gender'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'email_verified_at' => date("Y-m-d H:i:s")
        ]);
        //attach client role
        $role = Role::findByName("client");
        $user->assignRole($role);
        //create client record
        $client = new Client();
        $client->first_name = $user->first_name;
        $client->middle_name = $data['middle_name'];
        $client->last_name = $user->last_name;
        $client->mother_name = $data['mother_name'];
        $client->age = $data['age'];
        $client->house_no =    $data['house_no'];
        $client->address = $data['address'];
        $client->region = $data['region'];
        $client->city = $data['city'];
        $client->zone = $data['zone'];
        $client->kebele = $data['kebele'];
        $client->state = $data['state'];
        $client->country_id = $data['country_id'];
        $client->client_identification_type_id = $data['client_identification_type_id'];
        $client->identification_no = $data['identification_no'];
        $client->identification_issued = $data['identification_issued'];
        $client->identification_expire = $data['identification_expire'];
        $client->total_children = $data['total_children'];
        $client->saving_account = $data['saving_account'];
        $client->exist_loan = $data['exist_loan'];
        $client->created_by_id = Auth::id();
        $client->gender = $user->gender;
        $client->branch_id = (isset($data['branch_id']) && $data['branch_id'] != "") ? $data['branch_id'] : 1;
        $client->mobile = $user->phone;
        $client->email = $user->email;
        $client->created_date = date("Y-m-d");
        $client->save();
        $client_user = new ClientUser();
        $client_user->client_id = $client->id;
        $client_user->created_by_id = Auth::id();
        $client_user->user_id = $user->id;
        $client_user->save();
        session(['client_id' => $client->id]);
        return $user;
    }
}