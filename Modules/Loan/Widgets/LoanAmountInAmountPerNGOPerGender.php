<?php
 namespace Modules\Loan\Widgets;

use Arrilot\Widgets\AbstractWidget;
use Modules\Loan\Charts\LoanAmountInAmountPerNGOPerGenderBarChart;
use Modules\Loan\Entities\Loan;

class LoanAmountInAmountPerNGOPerGender extends AbstractWidget{
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
        $chart = new LoanAmountInAmountPerNGOPerGenderBarChart();
        $labels = [];
        $data = [];
        $colors=[];
        $data=[];
        $data1=[];
        $data2=[];
        $data3=[];
        $data4=[];
        $data5=[];
        $data6=[];
        $data7=[];
        $data8=[];
        $data9=[];
        $expected = [];
        $expected1 = [];
        $expected2 = [];
        $expected3 = [];
        $expected4 = [];
        $expected5 = [];
        $expected6 = [];
        $expected7 = [];
        $expected8 = [];
        $expected9 = [];
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
    $data = Loan::Join("clients", "clients.id", "loans.client_id")
    ->where('loans.status','active')
    ->where('loans.branch_id', 2)
    ->where('clients.gender','female')
    ->selectRaw("loans.principal")
    ->get();
    $totalPrincipal = 0 + $data->sum('principal');
    array_push($expected, round($totalPrincipal, 2));    
$data1 = Loan::Join("clients", "clients.id", "loans.client_id")
->where('loans.status','active')
    ->where('loans.branch_id', 2)
    ->where('clients.gender','male')
    ->selectRaw("loans.principal")
    ->get();
     $totalPrincipal1 = 0 + $data1->sum('principal');
    array_push($expected1, round($totalPrincipal1, 2));
$data2 = Loan::Join("clients", "clients.id", "loans.client_id")
    ->where('loans.status','active')
    ->where('loans.branch_id', 3)
    ->where('gender','female')
    ->selectRaw("loans.principal")
    ->get();
    $totalPrincipal2 = 0 + $data2->sum('principal');
    array_push($expected2, round($totalPrincipal2, 2)); 
$data3 = Loan::Join("clients", "clients.id", "loans.client_id")
    ->where('loans.status','active')
    ->where('loans.branch_id', 3)
    ->where('gender','male')
    ->selectRaw("loans.principal")
    ->get();
    $totalPrincipal3 = 0 + $data3->sum('principal');
    array_push($expected3, round($totalPrincipal3, 2));
$data4 = Loan::Join("clients", "clients.id", "loans.client_id")
    ->where('loans.status','active')
    ->where('loans.branch_id', 4)
    ->where('gender','female')
    ->selectRaw("loans.principal")
    ->get();
    $totalPrincipal4 = 0 + $data4->sum('principal');
    array_push($expected4, round($totalPrincipal4, 2)); 
$data5 = Loan::Join("clients", "clients.id", "loans.client_id")
    ->where('loans.status','active')
    ->where('loans.branch_id', 4)
    ->where('gender','male')
    ->selectRaw("loans.principal")
    ->get();
    $totalPrincipal5 = 0 + $data5->sum('principal');
    array_push($expected5, round($totalPrincipal5, 2));
$data6 = Loan::Join("clients", "clients.id", "loans.client_id")
    ->where('loans.status','active')
    ->where('loans.branch_id', 6)
    ->where('gender','female')
    ->selectRaw("loans.principal")
    ->get();
    $totalPrincipal6 = 0 + $data6->sum('principal');
    array_push($expected6, round($totalPrincipal6, 2)); 
$data7 = Loan::Join("clients", "clients.id", "loans.client_id")
    ->where('loans.status','active')
    ->where('loans.branch_id', 6)
    ->where('gender','male')
    ->selectRaw("loans.principal")
    ->get();
    $totalPrincipal7 = 0 + $data7->sum('principal');
    array_push($expected7, round($totalPrincipal7, 2));
    $data8 = Loan::Join("clients", "clients.id", "loans.client_id")
    ->where('loans.status','active')
    ->where('loans.branch_id', 7)
    ->where('gender','female')
    ->selectRaw("loans.principal")
    ->get();
    $totalPrincipal8 = 0 + $data8->sum('principal');
    array_push($expected8, round($totalPrincipal8, 2)); 
$data9 = Loan::Join("clients", "clients.id", "loans.client_id")
    ->where('loans.status','active')
    ->where('loans.branch_id', 7)
    ->where('gender','male')
    ->selectRaw("loans.principal")
    ->get();
    $totalPrincipal9 = 0 + $data9->sum('principal');
    array_push($expected9, round($totalPrincipal9, 2));  

    $labels = [trans_choice('user::general.AWSD', 1),trans_choice('user::general.BEZA', 1),trans_choice('user::general.Cheshire', 1),trans_choice('user::general.KD', 1),trans_choice('user::general.ECCD', 1)];
        //$chart->labels([trans_choice('user::general.AWSD', 1),trans_choice('user::general.BEZA', 1),trans_choice('user::general.Cheshire', 1),trans_choice('user::general.KD', 1),trans_choice('user::general.ECCD', 1)]);
        $chart->title(trans_choice('core::general.loan_disbursed_amount_per_ngo_per_gender', 1));
        $chart->dataset(trans_choice('user::general.AWSD', 1). ' ' .trans_choice('user::general.female', 1),'bar',array_values($expected))->color('#5E2605');
        $chart->dataset(trans_choice('user::general.AWSD', 1). ' '. trans_choice('user::general.male', 1),'bar',array_values($expected1))->color('#F7A06D');
        $chart->dataset(trans_choice('user::general.BEZA', 1). ' '. trans_choice('user::general.female', 1),'bar',array_values($expected2))->color('#009ACD	');
        $chart->dataset(trans_choice('user::general.BEZA', 1). ' '. trans_choice('user::general.male', 1),'bar',array_values($expected3))->color('#88E1FF');
        $chart->dataset(trans_choice('user::general.Cheshire', 1). ' '. trans_choice('user::general.female', 1),'bar',array_values($expected4))->color('#551A8B');
        $chart->dataset(trans_choice('user::general.Cheshire', 1). ' '.trans_choice('user::general.male', 1),'bar',array_values($expected5))->color('#BF90EA');
        $chart->dataset(trans_choice('user::general.KD', 1). ' '.trans_choice('user::general.female', 1),'bar',array_values($expected6))->color('#FF007F');
        $chart->dataset(trans_choice('user::general.KD', 1). ' '.trans_choice('user::general.male', 1),'bar',array_values($expected7))->color('#FF99CC');
        $chart->dataset(trans_choice('user::general.ECCD', 1). ' '.trans_choice('user::general.female', 1),'bar',array_values($expected8))->color('#E35152');
        $chart->dataset(trans_choice('user::general.ECCD', 1). ' '.trans_choice('user::general.male', 1),'bar',array_values($expected9))->color('#F3B6B7');
        return theme_view('loan::widgets.loan_amount_in_amount_per_ngo_per_gender', [
            'config' => $this->config,
            'chart'=>$chart
        ]);
    }
}