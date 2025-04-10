<?php
 namespace Modules\Loan\Widgets;

use Arrilot\Widgets\AbstractWidget;
use Modules\Loan\Charts\ClientStatisticsPerNGOBarChart;
use Modules\Client\Entities\Client;

class ClientStatisticsPerNGO extends AbstractWidget{
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
        $chart = new ClientStatisticsPerNGOBarChart();
        $labels = [];
        $data = [];
        $colors=['#f0e337','#faa732','#0088cc','#32a852','#ff0000','#782db3'];
        $key = 0;
          foreach (Client::selectRaw("count(id) as count,branch_id")
          ->where('status','active')
          ->groupBy('branch_id')
          ->get() as $key)
          {          
            array_push($data,$key->count);    
        }
         $chart->options([
             'plotOptions' => [
                'pie' => [
                    'size'=> 200
                ],
                ]
            ]);
        // return implode($data);
$chart->labels([trans_choice('core::general.null',1),trans_choice('user::general.AWSD', 1),trans_choice('user::general.BEZA', 1),trans_choice('user::general.Cheshire', 1),trans_choice('user::general.KD', 1),trans_choice('user::general.ECCD', 1)]);
        $chart->title(trans_choice('core::general.Client_stattistics_per_ngo', 1));
        $chart->dataset("Client Statistics Per NGO",'pie',array_values($data))->color(array_values($colors));
        return theme_view('loan::widgets.client_statistics_per_ngo', [
            'config' => $this->config,
            'chart'=>$chart
        ]);
    }
}