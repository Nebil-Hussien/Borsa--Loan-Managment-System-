<?php
 namespace Modules\Loan\Widgets;

use Arrilot\Widgets\AbstractWidget;
use Modules\Loan\Charts\ClientStatisticsPerCityLineChart;
use Modules\Client\Entities\Client;
use Illuminate\Support\Facades\Auth;

class ClientStatisticsPerCity extends AbstractWidget{
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

        $chart = new ClientStatisticsPerCityLineChart();
        $labels = [];
        $data = [];
        $colors=['#800000','#9A6324','#808000','#469990','#000075','#f58231','#ffe119','#42d4f4',
        '#911eb4','#fffac8','#ffd8b1','#f032e6', '#e6194B','#ffe119'];
    if(Auth::user()->hasRole('NGO_HeadOffice')){
            foreach (Client::selectRaw("count(id) as count, city_id")
         ->where('clients.branch_id', Auth::user()->branch_id)
          ->groupBy('city_id')
          ->get() as $key) {         
            array_push($data,$key->count);

        }
    }elseif(Auth::user()->hasRole('NGO_RegionalManagment'))
    {
        foreach (Client::selectRaw("count(id) as count, city_id")
         ->where('clients.branch_id', Auth::user()->branch_id)
         ->where('clients.city_id',Auth::user()->city_id)
          ->groupBy('city_id')
          ->get() as $key) {         
            array_push($data,$key->count);
          }

    }
    elseif(Auth::user()->hasRole('NGO_OperationOfficer'))
    {
        foreach (Client::selectRaw("count(id) as count, city_id")
         ->where('clients.loan_officer_id', Auth::user()->id)
          ->groupBy('city_id')
          ->get() as $key) 
          {         
            array_push($data,$key->count);
          }
    }
    else{
          foreach (Client::selectRaw("count(id) as count, city_id")
          ->groupBy('city_id')
          ->get() as $key) 
        {         
            array_push($data,$key->count);
        }
    }
        // return (implode($data));
        // //return "Done";
  $chart->options([
             'plotOptions' => [
                'pie' => [
                    'size'=> 180
                ],
                ]
            ]);
$chart->title(trans_choice('core::general.Client_stattistics_per_city', 1));
$chart->labels([trans_choice('core::general.null', 1),trans_choice('user::general.AA', 1),trans_choice('user::general.AD', 1),trans_choice('user::general.DB', 1),
trans_choice('user::general.DES', 1),trans_choice('user::general.DD', 1),trans_choice('user::general.AST', 1),
trans_choice('user::general.HAR', 1),trans_choice('user::general.AWA', 1),trans_choice('user::general.JIM', 1),
trans_choice('user::general.LOG', 1),trans_choice('user::general.KEM', 1),trans_choice('user::general.KOM', 1),
trans_choice('user::general.SR', 1)]);
        $chart->dataset("Client Statistics Per City",'pie',array_values($data))->color(array_values($colors));
        return theme_view('loan::widgets.client_statistics_per_city', [
            'config' => $this->config,
            'chart'=>$chart
        ]);
    }
}