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
use Haruncpi\LaravelIdGenerator\IdGenerator;

class RegisterController extends Controller
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
        if (isset($_GET['type']) && $_GET['type'] == 'company') {
            return theme_view('user::auth.company_register', compact('branches', 'cities', 'countries', 'client_identification_types'));
        } else {
            return theme_view('user::auth.register', compact('branches', 'cities', 'countries', 'client_identification_types'));
        }
    }


    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {

        if ($data['signup_type'] == 'company') {
            return Validator::make($data, [
                /*'branch_id' => ['required'],*/
                'r_first_name' => ['required'],
                'r_middle_name' => ['required'],
                'r_last_name' => ['required'],
                'r_gender' => ['required'],
                'r_phone' => ['required', 'numeric'],
                'agree' => ['required'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'username' => ['required', 'string', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:6', 'confirmed'],
            ]);
        } else {
            return Validator::make($data, [
                /*'branch_id' => ['required'],*/
                'first_name' => ['required'],
                'middle_name' => ['required'],
                'last_name' => ['required'],
                'gender' => ['required'],
                'phone' => ['required', 'numeric'],
                'agree' => ['required'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'username' => ['required', 'string', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:6', 'confirmed'],
            ]);
        }
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
            'first_name' => $data['signup_type'] == 'company' ? $data['r_first_name'] : $data['first_name'],
            'middle_name' => $data['signup_type'] == 'company' ? $data['r_middle_name'] : $data['middle_name'],
            'last_name' => $data['signup_type'] == 'company' ? $data['r_last_name'] : $data['last_name'],
            'gender' => $data['signup_type'] == 'company' ? $data['r_gender'] : $data['gender'],
            'dob' => $data['signup_type'] == 'company' ? $data['dob'] : $data['dob'],
            'phone' => $data['signup_type'] == 'company' ? $data['r_phone'] : $data['phone'],
            'phone' => $data['signup_type'] == 'company' ? $data['r_phone'] : $data['phone'],
            'email' => $data['email'],
            'username' => $data['username'],
            'city_id' => $data['city_id'],
            'branch_id'=> $data['branch_id'],
            'password' => Hash::make($data['password']),
            'email_verified_at' => date("Y-m-d H:i:s")
        ]);
        //attach client role
        $role = Role::findByName("client");
        $user->assignRole($role);
        //create client record
        $client = new Client();
        $client->UID = mt_rand(1000000000, 9999999999);
        //$client->id = IdGenerator::generate(['table' => 'clients', 'length' => 6, 'prefix' => $data['city_id']]);
        //$id = IdGenerator::generate(['table' => 'clients', 'length' => 6, 'prefix' => $data['city_id']]);
        $client->house_no =    $data['house_no'];
        $client->address = $data['address'];
        $client->region = $data['region'];
        $client->city_id = $data['city_id'];
        $client->zone = $data['zone'];
        $client->kebele = $data['kebele'];
        $client->state = $data['state'];
        $client->country_id = $data['country_id'];
        // $client->branch_id = $data['branch_id'];
        $client->first_name = $user->first_name;
        $client->last_name = $user->last_name;
        $client->client_type_id = (isset($data['signup_type']) && !empty($data['signup_type']) && $data['signup_type'] == "company") ? 2 : 1;

        if ($data['signup_type'] == 'company') {
            $client->company_name = $data['company_name'];
            $client->business_type = $data['business_type'];
            $client->registration_date = date("Y-m-d", strtotime($data['registration_date']));
            $client->lic_number = $data['lic_number'];
            $client->lic_renewal_date = date("Y-m-d", strtotime($data['lic_renewal_date']));
            $client->tin_number = $data['tin_number'];
            $client->reg_capital = $data['reg_capital'];
            $client->c_house_no = $data['c_house_no'];
            $client->c_address = $data['c_address'];
            $client->c_region = $data['c_region'];
            $client->c_city_id = $data['c_city_id'];
            $client->c_zone = $data['c_zone'];
            $client->c_kebele = $data['c_kebele'];
            $client->c_state = $data['c_state'];
            $client->c_country_id = $data['c_country_id'];
            $client->middle_name = $data['r_middle_name'];
            /* $prefix = $data['c_city_id'];
            echo $prefix; */
        } else {

            $client->total_children = $data['total_children'];
            $client->saving_account = $data['saving_account'];
            $client->exist_loan = $data['exist_loan'];
            /* $prefix = $data['city_id'];
            echo $prefix; */
        }
        $client->middle_name = $data['middle_name'];
        $client->mother_name = $data['mother_name'];
        $client->dob = date("Y-m-d", strtotime($data['dob']));
        $client->client_identification_type_id = $data['client_identification_type_id'];
        $client->identification_no = $data['identification_no'];
        $client->identification_issued = date("Y-m-d", strtotime($data['identification_issued']));
        $client->identification_expire = date("Y-m-d", strtotime($data['identification_expire']));

        $client->created_by_id = Auth::id();
        $client->gender = $user->gender;
        // $client->branch_id = (isset($data['branch_id']) && $data['branch_id'] != "") ? $data['branch_id'] : 1;
        $client->mobile = $user->phone;
        $client->email = $user->email;
        $client->created_date = date("Y-m-d");
        $client->save();
        $client_user = new ClientUser();
        $client_user->created_by_id = Auth::id();
        $client_user->user_id = $user->id;
        $client_user->client_id = $client->id;
        $client_user->save();
        session(['client_id' => $client->id]);
        return $user;
    }
}