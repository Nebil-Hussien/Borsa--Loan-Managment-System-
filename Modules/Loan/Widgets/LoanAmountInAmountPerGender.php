<?php
 namespace Modules\Loan\Widgets;

use Arrilot\Widgets\AbstractWidget;
use Modules\Loan\Charts\LoanAmountInAmountPerGenderBarChart;
use Modules\Loan\Entities\Loan;
use Illuminate\Support\Facades\DB;

class LoanAmountInAmountPerGender extends AbstractWidget{
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
        $chart = new LoanAmountInAmountPerGenderBarChart();
        $labels = [];
        $labels1 = [];
        $data = [];
        $data1 = [];
        $expected = [];
        $expected1 = [];
        $colors=['blue','red','pink','black'];
        $colors1=[];
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
$data = Loan::join("clients", "clients.id", "loans.client_id")
        ->where('loans.status', 'pending')
        ->orwhere('loans.status', 'submitted')
        ->where('gender', 'female')
        ->selectRaw('loans.*')
        ->get();
        $totalPrincipal = 0 + $data->sum('principal');
        array_push($expected, round($totalPrincipal, 2));
    $data1 = Loan::join("clients", "clients.id", "loans.client_id")
                ->where('loans.status', 'pending')
                ->orwhere('loans.status', 'submitted')
                ->where('gender', 'male')
                ->selectRaw('loans.*')
                ->get();
            $totalPrincipal1 = 0 + $data1->sum('principal');
            array_push($expected1, round($totalPrincipal1, 2));
      
       
    //$chart->labels(trans_choice('core::general.male', 1),trans_choice('core::general.female', 1));
    $chart->label(trans_choice('core::general.gender', 1));
    $chart->title(trans_choice('core::general.loan_disbursed_applied_per_gender', 1));
    $chart->dataset(trans_choice('core::general.female', 1), 'bar', array_values($expected))->color('red');
    $chart->dataset(trans_choice('core::general.male', 1), 'bar', array_values($expected1))->color('blue');
    
        return theme_view('loan::widgets.loan_status_overview', [
            'config' => $this->config,
            'chart'=>$chart
        ]);
    }
}