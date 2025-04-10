 <?php
 
 namespace Modules\Loan\Widgets;

use Arrilot\Widgets\AbstractWidget;
use Modules\Loan\Charts\LoanAmountInNumberApprovedPerGenderPieChart;
use Modules\Loan\Entities\Loan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LoanAmountInNumberApprovedPerGender extends AbstractWidget{
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
         $chart = new LoanAmountInNumberApprovedPerGenderPieChart();
        $labels = [];
        $data = [];
        $colors=[];
    if(Auth::user()->hasRole('NGO_HeadOffice')){
          foreach (Loan::Join("clients", "clients.id", "loans.client_id")
          ->where('loans.branch_id', Auth::user()->branch_id)
          ->where("loans.status", "approved")
          ->selectRaw("count(client_id) count, gender")
          ->groupBy('gender')
          ->get() as $key) {                
            if ($key->gender == 'female') {
                array_push($labels, trans_choice('core::general.female', 1));
                array_push($colors,'#f54269');
            }
            if ($key->gender == 'male') {
                array_push($labels, trans_choice('core::general.male', 1));
                array_push($colors,'#42b0f5');
            }
            array_push($data,$key->count);
        }
    } elseif(Auth::user()->hasRole('NGO_RegionalManagment'))
    {
         foreach (Loan::Join("clients", "clients.id", "loans.client_id")
          ->where("loans.status", "approved")
        ->where('loans.branch_id', Auth::user()->branch_id)
         ->where('clients.city_id',Auth::user()->city_id)
          ->selectRaw("count(client_id) count, gender")
          ->groupBy('gender')
          ->get() as $key) {                
            if ($key->gender == 'female') {
                array_push($labels, trans_choice('core::general.female', 1));
                array_push($colors,'#f54269');
            }
            if ($key->gender == 'male') {
                array_push($labels, trans_choice('core::general.male', 1));
                array_push($colors,'#42b0f5');
            }
            array_push($data,$key->count);
        }

    }
    elseif(Auth::user()->hasRole('NGO_OperationOfficer'))
    {
         foreach (Loan::Join("clients", "clients.id", "loans.client_id")
          ->where("loans.status", "approved")
          ->where('clients.loan_officer_id', Auth::user()->id)
          ->selectRaw("count(client_id) count, gender")
          ->groupBy('gender')
          ->get() as $key) {                
            if ($key->gender == 'female') {
                array_push($labels, trans_choice('core::general.female', 1));
                array_push($colors,'#f54269');
            }
            if ($key->gender == 'male') {
                array_push($labels, trans_choice('core::general.male', 1));
                array_push($colors,'#42b0f5');
            }
            array_push($data,$key->count);
        }
    }else{
         foreach (Loan::Join("clients", "clients.id", "loans.client_id")
          ->where("loans.status", "approved")
          ->selectRaw("count(client_id) count, gender")
          ->groupBy('gender')
          ->get() as $key) {                
            if ($key->gender == 'female') {
                array_push($labels, trans_choice('core::general.female', 1));
                array_push($colors,'#f54269');
            }
            if ($key->gender == 'male') {
                array_push($labels, trans_choice('core::general.male', 1));
                array_push($colors,'#42b0f5');
            }
            array_push($data,$key->count);
        }
    }
        $chart->labels(array_values($labels));
        $chart->title(trans_choice('core::general.loan_approved_number_per_gender', 1));
        $chart->dataset("Number of Loan Approved Per Gender",'pie',array_values($data))->color(array_values($colors));
        return theme_view('loan::widgets.loan_amount_in_number_approved_per_gender', [
            'config' => $this->config,
            'chart'=>$chart
        ]);
    }
    }