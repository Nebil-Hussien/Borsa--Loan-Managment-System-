<?php

namespace Modules\DashboardCOOP\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Branch\Entities\Branch;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Entities\LoanProduct;
use Modules\Loan\Exports\LoanExport;
use Modules\User\Entities\User;
use Modules\Core\Entities\Cities;

class DashboardCOOPController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {   if(Auth::user()->hasRole('COOP_HeadOffice')){
    
            $dataRepaymentNumber = DB::table("loan_transactions")
                ->join("loans", "loan_transactions.loan_id", "loans.id")
                ->join("branches", "loans.branch_id", "branches.id")
                ->join("clients", "loans.client_id", "clients.id")
                ->leftJoin("payment_details", "loan_transactions.payment_detail_id", "payment_details.id")
                ->leftJoin("users", "loan_transactions.created_by_id", "users.id")
                ->leftJoin("payment_types", "payment_details.payment_type_id", "payment_types.id")
                ->where('loan_transaction_type_id', 2)
                ->where('loan_transactions.coop_id', Auth::user()->coop_id)
                ->selectRaw("count(id) as count")
                ->get();
            $dataRepaymentAmount = DB::table("loan_transactions")
                ->join("loans", "loan_transactions.loan_id", "loans.id")
                ->join("branches", "loans.branch_id", "branches.id")
                ->join("clients", "loans.client_id", "clients.id")
                ->leftJoin("payment_details", "loan_transactions.payment_detail_id", "payment_details.id")
                ->leftJoin("users", "loan_transactions.created_by_id", "users.id")
                ->leftJoin("payment_types", "payment_details.payment_type_id", "payment_types.id")
                ->where('loan_transaction_type_id', 2)
                ->where('loan_transactions.coop_id', Auth::user()->coop_id)
                ->selectRaw("loan_transactions.*")
                ->get();
                $totalAmountTransaction = 0 + $dataRepaymentAmount->sum('amount');
         }
         elseif(Auth::user()->hasRole('COOP_RegionalManagment')){
             $dataRepaymentNumber = DB::table("loan_transactions")
                ->join("loans", "loan_transactions.loan_id", "loans.id")
                ->join("branches", "loans.branch_id", "branches.id")
                ->join("clients", "loans.client_id", "clients.id")
                ->leftJoin("payment_details", "loan_transactions.payment_detail_id", "payment_details.id")
                ->leftJoin("users", "loan_transactions.created_by_id", "users.id")
                ->leftJoin("payment_types", "payment_details.payment_type_id", "payment_types.id")
                ->where('loan_transaction_type_id', 2)
                ->where('loan_transactions.coop_id', Auth::user()->coop_id)
                ->where('clients.city_id',Auth::user()->city_id)
                ->selectRaw("count(id) as count")
                ->get();
            $dataRepaymentAmount = DB::table("loan_transactions")
                ->join("loans", "loan_transactions.loan_id", "loans.id")
                ->join("branches", "loans.branch_id", "branches.id")
                ->join("clients", "loans.client_id", "clients.id")
                ->leftJoin("payment_details", "loan_transactions.payment_detail_id", "payment_details.id")
                ->leftJoin("users", "loan_transactions.created_by_id", "users.id")
                ->leftJoin("payment_types", "payment_details.payment_type_id", "payment_types.id")
                ->where('loan_transaction_type_id', 2)
                ->where('loan_transactions.coop_id', Auth::user()->coop_id)
                ->where('clients.city_id',Auth::user()->city_id)
                ->selectRaw("loan_transactions.*")
                ->get();

                 $totalAmountTransaction = 0 + $dataRepaymentAmount->sum('amount');
         }
         elseif(Auth::user()->hasRole('COOP_OperationOfficer')){
             $dataRepaymentNumber = DB::table("loan_transactions")
                ->join("loans", "loan_transactions.loan_id", "loans.id")
                ->join("branches", "loans.branch_id", "branches.id")
                ->join("clients", "loans.client_id", "clients.id")
                ->leftJoin("payment_details", "loan_transactions.payment_detail_id", "payment_details.id")
                ->leftJoin("users", "loan_transactions.created_by_id", "users.id")
                ->leftJoin("payment_types", "payment_details.payment_type_id", "payment_types.id")
                ->where('loan_transaction_type_id', 2)
                ->where('clients.loan_officer_id', Auth::user()->id)
                ->selectRaw("count(id) as count")
                ->get();
            $dataRepaymentAmount = DB::table("loan_transactions")
                ->join("loans", "loan_transactions.loan_id", "loans.id")
                ->join("branches", "loans.branch_id", "branches.id")
                ->join("clients", "loans.client_id", "clients.id")
                ->leftJoin("payment_details", "loan_transactions.payment_detail_id", "payment_details.id")
                ->leftJoin("users", "loan_transactions.created_by_id", "users.id")
                ->leftJoin("payment_types", "payment_details.payment_type_id", "payment_types.id")
                ->where('loan_transaction_type_id', 2)
                ->selectRaw("loan_transactions.*")
                ->get();
                 $totalAmountTransaction = 0 + $dataRepaymentAmount->sum('amount');

         }
        
        return theme_view('dashboardcoop::index',compact('dataRepaymentNumber','totalAmountTransaction'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return theme_view('dashboardcoop::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return theme_view('dashboardcoop::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return theme_view('dashboardcoop::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
