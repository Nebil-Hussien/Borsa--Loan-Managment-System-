<?php
 namespace Modules\Loan\Widgets;

use Arrilot\Widgets\AbstractWidget;
use Modules\Loan\Charts\ClientStatisticsPerGenderPieChart;
use Modules\Client\Entities\Client;
use Illuminate\Support\Facades\Auth;

class ClientStatisticsPerGender extends AbstractWidget{
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
        $chart = new ClientStatisticsPerGenderPieChart();
        $labels = [];
        $data = [];
        $colors=['yellow','#f54269','#42b0f5'];
if(Auth::user()->hasRole('NGO_HeadOffice')){
          foreach (Client::selectRaw("count(id) count, gender")
          ->where('clients.branch_id', Auth::user()->branch_id)
          ->groupBy('gender')
          ->get()as $key) { 
            
            array_push($data,$key->count);
        }
    }elseif(Auth::user()->hasRole('NGO_RegionalManagment'))
    {
         foreach (Client::selectRaw("count(id) count, gender")
         ->where('clients.branch_id', Auth::user()->branch_id)
         ->where('clients.city_id',Auth::user()->city_id)
          ->groupBy('gender')
          ->get()as $key) { 
            
            array_push($data,$key->count);
    }
} elseif(Auth::user()->hasRole('NGO_OperationOfficer'))
    {
        foreach (Client::selectRaw("count(id) count, gender")
        ->where('clients.loan_officer_id', Auth::user()->id)
          ->groupBy('gender')
          ->get()as $key) { 
            
            array_push($data,$key->count);
    }
} else
{
    foreach (Client::selectRaw("count(id) count, gender")
          ->groupBy('gender')
          ->get()as $key) { 
            
            array_push($data,$key->count);
    }
}
        //return implode($data);
        $chart->labels([trans_choice('core::general.female', 1),trans_choice('core::general.male', 1),trans_choice('core::general.null',1),]);
        $chart->title(trans_choice('core::general.Client_stattistics_per_gender', 1));
        $chart->dataset("Client Statistics Per NGO",'pie',array_values($data))->color(array_values($colors));
        return theme_view('loan::widgets.client_statistics_per_gender', [
            'config' => $this->config,
            'chart'=>$chart
        ]);
    }
}