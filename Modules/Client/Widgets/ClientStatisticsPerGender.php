<?php
 namespace Modules\Client\Widgets;

use Arrilot\Widgets\AbstractWidget;
use Modules\Loan\Charts\ClientStatisticsPerGenderPieChart;
use Modules\Client\Entities\Client;

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
        $colors=[];
          foreach (Client::selectRaw("count(id) count,gender")->groupBy('gender')->get()as $key) {
            if ($key->gender == 'female') {
                array_push($labels, trans_choice('user::general.female', 1));
                array_push($colors,'#faa732');
            }
            if ($key->gender == 'male') {
                array_push($labels, trans_choice('user::general.male', 1));
                array_push($colors,'#0088cc');
            }
           
            array_push($data,$key->count);
        }
        $chart->labels(array_values($labels));
        $chart->type('doughnut');
        $chart->dataset("Client Statistics Per Gender",array_values($data))->color(array_values($colors));
        return theme_view('client::widgets.clientStatistics', [
            'config' => $this->config,
            'chart'=>$chart
        ]);
    }
}