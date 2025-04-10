<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sentinel;
use Modules\User\Entities\User;
use Laracasts\Flash\Flash;
class QrLoginController extends Controller
{
    public function index(Request $request) {
    	
		return theme_view('user::auth.QrLogin');
	}
	public function indexoption2(Request $request) {
    	
		return theme_view('user::auth.QrLogin2');
	}
	public function checkUser(Request $request) {
		 $result =0;
			if ($request->data) {
				$user = User::where('QRpassword',$request->data)->first();
				if ($user) {
					Sentinel::authenticate($user);
					session(['client_id' => $client->client_id]);
                	return redirect('/portal/dashboard');
				    $result =1;
				 }else{
					 Flash::warning(trans_choice('portal::general.no_linked_client_found', 1));
                		$this->guard()->logout();
                		$request->session()->invalidate();
                		return redirect('login');
				 	$result =0;

				 }
			}
			
			return $result;
	}

}