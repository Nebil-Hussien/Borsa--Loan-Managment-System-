<?php
 namespace Modules\Loan\Widgets;

use Arrilot\Widgets\AbstractWidget;
use Modules\Loan\Charts\ClientStatisticsPerCityPerNGOPerGenderBarChart;
use Modules\Client\Entities\Client;

class ClientStatisticsPerCityPerNGOPerGender extends AbstractWidget{
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
        $chart = new ClientStatisticsPerCityPerNGOPerGenderBarChart();
        $labels = [];
        $data = [];
        $data1 = [];
        $data2 = [];
        $data3 = [];
        $data4 = [];
        $data5 = [];
        $data6 = [];
        $data7 = [];
        $data8 = [];
        $data9 = [];
        $colors=[];
          foreach (Client::selectRaw("count(id) count, city_id")->groupBy('city_id')->where('branch_id','2')->where('gender','female')->get()as $key) {
            array_push($data,$key->count);
        }
        foreach (Client::selectRaw("count(id) count, city_id")->groupBy('city_id')->where('branch_id','2')->where('gender','male')->get()as $key) {
           
            array_push($data1,$key->count);
        }
        foreach (Client::selectRaw("count(id) count, city_id")->groupBy('city_id')->where('branch_id','3')->where('gender','female')->get()as $key) {
           
            array_push($data2,$key->count);
        }
         foreach (Client::selectRaw("count(id) count, city_id")->groupBy('city_id')->where('branch_id','3')->where('gender','male')->get()as $key) {
           
            array_push($data3,$key->count);
        }
        foreach (Client::selectRaw("count(id) count, city_id")->groupBy('city_id')->where('branch_id','4')->where('gender','female')->get()as $key) {
           
            array_push($data4,$key->count);
        }
        foreach (Client::selectRaw("count(id) count, city_id")->groupBy('city_id')->where('branch_id','4')->where('gender','male')->get()as $key) {
           
            array_push($data5,$key->count);
        }
        foreach (Client::selectRaw("count(id) count, city_id")->groupBy('city_id')->where('branch_id','7')->where('gender','female')->get()as $key) {
           
            array_push($data6,$key->count);
        }
        foreach (Client::selectRaw("count(id) count, city_id")->groupBy('city_id')->where('branch_id','7')->where('gender','male')->get()as $key) {
           
            array_push($data7,$key->count);
        }
        foreach (Client::selectRaw("count(id) count, city_id")->groupBy('city_id')->where('branch_id','6')->where('gender','female')->get()as $key) {
           
            array_push($data8,$key->count);
        }
        foreach (Client::selectRaw("count(id) count, city_id")->groupBy('city_id')->where('branch_id','6')->where('gender','male')->get()as $key) {
           
            array_push($data9,$key->count);
        }
        $chart->labels([trans_choice('user::general.AA', 1),trans_choice('user::general.AD', 1),trans_choice('user::general.DB', 1),
trans_choice('user::general.DES', 1),trans_choice('user::general.DD', 1),trans_choice('user::general.AST', 1),
trans_choice('user::general.HAR', 1),trans_choice('user::general.AWA', 1),trans_choice('user::general.JIM', 1),
trans_choice('user::general.LOG', 1),trans_choice('user::general.KEM', 1),trans_choice('user::general.KOM', 1),
trans_choice('user::general.SR', 1)]);
$chart->options([
            'inverted' => false,
            'credits' => [
                'enabled' => false,
            ],
            'title' => [
                'text' => "Total Clients Per City",
            ],
        'borderWidth'=> 25,
        'barPercentage' => 5,
      'categoryPercentage' => 10,
      'yAxis' => [
        'min' => 0,
        'minRange' => 100,
        'minPadding' => 0.5,
        'startOnTick' => false,
        'title'=> [
            'text'=> 'Population (millions)',
            'align'=> 'high'
        ],
        'labels'=> [
            'overflow'=> 'justify'
        ]
        ],
    ]);
        $chart->title(trans_choice('core::general.client_statistics_per_city_per_per_ngo_per_gender', 1));
        $chart->dataset(trans_choice('user::general.AWSD', 1). ' ' .trans_choice('user::general.female', 1),'bar',array_values($data))->color('#5E2605');
        $chart->dataset(trans_choice('user::general.AWSD', 1). ' '. trans_choice('user::general.male', 1),'bar',array_values($data1))->color('#F7A06D');
        $chart->dataset(trans_choice('user::general.BEZA', 1). ' '. trans_choice('user::general.female', 1),'bar',array_values($data2))->color('#009ACD	');
        $chart->dataset(trans_choice('user::general.BEZA', 1). ' '. trans_choice('user::general.male', 1),'bar',array_values($data3))->color('#88E1FF');
        $chart->dataset(trans_choice('user::general.Cheshire', 1). ' '. trans_choice('user::general.female', 1),'bar',array_values($data4))->color('#551A8B');
        $chart->dataset(trans_choice('user::general.Cheshire', 1). ' '.trans_choice('user::general.male', 1),'bar',array_values($data5))->color('#BF90EA');
        $chart->dataset(trans_choice('user::general.KD', 1). ' '.trans_choice('user::general.female', 1),'bar',array_values($data6))->color('#FF007F');
        $chart->dataset(trans_choice('user::general.KD', 1). ' '.trans_choice('user::general.male', 1),'bar',array_values($data7))->color('#FF99CC');
        $chart->dataset(trans_choice('user::general.ECCD', 1). ' '.trans_choice('user::general.female', 1),'bar',array_values($data8))->color('#E35152');
        $chart->dataset(trans_choice('user::general.ECCD', 1). ' '.trans_choice('user::general.male', 1),'bar',array_values($data9))->color('#F3B6B7');

        return theme_view('loan::widgets.client_statistics_per_city_per_ngo_per_gender', [
            'config' => $this->config,
            'chart'=>$chart
        ]);
    }
}