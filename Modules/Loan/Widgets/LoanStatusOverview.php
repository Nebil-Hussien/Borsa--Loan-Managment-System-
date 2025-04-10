<?php
namespace Modules\Loan\Widgets;


use Arrilot\Widgets\AbstractWidget;
use Modules\Loan\Charts\LoanStatusOverviewPieChart;
use Modules\Loan\Entities\Loan;
use Illuminate\Support\Facades\Auth;

class LoanStatusOverview extends AbstractWidget
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
        $chart = new LoanStatusOverviewPieChart();
        $labels = [];
        $data = [];
        $colors=[];
        $key =0;

if(Auth::user()->hasRole('NGO_HeadOffice')){

        foreach (Loan::selectRaw("count(loans.id) count,status")->where('loans.branch_id', Auth::user()->branch_id)->groupBy('status')->get() as $key) {
            if ($key->status == 'submitted') {
                array_push($labels, trans_choice('loan::general.pending_approval', 1));
                array_push($colors,'#faa732');
            }
            if ($key->status == 'approved') {
                array_push($labels, trans_choice('loan::general.awaiting_disbursement', 1));
                array_push($colors,'#0088cc');
            }
            if ($key->status == 'active') {
                array_push($labels, trans_choice('loan::general.active', 1));
                array_push($colors,'#5bb75b');
            }
            if ($key->status == 'rejected') {
                array_push($labels, trans_choice('loan::general.rejected', 1));
                array_push($colors,'#ff0000');
            }
            if ($key->status == 'written_off') {
                array_push($labels, trans_choice('loan::general.written_off', 1));
                array_push($colors,'#ff0000');
            }
            if ($key->status == 'rescheduled') {
                array_push($labels, trans_choice('loan::general.rescheduled', 1));
                array_push($colors,'#faa732');
            }
            if ($key->status == 'withdrawn') {
                array_push($labels, trans_choice('loan::general.withdrawn', 1));
                array_push($colors,'#ff0000');
            }
            if ($key->status == 'pending') {
                array_push($labels, trans_choice('loan::general.pending_approval', 1));
                array_push($colors,'#faa732');
            }
            if ($key->status == 'closed') {
                array_push($labels, trans_choice('loan::general.closed', 1));
                array_push($colors,'#5bb75b');
            }
            array_push($data,$key->count);
        }
    }elseif(Auth::user()->hasRole('NGO_RegionalManagment'))
    {
        foreach (
        Loan::selectRaw("count(loans.id) count,status")
        ->join('clients','loans.client_id','clients.id')
        ->where('loans.branch_id', Auth::user()->branch_id)
         ->where('clients.city_id',Auth::user()->city_id)
         ->groupBy('loans.status')->get() as $key) {
            if ($key->status == 'submitted') {
                array_push($labels, trans_choice('loan::general.pending_approval', 1));
                array_push($colors,'#faa732');
            }
            if ($key->status == 'approved') {
                array_push($labels, trans_choice('loan::general.awaiting_disbursement', 1));
                array_push($colors,'#0088cc');
            }
            if ($key->status == 'active') {
                array_push($labels, trans_choice('loan::general.active', 1));
                array_push($colors,'#5bb75b');
            }
            if ($key->status == 'rejected') {
                array_push($labels, trans_choice('loan::general.rejected', 1));
                array_push($colors,'#ff0000');
            }
            if ($key->status == 'written_off') {
                array_push($labels, trans_choice('loan::general.written_off', 1));
                array_push($colors,'#ff0000');
            }
            if ($key->status == 'rescheduled') {
                array_push($labels, trans_choice('loan::general.rescheduled', 1));
                array_push($colors,'#faa732');
            }
            if ($key->status == 'withdrawn') {
                array_push($labels, trans_choice('loan::general.withdrawn', 1));
                array_push($colors,'#ff0000');
            }
            if ($key->status == 'pending') {
                array_push($labels, trans_choice('loan::general.pending_approval', 1));
                array_push($colors,'#faa732');
            }
            if ($key->status == 'closed') {
                array_push($labels, trans_choice('loan::general.closed', 1));
                array_push($colors,'#5bb75b');
            }
            array_push($data,$key->count);
        }
    
    }elseif(Auth::user()->hasRole('NGO_OperationOfficer'))
    {
        foreach (
            Loan::selectRaw("count(loans.id) count,status")
             ->where('loans.loan_officer_id ', Auth::user()->id)
            ->groupBy('status')->get() as $key) {
            if ($key->status == 'submitted') {
                array_push($labels, trans_choice('loan::general.pending_approval', 1));
                array_push($colors,'#faa732');
            }
            if ($key->status == 'approved') {
                array_push($labels, trans_choice('loan::general.awaiting_disbursement', 1));
                array_push($colors,'#0088cc');
            }
            if ($key->status == 'active') {
                array_push($labels, trans_choice('loan::general.active', 1));
                array_push($colors,'#5bb75b');
            }
            if ($key->status == 'rejected') {
                array_push($labels, trans_choice('loan::general.rejected', 1));
                array_push($colors,'#ff0000');
            }
            if ($key->status == 'written_off') {
                array_push($labels, trans_choice('loan::general.written_off', 1));
                array_push($colors,'#ff0000');
            }
            if ($key->status == 'rescheduled') {
                array_push($labels, trans_choice('loan::general.rescheduled', 1));
                array_push($colors,'#faa732');
            }
            if ($key->status == 'withdrawn') {
                array_push($labels, trans_choice('loan::general.withdrawn', 1));
                array_push($colors,'#ff0000');
            }
            if ($key->status == 'pending') {
                array_push($labels, trans_choice('loan::general.pending_approval', 1));
                array_push($colors,'#faa732');
            }
            if ($key->status == 'closed') {
                array_push($labels, trans_choice('loan::general.closed', 1));
                array_push($colors,'#5bb75b');
            }
            array_push($data,$key->count);
        }

    }
    else{
        foreach (Loan::selectRaw("count(id) count,status")->groupBy('status')->get() as $key) {
            if ($key->status == 'submitted') {
                array_push($labels, trans_choice('loan::general.pending_approval', 1));
                array_push($colors,'#faa732');
            }
            if ($key->status == 'approved') {
                array_push($labels, trans_choice('loan::general.awaiting_disbursement', 1));
                array_push($colors,'#0088cc');
            }
            if ($key->status == 'active') {
                array_push($labels, trans_choice('loan::general.active', 1));
                array_push($colors,'#5bb75b');
            }
            if ($key->status == 'rejected') {
                array_push($labels, trans_choice('loan::general.rejected', 1));
                array_push($colors,'#ff0000');
            }
            if ($key->status == 'written_off') {
                array_push($labels, trans_choice('loan::general.written_off', 1));
                array_push($colors,'#ff0000');
            }
            if ($key->status == 'rescheduled') {
                array_push($labels, trans_choice('loan::general.rescheduled', 1));
                array_push($colors,'#faa732');
            }
            if ($key->status == 'withdrawn') {
                array_push($labels, trans_choice('loan::general.withdrawn', 1));
                array_push($colors,'#ff0000');
            }
            if ($key->status == 'pending') {
                array_push($labels, trans_choice('loan::general.pending_approval', 1));
                array_push($colors,'#faa732');
            }
            if ($key->status == 'closed') {
                array_push($labels, trans_choice('loan::general.closed', 1));
                array_push($colors,'#5bb75b');
            }
            array_push($data,$key->count);
        }

    }        
  $chart->options([
             'plotOptions' => [
                'pie' => [
                    'size'=> 200
                ],
                ]
            ]);
        $chart->title(trans_choice('core::general.total_loan_status', 1));
        $chart->labels(array_values($labels));
        $chart->dataset("Loan Status Overview",'pie',array_values($data))->color(array_values($colors));
        return theme_view('loan::widgets.loan_status_overview', [
            'config' => $this->config,
            'chart'=>$chart
        ]);
    }
}