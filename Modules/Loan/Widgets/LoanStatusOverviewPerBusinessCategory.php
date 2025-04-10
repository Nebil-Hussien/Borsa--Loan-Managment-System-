<?php
namespace Modules\Loan\Widgets;


use Arrilot\Widgets\AbstractWidget;
use Modules\Loan\Charts\LoanStatusOverviewPerBusinessCategoryPieChart;
use Modules\Loan\Entities\Loan;
use Illuminate\Support\Facades\Auth;

class LoanStatusOverviewPerBusinessCategory extends AbstractWidget{
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
        $chart = new LoanStatusOverviewPerBusinessCategoryPieChart();
        $labels = [];
        $data = [];
        $colors=[];
    if(Auth::user()->hasRole('NGO_HeadOffice')){

          foreach (Loan::Join("clients", "clients.id", "loans.client_id")
          ->Join("kyc","kyc.client_id", "clients.id")
            ->where('loans.branch_id', Auth::user()->branch_id)
            ->where('loans.status','active')
            ->selectRaw("count(loans.id) count, kyc.bussiness_sector_id")
            ->groupBy('bussiness_sector_id')
            ->get() as $key) {
            if ($key->bussiness_sector_id == '1') {
                array_push($labels, trans_choice('loan::general.bussiness_sector_chicken_farming', 1));
                array_push($colors,'#faa732');
            }
            if ($key->bussiness_sector_id == '2') {
                array_push($labels, trans_choice('loan::general.bussiness_sector_animal_farming', 1));
                array_push($colors,'#0088cc');
            }
            if ($key->bussiness_sector_id == '3') {
                array_push($labels, trans_choice('loan::general.bussiness_sector_diary_product', 1));
                array_push($colors,'#5bb75b');
            }
            if ($key->bussiness_sector_id == '4') {
                array_push($labels, trans_choice('loan::general.bussiness_sector_merchandise', 1));
                array_push($colors,'#ff0000');
            }
            if ($key->bussiness_sector_id == '5') {
                array_push($labels, trans_choice('loan::general.bussiness_sector_woodwork_metal', 1));
                array_push($colors,'#00FFFF');
            }
            if ($key->bussiness_sector_id == '6') {
                array_push($labels, trans_choice('loan::general.bussiness_sector_garment', 1));
                array_push($colors,'#8A2BE2');
            }
            if ($key->bussiness_sector_id == '7') {
                array_push($labels, trans_choice('loan::general.bussiness_sector_petty_trade', 1));
                array_push($colors,'#8B008B');
            }
            if ($key->bussiness_sector_id == '8') {
                array_push($labels, trans_choice('loan::general.bussiness_sector_other', 1));
                array_push($colors,'#4B0082');
            }
            array_push($data,$key->count);
        }
    }elseif(Auth::user()->hasRole('NGO_RegionalManagment'))
    {
         foreach (Loan::Join("clients", "clients.id", "loans.client_id")
          ->Join("kyc","kyc.client_id", "clients.id")
            ->where('loans.branch_id', Auth::user()->branch_id)
            ->where('clients.city_id',Auth::user()->city_id)
            ->where('loans.status','active')
            ->selectRaw("count(loans.id) count, kyc.bussiness_sector_id")
            ->groupBy('bussiness_sector_id')
            ->get() as $key) {
            if ($key->bussiness_sector_id == '1') {
                array_push($labels, trans_choice('loan::general.bussiness_sector_chicken_farming', 1));
                array_push($colors,'#faa732');
            }
            if ($key->bussiness_sector_id == '2') {
                array_push($labels, trans_choice('loan::general.bussiness_sector_animal_farming', 1));
                array_push($colors,'#0088cc');
            }
            if ($key->bussiness_sector_id == '3') {
                array_push($labels, trans_choice('loan::general.bussiness_sector_diary_product', 1));
                array_push($colors,'#5bb75b');
            }
            if ($key->bussiness_sector_id == '4') {
                array_push($labels, trans_choice('loan::general.bussiness_sector_merchandise', 1));
                array_push($colors,'#ff0000');
            }
            if ($key->bussiness_sector_id == '5') {
                array_push($labels, trans_choice('loan::general.bussiness_sector_woodwork_metal', 1));
                array_push($colors,'#00FFFF');
            }
            if ($key->bussiness_sector_id == '6') {
                array_push($labels, trans_choice('loan::general.bussiness_sector_garment', 1));
                array_push($colors,'#8A2BE2');
            }
            if ($key->bussiness_sector_id == '7') {
                array_push($labels, trans_choice('loan::general.bussiness_sector_petty_trade', 1));
                array_push($colors,'#8B008B');
            }
            if ($key->bussiness_sector_id == '8') {
                array_push($labels, trans_choice('loan::general.bussiness_sector_other', 1));
                array_push($colors,'#4B0082');
            }
            array_push($data,$key->count);
        }

    } elseif(Auth::user()->hasRole('NGO_OperationOfficer'))
    {
         foreach (Loan::Join("clients", "clients.id", "loans.client_id")
            ->Join("kyc","kyc.client_id", "clients.id")
            ->where('loans.loan_officer_id', Auth::user()->id)
            ->where('loans.status','active')
            ->selectRaw("count(loans.id) count, kyc.bussiness_sector_id")
            ->groupBy('bussiness_sector_id')
            ->get() as $key) {
            if ($key->bussiness_sector_id == '1') {
                array_push($labels, trans_choice('loan::general.bussiness_sector_chicken_farming', 1));
                array_push($colors,'#faa732');
            }
            if ($key->bussiness_sector_id == '2') {
                array_push($labels, trans_choice('loan::general.bussiness_sector_animal_farming', 1));
                array_push($colors,'#0088cc');
            }
            if ($key->bussiness_sector_id == '3') {
                array_push($labels, trans_choice('loan::general.bussiness_sector_diary_product', 1));
                array_push($colors,'#5bb75b');
            }
            if ($key->bussiness_sector_id == '4') {
                array_push($labels, trans_choice('loan::general.bussiness_sector_merchandise', 1));
                array_push($colors,'#ff0000');
            }
            if ($key->bussiness_sector_id == '5') {
                array_push($labels, trans_choice('loan::general.bussiness_sector_woodwork_metal', 1));
                array_push($colors,'#00FFFF');
            }
            if ($key->bussiness_sector_id == '6') {
                array_push($labels, trans_choice('loan::general.bussiness_sector_garment', 1));
                array_push($colors,'#8A2BE2');
            }
            if ($key->bussiness_sector_id == '7') {
                array_push($labels, trans_choice('loan::general.bussiness_sector_petty_trade', 1));
                array_push($colors,'#8B008B');
            }
            if ($key->bussiness_sector_id == '8') {
                array_push($labels, trans_choice('loan::general.bussiness_sector_other', 1));
                array_push($colors,'#4B0082');
            }
         
    }
}   
    else{
         foreach (Loan::Join("clients", "clients.id", "loans.client_id")
          ->Join("kyc","kyc.client_id", "clients.id")
          ->where('loans.status','active')
          ->selectRaw("count(loans.id) count, kyc.bussiness_sector_id")
          ->groupBy('bussiness_sector_id')
          ->get() as $key) {
            if ($key->bussiness_sector_id == '1') {
                array_push($labels, trans_choice('loan::general.bussiness_sector_chicken_farming', 1));
                array_push($colors,'#faa732');
            }
            if ($key->bussiness_sector_id == '2') {
                array_push($labels, trans_choice('loan::general.bussiness_sector_animal_farming', 1));
                array_push($colors,'#0088cc');
            }
            if ($key->bussiness_sector_id == '3') {
                array_push($labels, trans_choice('loan::general.bussiness_sector_diary_product', 1));
                array_push($colors,'#5bb75b');
            }
            if ($key->bussiness_sector_id == '4') {
                array_push($labels, trans_choice('loan::general.bussiness_sector_merchandise', 1));
                array_push($colors,'#ff0000');
            }
            if ($key->bussiness_sector_id == '5') {
                array_push($labels, trans_choice('loan::general.bussiness_sector_woodwork_metal', 1));
                array_push($colors,'#00FFFF');
            }
            if ($key->bussiness_sector_id == '6') {
                array_push($labels, trans_choice('loan::general.bussiness_sector_garment', 1));
                array_push($colors,'#8A2BE2');
            }
            if ($key->bussiness_sector_id == '7') {
                array_push($labels, trans_choice('loan::general.bussiness_sector_petty_trade', 1));
                array_push($colors,'#8B008B');
            }
            if ($key->bussiness_sector_id == '8') {
                array_push($labels, trans_choice('loan::general.bussiness_sector_other', 1));
                array_push($colors,'#4B0082');
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
      $chart->labels(array_values($labels));
      $chart->title(trans_choice('core::general.loan_disbursed_per_bussiness_sector', 1));
        $chart->dataset(trans_choice('core::general.loan_disbursed_per_bussiness_sector', 1),'pie',array_values($data))->color(array_values($colors));
        return theme_view('loan::widgets.loan_status_overview_per_business_category', [
            'config' => $this->config,
            'chart'=>$chart
        ]);
    }
}