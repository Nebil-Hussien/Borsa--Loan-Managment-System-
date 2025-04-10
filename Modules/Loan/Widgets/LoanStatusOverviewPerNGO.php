<?php
namespace Modules\Loan\Widgets;


use Arrilot\Widgets\AbstractWidget;
use Modules\Loan\Charts\LoanStatusOverviewPerNGOPieChart;
use Modules\Loan\Entities\Loan;

class LoanStatusOverviewPerNGO extends AbstractWidget{
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
        $chart = new LoanStatusOverviewPerNGOPieChart();
        $labels = [];
        $data = [];
        $colors=[];
          foreach (Loan::selectRaw("count(id) count,branch_id")
          ->where('status','active')
          ->groupBy('branch_id')
          ->get() as $key) {
            if ($key->branch_id == '2') {
                array_push($labels, trans_choice('core::general.AWSD', 1));
                array_push($colors,'#faa732');
            }
            if ($key->branch_id == '3') {
                array_push($labels, trans_choice('core::general.BEZA', 1));
                array_push($colors,'#0088cc');
            }
            if ($key->branch_id == '4') {
                array_push($labels, trans_choice('core::general.Cheshire', 1));
                array_push($colors,'#5bb75b');
            }
            if ($key->branch_id == '6') {
                array_push($labels, trans_choice('core::general.KD', 1));
                array_push($colors,'#ff0000');
            }
            
            if ($key->branch_id == '7') {
                array_push($labels, trans_choice('core::general.ECCD', 1));
                array_push($colors,'purple');
            }
            if($key->branch_id == 'null')
            {
                array_push($labels, trans_choice('core::general.null', 1));
                array_push($colors,'blue');

            }
            array_push($data,$key->count);
        }
        $chart->options([
             'plotOptions' => [
                'pie' => [
                    'size'=> 200
                ],
                ]
            ]);
        $chart->labels(array_values($labels));
        $chart->title(trans_choice('core::general.number_of_loan_disbursed_per_ngo', 1));
        $chart->dataset("Number of Loan Disbursed Per NGO",'pie',array_values($data))->color(array_values($colors));
        return theme_view('loan::widgets.loan_status_overview_per_ngo', [
            'config' => $this->config,
            'chart'=>$chart
        ]);
    }
}