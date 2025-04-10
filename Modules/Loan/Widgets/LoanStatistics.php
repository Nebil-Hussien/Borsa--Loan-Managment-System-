<?php
/**
 * Created by PhpStorm.
 * User: tj
 * Date: 8/2/19
 * Time: 8:50 PM
 */

namespace Modules\Loan\Widgets;

use Arrilot\Widgets\AbstractWidget;
use Illuminate\Support\Facades\DB;
use Modules\Loan\Entities\Loan;
use Illuminate\Support\Carbon;
use Modules\Loan\Entities\LoanRepaymentSchedule;
use Modules\Loan\Entities\LoanTransaction;
use Illuminate\Support\Facades\Auth;

class LoanStatistics extends AbstractWidget
{
    /**
     * The configuration array.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Treat this method as a controller action.
     * Return view() or other content to display.
     */
    public function run()
    {
        if(Auth::user()->hasRole('NGO_HeadOffice'))
        {
        $loan_disbursed = Loan::where('status', 'active')->where('branch_id',Auth::user()->branch_id)->sum('principal');
        $total_repayment = LoanTransaction::where('reversed', 0)
            ->where('branch_id',Auth::user()->branch_id)
            ->whereIn('loan_transaction_type_id', [2, 5, 8])
            ->sum('amount');
        $loans = Loan::with('repayment_schedules')
                        ->where('status','active')
                        ->where('loans.branch_id', Auth::user()->branch_id)
                        ->get();
         $repayment_schedules = LoanRepaymentSchedule::join("loans", "loans.id", "loan_repayment_schedules.loan_id")
            ->selectRaw('loan_repayment_schedules.*')
            ->whereBetween('due_date', [Carbon::today()->startOfMonth(), Carbon::today()->endOfMonth()])
            ->where('loans.status', 'active')
            ->where('loans.branch_id',Auth::user()->branch_id)
            ->get();
        }
        elseif(Auth::user()->hasRole('NGO_RegionalManagment')){
             $loan_disbursed = Loan::join('clients', 'loans.client_id', 'clients.id')->where('loans.status', 'active')
                ->where('loans.branch_id',Auth::user()->branch_id)
                ->where('clients.city_id',Auth::user()->city_id)
                ->sum('principal');
             $total_repayment = LoanTransaction::join('loans','loan_transactions.loan_id','loans.id')
                ->join('clients','loans.client_id','clients.id')
                ->where('reversed', 0)
                ->where('loans.branch_id',Auth::user()->branch_id)
                ->where('clients.city_id',Auth::user()->city_id)
                ->whereIn('loan_transaction_type_id', [2, 5, 8])
                ->sum('amount');
            $loans = Loan::with('repayment_schedules')
                        ->join('clients', 'loans.client_id', 'clients.id')
                        ->where('loans.status','active')
                         ->where('loans.branch_id', Auth::user()->branch_id)
                        ->where('clients.city_id',Auth::user()->city_id)
                        ->get();
         $repayment_schedules = LoanRepaymentSchedule::join("loans", "loans.id", "loan_repayment_schedules.loan_id")
            ->join('clients', 'loans.client_id', 'clients.id')
            ->selectRaw('loan_repayment_schedules.*')
            ->whereBetween('due_date', [Carbon::today()->startOfMonth(), Carbon::today()->endOfMonth()])
            ->where('loans.status', 'active')
             ->where('loans.branch_id', Auth::user()->branch_id)
            ->where('clients.city_id',Auth::user()->city_id)
            ->get();

        }
        elseif(Auth::user()->hasRole('NGO_OperationOfficer'))
        {
            $loan_disbursed = Loan::where('status', 'active')
                ->where('loans.loan_officer_id', Auth::user()->id)
                ->sum('principal');
             $total_repayment = LoanTransaction::join('loans','loan_transactions.loan_id','loans.id')
                ->join('clients','loans.client_id','clients.id')
                ->where('reversed', 0)
                ->where('loans.loan_officer_id', Auth::user()->id)
                ->whereIn('loan_transaction_type_id', [2, 5, 8])
                ->sum('amount');
            $loans = Loan::with('repayment_schedules')
                ->where('status','active')
                ->where('loans.loan_officer_id', Auth::user()->id)
                ->get();
            
            $repayment_schedules = LoanRepaymentSchedule::join("loans", "loans.id", "loan_repayment_schedules.loan_id")
            ->selectRaw('loan_repayment_schedules.*')
            ->whereBetween('due_date', [Carbon::today()->startOfMonth(), Carbon::today()->endOfMonth()])
            ->where('loans.status', 'active')
            ->where('loans.loan_officer_id',Auth::user()->id)
            ->get();
            //return "here";

        }        
        else {
        $total_repayment = LoanTransaction::join('loans','loan_transactions.loan_id','loans.id')
            ->where('reversed', 0)
            ->whereIn('loan_transaction_type_id', [2, 5, 8])
            ->sum('amount');
        $loan_disbursed = Loan::where('status', 'active')
                ->sum('principal'); 
        $loans = Loan::with('repayment_schedules')->where('status','active')->get();
         $repayment_schedules = LoanRepaymentSchedule::join("loans", "loans.id", "loan_repayment_schedules.loan_id")
            ->selectRaw('loan_repayment_schedules.*')
            ->whereBetween('due_date', [Carbon::today()->startOfMonth(), Carbon::today()->endOfMonth()])
            ->where('loans.status', 'active')
            ->get();
            //return "here";
        }
        return theme_view('loan::widgets.loan_statistics', [
            'config' => $this->config,
            'loans' => $loans,
            'repayment_schedules' => $repayment_schedules,
            'loan_disbursed' => $loan_disbursed,
            'total_repayment' => $total_repayment,

        ]);
    }
}