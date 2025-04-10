<?php
 namespace Modules\Loan\Widgets;

use Arrilot\Widgets\AbstractWidget;
use Modules\Loan\Charts\LoanAmountInNumberDisbursedPerNGOPerGenderPieChart;
use Modules\Loan\Entities\Loan;

class LoanAmountInNumberDisbursedPerNGOPerGender extends AbstractWidget{
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
        $chart = new LoanAmountInNumberDisbursedPerNGOPerGenderPieChart();
        $labels = [];
        $data = [];
        $colors=[];
        $data1=[];
        $chartOptions = [
    'scales' => [
        'yAxes' => [
            
                'stacked' => true,
            ],
            'xAxes' => 
                [
                    'stacked' => true,
                ],
        ],
        
    
]; 
    foreach (Loan::Join("clients", "clients.id", "loans.client_id")
    ->where("loans.status", "active")
    ->selectRaw("count(client_id) count")
    ->groupBy('loans.branch_id')
    ->where('gender','female')
    ->get() as $key) 
    {
            array_push($data,$key->count);
    }
    foreach (Loan::Join("clients", "clients.id", "loans.client_id")
    ->where("loans.status", "active")
    ->selectRaw("count(client_id) count")
    ->groupBy('loans.branch_id')
    ->where('gender','male')
    ->get() as $line) 
    {
        array_push($data1,$line->count);
    }
        $chart->labels([trans_choice('user::general.AWSD', 1),trans_choice('user::general.BEZA', 1),trans_choice('user::general.Cheshire', 1),trans_choice('user::general.KD', 1),trans_choice('user::general.ECCD', 1)]);
        $chart->title(trans_choice('core::general.loan_disbursed_number_per_ngo_per_gender', 1));
        $chart->dataset(trans_choice('user::general.female', 1),'bar',array_values($data))->color('red');
        $chart->dataset(trans_choice('user::general.male', 1),'bar',array_values($data1))->color('blue');
        $chart->options($chartOptions);
        return theme_view('loan::widgets.loan_amount_in_number_disbursed_per_ngo_per_gender', [
            'config' => $this->config,
            'chart'=>$chart
        ]);
    }
}