<?php

namespace Modules\Loan\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laracasts\Flash\Flash;
use Modules\Accounting\Entities\JournalEntry;
use Modules\Client\Entities\Client;
use Modules\Core\Entities\PaymentDetail;
use Modules\Core\Entities\PaymentType;
use Modules\CustomField\Entities\CustomField;
use Modules\Loan\Entities\Fund;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Entities\LoanApplication;
use Modules\Loan\Entities\LoanCharge;
use Modules\Loan\Entities\LoanHistory;
use Modules\Loan\Entities\LoanLinkedCharge;
use Modules\Loan\Entities\LoanOfficerHistory;
use Modules\Loan\Entities\LoanProduct;
use Modules\Loan\Entities\LoanPurpose;
use Modules\Loan\Entities\LoanRepaymentSchedule;
use Modules\Loan\Entities\LoanTransaction;
use Modules\Loan\Entities\KYC;
use Modules\Loan\Entities\BussinessPlan;
use Modules\Loan\Entities\SourceCaptial;
use Modules\Loan\Entities\FinancialPlan;
use Modules\Loan\Entities\Others;
use Modules\Loan\Events\LoanStatusChanged;
use Modules\Loan\Events\TransactionUpdated;
use Modules\User\Entities\User;
use JavaScript;
use PDF;
use Yajra\DataTables\Facades\DataTables;

class LoanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', '2fa']);
        $this->middleware(['permission:loan.loans.index'])->only(['index', 'get_loans', 'show', 'show_application', 'get_applications']);
        $this->middleware(['permission:loan.loans.create'])->only(['create', 'create_client_loan', 'store_client_loan', 'store']);
        $this->middleware(['permission:loan.loans.edit'])->only(['edit', 'edit_client_loan', 'update', 'update_client_loan', 'change_loan_officer']);
        $this->middleware(['permission:loan.loans.destroy'])->only(['destroy']);
        $this->middleware(['permission:loan.loans.approve_loan'])->only(['approve_loan', 'undo_approval', 'reject_loan', 'undo_rejection', 'approve_application', 'store_approve_application']);
        $this->middleware(['permission:loan.loans.disburse_loan'])->only(['disburse_loan', 'undo_disbursement']);
        $this->middleware(['permission:loan.loans.withdraw_loan'])->only(['withdraw_loan', 'undo_withdrawn']);
        $this->middleware(['permission:loan.loans.write_off_loan'])->only(['write_off_loan', 'undo_write_off']);
        $this->middleware(['permission:loan.loans.reschedule_loan'])->only(['reschedule_loan']);
        $this->middleware(['permission:loan.loans.close_loan'])->only(['close_loan', 'undo_close']);
        $this->middleware(['permission:loan.loans.calculator'])->only(['calculator']);
        $this->middleware(['permission:loan.loans.transactions.create'])->only(['create_repayment', 'store_repayment', 'create_loan_linked_charge', 'store_loan_linked_charge']);
        $this->middleware(['permission:loan.loans.transactions.edit'])->only(['edit_repayment', 'reverse_repayment', 'update_repayment', 'waive_interest', 'waive_charge']);
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $perPage = $request->per_page ?: 20;
        $orderBy = $request->order_by;
        $orderByDir = $request->order_by_dir;
        $search = $request->s;
        $status = $request->status;
        $client_id = $request->client_id;
        $authUser = Auth::user();
        $request_forRestLoan_officer = $request->loan_officer_id;
        $loan_officer_id = determin_the_role_forLoanView_loan_officer($authUser, $request_forRestLoan_officer);
        $request_forRestBranch = $request->branch_id;
        $branch_id = determin_the_role_forLoanView_branch($authUser, $request_forRestBranch);
        $request_forRestCity = $request->city_id;
        $city_id = determin_the_role_forLoanView_city($authUser, $request_forRestCity);
        $data = Loan::leftJoin("clients", "clients.id", "loans.client_id")
            ->leftJoin("loan_repayment_schedules", "loan_repayment_schedules.loan_id", "loans.id")
            ->leftJoin("loan_products", "loan_products.id", "loans.loan_product_id")
            ->leftJoin("branches", "branches.id", "loans.branch_id")
            ->leftJoin("users", "users.id", "loans.loan_officer_id")
            ->when($client_id, function ($query) use ($client_id) {
                $query->where("loans.client_id", $client_id);
            })
            ->when($city_id, function ($query) use ($city_id) {
                $query->where("clients.city_id", $city_id);
            })
            ->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                $query->where("loans.loan_officer_id", $loan_officer_id);
            })
            ->when($branch_id, function ($query) use ($branch_id) {
                $query->where("loans.branch_id", $branch_id);
            })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('loan_products.name', 'like', "%$search%");
                $query->orWhere('clients.first_name', 'like', "%$search%");
                $query->orWhere('clients.middle_name', 'like', "%$search%");
                $query->orWhere('clients.last_name', 'like', "%$search%");
                $query->orWhere('clients.company_name', 'like', "%$search%");
                $query->orWhere('loans.id', 'like', "%$search%");
                $query->orWhere('loans.account_number', 'like', "%$search%");
                $query->orWhere('loans.external_id', 'like', "%$search%");
            })
            ->when($status, function ($query) use ($status) {
                $query->where("loans.status", $status);
            })
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->selectRaw("concat(clients.first_name,' ',clients.middle_name) client, (clients.company_name) company_name ,(clients.city_id) city ,concat(clients.mobile, '') clientMobile, concat(users.first_name,' ',users.last_name) loan_officer,loans.id,loans.client_id,loans.applied_amount,loans.principal,loans.disbursed_on_date,loans.expected_maturity_date,loan_products.name loan_product,loans.status,loans.decimals,branches.name branch, SUM(loan_repayment_schedules.principal) total_principal, SUM(loan_repayment_schedules.principal_written_off_derived) principal_written_off_derived, SUM(loan_repayment_schedules.principal_repaid_derived) principal_repaid_derived, SUM(loan_repayment_schedules.interest) total_interest, SUM(loan_repayment_schedules.interest_waived_derived) interest_waived_derived,SUM(loan_repayment_schedules.interest_written_off_derived) interest_written_off_derived,  SUM(loan_repayment_schedules.interest_repaid_derived) interest_repaid_derived,SUM(loan_repayment_schedules.fees) total_fees, SUM(loan_repayment_schedules.fees_waived_derived) fees_waived_derived, SUM(loan_repayment_schedules.fees_written_off_derived) fees_written_off_derived, SUM(loan_repayment_schedules.fees_repaid_derived) fees_repaid_derived,SUM(loan_repayment_schedules.penalties) total_penalties, SUM(loan_repayment_schedules.penalties_waived_derived) penalties_waived_derived, SUM(loan_repayment_schedules.penalties_written_off_derived) penalties_written_off_derived, SUM(loan_repayment_schedules.penalties_repaid_derived) penalties_repaid_derived")
            ->groupBy("loans.id")
            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('loan::loan.index', compact('data'));
    }

    public function application(Request $request)
    {
        $perPage = $request->per_page ?: 20;
        $orderBy = $request->order_by;
        $orderByDir = $request->order_by_dir;
        $search = $request->s;
        $status = $request->status;
        $client_id = $request->client_id;
        $authUser = Auth::user();
        $request_forRestLoan_officer = $request->loan_officer_id;
        $loan_officer_id = determin_the_role_forLoanView_loan_officer($authUser, $request_forRestLoan_officer);
        $request_forRestBranch = $request->branch_id;
        $branch_id = determin_the_role_forLoanView_branch($authUser, $request_forRestBranch);
        $request_forRestCity = $request->city_id;
        $city_id = determin_the_role_forLoanView_city($authUser, $request_forRestCity);
        $data = LoanApplication::leftJoin("clients", "clients.id", "loan_applications.client_id")
            ->leftJoin("loan_products", "loan_products.id", "loan_applications.loan_product_id")
            ->leftJoin("branches", "branches.id", "loan_applications.branch_id")
            ->leftJoin("users", "users.id", "loan_applications.created_by_id")
            ->when($status, function ($query) use ($status) {
                $query->where("loan_applications.status", $status);
            })->when($client_id, function ($query) use ($client_id) {
                $query->where("loan_applications.client_id", $client_id);
            })->when($city_id, function ($query) use ($city_id) {
                $query->where("clients.city_id", $city_id);
            })->when($branch_id, function ($query) use ($branch_id) {
                $query->where("loan_applications.branch_id", $branch_id);
            })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('loan_products.name', 'like', "%$search%");
                $query->orWhere('clients.first_name', 'like', "%$search%");
                $query->orWhere('clients.middle_name', 'like', "%$search%");
                $query->orWhere('loan_applications.id', 'like', "%$search%");
            })
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            })
            ->selectRaw("concat(clients.first_name,' ',clients.middle_name) client,concat(users.first_name,' ',users.last_name) created_by,loan_applications.id,loan_applications.client_id,loan_products.name loan_product,loan_applications.status,loan_applications.loan_id,branches.name branch,loan_applications.amount,loan_applications.created_at")
            ->groupBy("loan_applications.id")
            ->paginate($perPage)
            ->appends($request->input());
        return theme_view('loan::application.index', compact('data'));
    }

    public function get_loans(Request $request)
    {

        $status = $request->status;
        $client_id = $request->client_id;
        $loan_officer_id = $request->loan_officer_id;
        $branch_id = $request->branch_id;
        $query = DB::table("loans")
            ->leftJoin("clients", "clients.id", "loans.client_id")
            ->leftJoin("loan_repayment_schedules", "loan_repayment_schedules.loan_id", "loans.id")
            ->leftJoin("loan_products", "loan_products.id", "loans.loan_product_id")
            ->leftJoin("branches", "branches.id", "loans.branch_id")
            ->leftJoin("users", "users.id", "loans.loan_officer_id")
            ->selectRaw("concat(clients.first_name,' ',clients.middle_name) client,concat(users.first_name,' ',users.last_name) loan_officer,loans.id,loans.client_id,loans.applied_amount,loans.principal,loans.disbursed_on_date,loans.expected_maturity_date,loan_products.name loan_product,loans.status,loans.decimals,branches.name branch, SUM(loan_repayment_schedules.principal) total_principal, SUM(loan_repayment_schedules.principal_written_off_derived) principal_written_off_derived, SUM(loan_repayment_schedules.principal_repaid_derived) principal_repaid_derived, SUM(loan_repayment_schedules.interest) total_interest, SUM(loan_repayment_schedules.interest_waived_derived) interest_waived_derived,SUM(loan_repayment_schedules.interest_written_off_derived) interest_written_off_derived,  SUM(loan_repayment_schedules.interest_repaid_derived) interest_repaid_derived,SUM(loan_repayment_schedules.fees) total_fees, SUM(loan_repayment_schedules.fees_waived_derived) fees_waived_derived, SUM(loan_repayment_schedules.fees_written_off_derived) fees_written_off_derived, SUM(loan_repayment_schedules.fees_repaid_derived) fees_repaid_derived,SUM(loan_repayment_schedules.penalties) total_penalties, SUM(loan_repayment_schedules.penalties_waived_derived) penalties_waived_derived, SUM(loan_repayment_schedules.penalties_written_off_derived) penalties_written_off_derived, SUM(loan_repayment_schedules.penalties_repaid_derived) penalties_repaid_derived")->when($status, function ($query) use ($status) {
                $query->where("loans.status", $status);
            })->when($client_id, function ($query) use ($client_id) {
                $query->where("loans.client_id", $client_id);
            })->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                $query->where("loans.loan_officer_id", $loan_officer_id);
            })->when($branch_id, function ($query) use ($branch_id) {
                $query->where("loans.branch_id", $branch_id);
            })->groupBy("loans.id");
        return DataTables::of($query)->editColumn('client', function ($data) {
            return '<a href="' . url('client/' . $data->client_id . '/show') . '">' . $data->client . '</a>';
        })->editColumn('principal', function ($data) {
            return number_format($data->principal, 2);
        })->editColumn('total_principal', function ($data) {
            return number_format($data->total_principal, 2);
        })->editColumn('total_interest', function ($data) {
            return number_format($data->total_interest, 2);
        })->editColumn('total_fees', function ($data) {
            return number_format($data->total_fees, 2);
        })->editColumn('total_penalties', function ($data) {
            return number_format($data->total_penalties, 2);
        })->editColumn('due', function ($data) {
            return number_format($data->total_principal + $data->total_interest + $data->total_fees + $data->total_penalties, 2);
        })->editColumn('balance', function ($data) {
            return number_format(($data->total_principal - $data->principal_repaid_derived - $data->principal_written_off_derived) + ($data->total_interest - $data->interest_repaid_derived - $data->interest_written_off_derived - $data->interest_waived_derived) + ($data->total_fees - $data->fees_repaid_derived - $data->fees_written_off_derived - $data->fees_waived_derived) + ($data->total_penalties - $data->penalties_repaid_derived - $data->penalties_written_off_derived - $data->penalties_waived_derived), 2);
        })->editColumn('status', function ($data) {
            if ($data->status == 'pending') {
                return '<span class="label label-warning">' . trans_choice('loan::general.pending', 1) . ' ' . trans_choice('general.approval', 1) . '</span>';
            }
            if ($data->status == 'submitted') {
                return '<span class="label label-warning">' . trans_choice('loan::general.pending_approval', 1) . '</span>';
            }
            if ($data->status == 'overpaid') {
                return '<span class="label label-warning">' . trans_choice('loan::general.overpaid', 1) . '</span>';
            }
            if ($data->status == 'approved') {
                return '<span class="label label-warning">' . trans_choice('loan::general.awaiting_disbursement', 1) . '</span>';
            }
            if ($data->status == 'active') {
                return '<span class="label label-info">' . trans_choice('loan::general.active', 1) . '</span>';
            }
            if ($data->status == 'rejected') {
                return '<span class="label label-danger">' . trans_choice('loan::general.rejected', 1) . '</span>';
            }
            if ($data->status == 'withdrawn') {
                return '<span class="label label-danger">' . trans_choice('loan::general.withdrawn', 1) . '</span>';
            }
            if ($data->status == 'written_off') {
                return '<span class="label label-danger">' . trans_choice('loan::general.written_off', 1) . '</span>';
            }
            if ($data->status == 'closed') {
                return '<span class="label label-success">' . trans_choice('loan::general.closed', 1) . '</span>';
            }
            if ($data->status == 'pending_reschedule') {
                return '<span class="label label-warning">' . trans_choice('loan::general.pending_reschedule', 1) . '</span>';
            }
            if ($data->status == 'rescheduled') {
                return '<span class="label label-info">' . trans_choice('loan::general.rescheduled', 1) . '</span>';
            }
        })->editColumn('action', function ($data) {

            $action = '<a href="' . url('loan/' . $data->id . '/show') . '" class="btn btn-info">' . trans_choice('general.detail', 2) . '</a>';

            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('loan/' . $data->id . '/show') . '" class="">' . $data->id . '</a>';
        })->rawColumns(['id', 'client', 'action', 'status'])->make(true);
    }

    public function get_applications(Request $request)
    {

        $status = $request->status;
        $client_id = $request->client_id;
        $branch_id = $request->branch_id;
        $query = DB::table("loan_applications")
            ->leftJoin("clients", "clients.id", "loan_applications.client_id")
            ->leftJoin("loan_products", "loan_products.id", "loan_applications.loan_product_id")
            ->leftJoin("branches", "branches.id", "loan_applications.branch_id")
            ->leftJoin("users", "users.id", "loan_applications.created_by_id")
            ->selectRaw("concat(clients.first_name,' ',clients.middle_name) client,concat(users.first_name,' ',users.last_name) created_by,loan_applications.id,loan_applications.client_id,loan_products.name loan_product,loan_applications.status,loan_applications.loan_id,branches.name branch,loan_applications.amount,loan_applications.created_at")
            ->when($status, function ($query) use ($status) {
                $query->where("loan_applications.status", $status);
            })->when($client_id, function ($query) use ($client_id) {
                $query->where("loan_applications.client_id", $client_id);
            })->when($branch_id, function ($query) use ($branch_id) {
                $query->where("loan_applications.branch_id", $branch_id);
            });
        return DataTables::of($query)->editColumn('client', function ($data) {
            return '<a href="' . url('client/' . $data->client_id . '/show') . '">' . $data->client . '</a>';
        })->editColumn('amount', function ($data) {
            return number_format($data->amount, 2);
        })->editColumn('status', function ($data) {
            if ($data->status == 'pending') {
                return '<span class="label label-warning">' . trans_choice('loan::general.pending', 1) . ' ' . trans_choice('general.approval', 1) . '</span>';
            }
            if ($data->status == 'submitted') {
                return '<span class="label label-warning">' . trans_choice('loan::general.pending_approval', 1) . '</span>';
            }
            if ($data->status == 'overpaid') {
                return '<span class="label label-warning">' . trans_choice('loan::general.overpaid', 1) . '</span>';
            }
            if ($data->status == 'approved') {
                return '<span class="label label-success">' . trans_choice('loan::general.approved', 1) . '</span>';
            }
            if ($data->status == 'active') {
                return '<span class="label label-info">' . trans_choice('loan::general.active', 1) . '</span>';
            }
            if ($data->status == 'rejected') {
                return '<span class="label label-danger">' . trans_choice('loan::general.rejected', 1) . '</span>';
            }
            if ($data->status == 'withdrawn') {
                return '<span class="label label-danger">' . trans_choice('loan::general.withdrawn', 1) . '</span>';
            }
        })->editColumn('action', function ($data) {

            $action = '<a href="' . url('loan/application/' . $data->id . '/show') . '" class="btn btn-info">' . trans_choice('general.detail', 2) . '</a>';

            return $action;
        })->editColumn('id', function ($data) {
            return '<a href="' . url('loan/application/' . $data->id . '/show') . '" class="">' . $data->id . '</a>';
        })->rawColumns(['id', 'client', 'action', 'status'])->make(true);
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request)
    {
        
        $status = 'active';
        $authUser = Auth::user();
        $request_forRestBranch = $request->branch_id;
        //$branch_id = $request->branch_id;
        $branch_id = determin_the_role_forLoanView_branch($authUser, $request_forRestBranch);   
        if(Auth::user()->hasRole('NGO_HeadOffice') || Auth::user()->hasRole('NGO_OperationOfficer') || Auth::user()->hasRole('NGO_RegionalManagment'))
          {
            $clients = Client::where('status', 'active')
            ->where("clients.branch_id", $branch_id)
            ->get();
              $users =  User::whereHas('roles', function ($query) {
            return $query->where('name', '!=', 'client');
        })
        ->where("users.branch_id", $branch_id )
        ->get();
          
    }else{

            $clients = Client::where('status', 'active')->get();
            $users =  User::whereHas('roles', function ($query) {
            return $query->where('name', '!=', 'client');
        })
        ->get();
          }

        $loan_products = LoanProduct::with('charges')->with('charges.charge')->where('active', 1)->get();
        $funds = Fund::all();
        $loan_purposes = LoanPurpose::get();

      
        $custom_fields = CustomField::where('category', 'add_loan')->where('active', 1)->get();
        JavaScript::put([
            'clients' => $clients,
            'loan_products' => $loan_products,
            'loan_charges' => LoanCharge::get(),
            'funds' => $funds,
            'loan_purposes' => $loan_purposes,
            'users' => $users

        ]);
        return theme_view('loan::loan.create', compact('clients', 'loan_products', 'funds', 'loan_purposes', 'users', 'custom_fields'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    /*   function add_data(Request $request)
    {
        if ($request->ajax()) {
            $data = array(
                'description'      =>  $request->description,
                'unit_measure'     =>  $request->unit_measure,
                'qty'              =>  $request->qty,
                'amount'           =>  $request->amount,
                'client_id'        =>  $request->client_id
            );
            $id = DB::table('fixed_assets')->insert($data);
            if ($id > 0) {
                echo '<div class="alert alert-success">Data Inserted</div>';
            }
        }
    } */
    public function store(Request $request)
    {
        $authUser = Auth::user();
        $request_forRestLoan_officer = $request->loan_officer_id;
        $authId = Auth::id();
        $request->validate([
            'fund_id' => ['required'],
            'loan_product_id' => ['required'],
            'client_id' => ['required'],
            'applied_amount' => ['required', 'numeric'],
            'loan_term' => ['required', 'numeric'],
            'repayment_frequency' => ['required', 'numeric'],
            'repayment_frequency_type' => ['required'],
            'interest_rate' => ['required', 'numeric'],
            'expected_disbursement_date' => ['required', 'date'],
            'loan_purpose_id' => ['required'],
            'expected_first_payment_date' => ['required', 'date'],
        ]);
        $loan_product = LoanProduct::find($request->loan_product_id);
        $client = Client::find($request->client_id);
        $loan = new Loan();
        $kyc = new KYC();
        $bussiness_plan = new BussinessPlan();
        $source_captial = new SourceCaptial();
        $financial_plan = new FinancialPlan();
        $others = new Others();

        $loan->currency_id = $loan_product->currency_id;
        $loan->id = mt_rand(0, 100000);
        $loan->loan_product_id = $loan_product->id;
        $loan->client_id = $client->id;
        $loan->branch_id = $client->branch_id;
        $loan->loan_transaction_processing_strategy_id = $loan_product->loan_transaction_processing_strategy_id;
        $loan->loan_purpose_id = $request->loan_purpose_id;
        $loan->loan_officer_id = determin_the_role_forLoanView_loan_officer($authUser, $request_forRestLoan_officer);
        // $loan->loan_officer_id = $authId;
        $loan->expected_disbursement_date = $request->expected_disbursement_date;
        $loan->expected_first_payment_date = date('Y-m-d', strtotime("+3 months", strtotime($request->expected_disbursement_date)));
        $loan->fund_id = $request->fund_id;
        $loan->created_by_id = Auth::id();
        $loan->applied_amount = $request->applied_amount;
        $loan->loan_term = $request->loan_term;
        $loan->repayment_frequency = $request->repayment_frequency;
        $loan->repayment_frequency_type = $request->repayment_frequency_type;
        $loan->interest_rate = $request->interest_rate;
        $kyc->client_id = $client->id;
        $kyc->loan_id = $loan->id;
        $kyc->bussiness_sector_id = $request->bussiness_sector_id;
        $kyc->reason_bussiness  = $request->reason_bussiness;
        $kyc->knowledge_id = $request->knowledge_id;
        $kyc->owners_of_bussiness = $request->owners_of_bussiness;
        $kyc->bank_name = $request->bank_name;
        $kyc->bank_branch_name = $request->bank_branch_name;
        $kyc->bank_saving_account = $request->bank_saving_account;
        $kyc->martial_status = $request->martial_status;
        $kyc->tin_number = $request->tin_number;
        $kyc->tin_number2 = $request->tin_number2;
        $kyc->physical_bussiness = $request->physical_bussiness;
        $kyc->loan_before = $request->loan_before;
        $kyc->number_rounds_loan = $request->number_rounds_loan;
        $kyc->reason_histroy_loan = $request->reason_histroy_loan;
        $kyc->save();
        $bussiness_plan->client_id = $client->id;
        $bussiness_plan->loan_id = $loan->id;
        $bussiness_plan->estimated_market_size = $request->estimated_market_size;
        $bussiness_plan->market_potential = $request->market_potential;
        $bussiness_plan->sales_approach = $request->sales_approach;
        $bussiness_plan->expected_risk_of_bussiness = $request->expected_risk_of_bussiness;
        $bussiness_plan->direct_employee = $request->direct_employee;
        $bussiness_plan->indirect_employee = $request->indirect_employee;
        $bussiness_plan->supplier_amount = $request->supplier_amount;
        $bussiness_plan->customer_buying_power = $request->customer_buying_power;
        $bussiness_plan->pricing_strategy = $request->pricing_strategy;
        $bussiness_plan->save();
        $source_captial->client_id = $client->id;
        $source_captial->loan_id = $loan->id;
        $source_captial->own_capital = $request->own_capital;
        $source_captial->other_source_of_income = $request->other_source_of_income;
        $source_captial->total_captial = $request->total_captial;
        $source_captial->save();
        $financial_plan->client_id = $client->id;
        $financial_plan->loan_id = $loan->id;
        $financial_plan->current_asset_amount = $request->current_asset_amount;
        $financial_plan->fixed_asset_amount = $request->fixed_asset_amount;
        $financial_plan->current_liability_amount = $request->current_liability_amount;
        $financial_plan->long_term_liabilit_amount = $request->long_term_liabilit_amount;
        $financial_plan->annual_income_bussiness = $request->annual_income_bussiness;
        $financial_plan->monthly_income_bussiness = ($request->annual_income_bussiness / 12);
        $financial_plan->daily_income_bussiness = ($request->annual_income_bussiness / 365);
        $financial_plan->annual_expense = $request->annual_expense;
        $financial_plan->monthly_expense = ($request->annual_expense / 12);
        $financial_plan->daily_expesnse = ($request->annual_expense / 365);
        $financial_plan->raw_material_total = $request->raw_material_total;
        $financial_plan->recuring_costs_total = $request->recuring_costs_total;
        $financial_plan->sales_reveanu_total = $request->sales_reveanu_total;
        $financial_plan->gross_profit_total = ($request->sales_reveanu_total - $request->raw_material_total);
        $financial_plan->expenses_total = ($request->recuring_costs_total + ($request->fixed_asset_amount / 5));
        $financial_plan->net_profit = (($request->sales_reveanu_total - $request->raw_material_total) - ($request->recuring_costs_total + ($request->fixed_asset_amount / 5)));
        if ((($request->sales_reveanu_total - $request->raw_material_total) - ($request->recuring_costs_total + ($request->fixed_asset_amount / 5))) > 0) {
            $financial_plan->profitablity = 'YES';
        } else {
            $financial_plan->profitablity = 'NO';
        }

        $financial_plan->asset_depression = ($request->fixed_asset_amount / 5);
        $financial_plan->monthly_profit = (((($request->sales_reveanu_total - $request->raw_material_total) - ($request->recuring_costs_total + ($request->fixed_asset_amount / 5))) + ($request->fixed_asset_amount / 5)) / 12);
        $financial_plan->applied_loan_amount_repayment = ($request->applied_amount * ((.00583 * (1.00583 ^ 36)) / ((1.00583 ^ 36) - 1)));
        $financial_plan->amount_remaning = ((((($request->sales_reveanu_total - $request->raw_material_total) - ($request->recuring_costs_total + ($request->fixed_asset_amount / 5))) + ($request->fixed_asset_amount / 5)) / 12) - ($request->applied_amount * ((.00583 * (1.00583 ^ 36)) / ((1.00583 ^ 36) - 1))));
        if (((((($request->sales_reveanu_total - $request->raw_material_total) - ($request->recuring_costs_total + ($request->fixed_asset_amount / 5))) + ($request->fixed_asset_amount / 5)) / 12) - ($request->applied_amount * ((.00583 * (1.00583 ^ 36)) / ((1.00583 ^ 36) - 1)))) > 0) {
            $financial_plan->feasiblity_check = 'YES';
        } else {
            $financial_plan->feasiblity_check = 'NO';
        }
        $financial_plan->save();
        $others->client_id = $client->id;
        $others->loan_id = $loan->id;
        $others->book_of_records = $request->book_of_records;
        $others->edir_status = $request->edir_status;
        $others->edir_payment_status = $request->edir_payment_status;
        $others->equib_status = $request->equib_status;
        $others->equib_payment_status = $request->equib_payment_status;
        $others->community_role_status = $request->community_role_status;
        $others->utillites_payment_status = $request->utillites_payment_status;
        $others->fines_penalities_status = $request->fines_penalities_status;
        $others->save();
        $loan->interest_rate_type = $loan_product->interest_rate_type;
        $loan->grace_on_principal_paid = $loan_product->grace_on_principal_paid;
        $loan->grace_on_interest_paid = $loan_product->grace_on_interest_paid;
        $loan->grace_on_interest_charged = $loan_product->grace_on_interest_charged;
        $loan->interest_methodology = $loan_product->interest_methodology;
        $loan->amortization_method = $loan_product->amortization_method;
        $loan->auto_disburse = $loan_product->auto_disburse;
        $loan->submitted_on_date = date("Y-m-d");
        $loan->submitted_by_user_id = Auth::id();
        $loan->save();
        //save charges
        if (!empty($request->charges)) {
            foreach ($request->charges as $key => $value) {
                $loan_charge = LoanCharge::find($key);
                $loan_linked_charge = new LoanLinkedCharge();
                $loan_linked_charge->loan_id = $loan->id;
                $loan_linked_charge->name = $loan_charge->name;
                $loan_linked_charge->loan_charge_id = $key;
                if ($loan_charge->allow_override == 1) {
                    $loan_linked_charge->amount = $value;
                } else {
                    $loan_linked_charge->amount = $loan_charge->amount;
                }
                $loan_linked_charge->loan_charge_type_id = $loan_charge->loan_charge_type_id;
                $loan_linked_charge->loan_charge_option_id = $loan_charge->loan_charge_option_id;
                $loan_linked_charge->is_penalty = $loan_charge->is_penalty;
                $loan_linked_charge->save();
            }
        }
        $loan_history = new LoanHistory();
        $loan_history->loan_id = $loan->id;
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Created';
        $loan_history->save();
        $loan_officer_history = new LoanOfficerHistory();
        $loan_officer_history->loan_id = $loan->id;
        $loan_officer_history->created_by_id = Auth::id();
        $loan_officer_history->loan_officer_id = $request->loan_officer_id;
        $loan_officer_history->start_date = date("Y-m-d");
        $loan_officer_history->save();
        custom_fields_save_form('add_loan', $request, $loan->id);
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Create Loan');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        //fire loan status changed event
        event(new LoanStatusChanged($loan));
        return redirect('loan/' . $loan->id . '/show');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function reschedule_loan(Request $request, $id)
    {
        $request->validate([
            'rescheduled_from_date' => ['required', 'date'],
            'rescheduled_on_date' => ['required', 'date'],
            'rescheduled_first_payment_date' => ['required_if:reschedule_first_payment_date,on', 'date'],
            'reschedule_grace_on_principal_paid' => ['nullable', 'numeric'],
            'reschedule_grace_on_interest_paid' => ['nullable', 'numeric'],
            'reschedule_extra_installments' => ['required_if:reschedule_add_extra_installments,on', 'numeric'],
            'reschedule_interest_rate' => ['required_if:reschedule_adjust_loan_interest_rate,on', 'numeric'],
        ]);
        $loan = Loan::find($id);

        if (empty($loan->repayment_schedules->where('due_date', $request->rescheduled_from_date)->first())) {
            \flash(trans_choice("loan::general.no_installment_schedule_found", 1))->warning()->important();
            return redirect()->back();
        }
        $reschedule_principal = $loan->repayment_schedules->sum('principal') - $loan->repayment_schedules->where('due_date', '<', $request->rescheduled_from_date)->sum('principal');
        LoanRepaymentSchedule::where('due_date', '>=', $request->rescheduled_from_date)->where('loan_id', $loan->id)->delete();
        $loan_product = $loan->loan_product;
        $client = $loan->client;
        $interest_rate = determine_period_interest_rate($request->reschedule_interest_rate ?: $loan->interest_rate, $loan->repayment_frequency_type, $loan->interest_rate_type);
        $balance = round($reschedule_principal, 2);
        $period = $loan->repayment_schedules->where('due_date', '>=', $request->rescheduled_from_date)->count() + $request->reschedule_extra_installments;
        $payment_from_date = $request->rescheduled_on_date;
        $next_payment_date = $request->rescheduled_first_payment_date ?: $loan->repayment_schedules->where('due_date', '>=', $request->rescheduled_from_date)->first()->due_date;

        for ($i = 1; $i <= $period; $i++) {
            $loan_repayment_schedule = new LoanRepaymentSchedule();
            $loan_repayment_schedule->created_by_id = Auth::id();
            $loan_repayment_schedule->loan_id = $loan->id;
            $loan_repayment_schedule->installment = $i;
            $loan_repayment_schedule->due_date = $next_payment_date;
            $loan_repayment_schedule->from_date = $payment_from_date;
            $date = explode('-', $next_payment_date);
            $loan_repayment_schedule->month = $date[1];
            $loan_repayment_schedule->year = $date[0];
            //determine which method to use
            //flat  method
            if ($loan->interest_methodology == 'flat') {
                $principal = round($reschedule_principal / $period, 2);
                $interest = round($interest_rate * $principal, 2);
                if ($request->reschedule_grace_on_interest_charged >= $i) {
                    $loan_repayment_schedule->interest = 0;
                } else {
                    $loan_repayment_schedule->interest = $interest;
                }
                if ($i == $period) {
                    //account for values lost during rounding
                    $loan_repayment_schedule->principal = round($balance, 2);
                } else {
                    $loan_repayment_schedule->principal = $principal;
                }
                //determine next balance
                $balance = ($balance - $principal);
            }
            //reducing balance
            if ($loan->interest_methodology == 'declining_balance') {
                if ($loan->amortization_method == 'equal_installments') {
                    $amortized_payment = round(determine_amortized_payment($interest_rate, $reschedule_principal, $period), 2);
                    //determine if we have grace period for interest
                    $interest = round($interest_rate * $balance, 2);
                    $principal = round(($amortized_payment - $interest), 2);
                    if ($request->reschedule_grace_on_interest_charged >= $i) {
                        $loan_repayment_schedule->interest = 0;
                    } else {
                        $loan_repayment_schedule->interest = $interest;
                    }
                    if ($i == $period) {
                        //account for values lost during rounding
                        $loan_repayment_schedule->principal = round($balance, 2);
                    } else {
                        $loan_repayment_schedule->principal = $principal;
                    }
                    //determine next balance
                    $balance = ($balance - $principal);
                }
                if ($loan->amortization_method == 'equal_principal_payments') {
                    $principal = round($reschedule_principal / $period, 2);
                    //determine if we have grace period for interest
                    $interest = round($interest_rate * $balance, 2);
                    if ($request->reschedule_grace_on_interest_charged >= $i) {
                        $loan_repayment_schedule->interest = 0;
                    } else {
                        $loan_repayment_schedule->interest = $interest;
                    }
                    if ($i == $period) {
                        //account for values lost during rounding
                        $loan_repayment_schedule->principal = round($balance, 2);
                    } else {
                        $loan_repayment_schedule->principal = $principal;
                    }
                    //determine next balance
                    $balance = ($balance - $principal);
                }
            }
            $payment_from_date = Carbon::parse($next_payment_date)->add(1, 'day')->format("Y-m-d");
            $next_payment_date = Carbon::parse($next_payment_date)->add($loan->repayment_frequency, $loan->repayment_frequency_type)->format("Y-m-d");
            $loan_repayment_schedule->total_due = $loan_repayment_schedule->principal + $loan_repayment_schedule->interest;
            $loan_repayment_schedule->save();
        }

        $loan->load('repayment_schedules');
        $total_principal = $loan->repayment_schedules->sum('principal');
        $total_interest = $loan->repayment_schedules->sum('interest');
        foreach ($loan->charges->whereIn('loan_charge_type_id', [3, 2]) as $key) {
            //installment_fee
            $total_calculated_amount = 0;
            if ($key->loan_charge_type_id == 3) {
                if ($key->loan_charge_option_id == 1) {
                    $key->calculated_amount = $key->amount;
                }
                if ($key->loan_charge_option_id == 2) {
                    $key->calculated_amount = round(($key->amount * $total_principal / 100), 2);
                }
                if ($key->loan_charge_option_id == 3) {
                    $key->calculated_amount = round(($key->amount * ($total_interest + $total_principal) / 100), 2);
                }
                if ($key->loan_charge_option_id == 4) {
                    $key->calculated_amount = round(($key->amount * $total_interest / 100), 2);
                }
                if ($key->loan_charge_option_id == 5) {
                    $key->calculated_amount = round(($key->amount * $total_principal / 100), 2);
                }
                if ($key->loan_charge_option_id == 6) {
                    $key->calculated_amount = round(($key->amount * $total_principal / 100), 2);
                }
                if ($key->loan_charge_option_id == 7) {
                    $key->calculated_amount = round(($key->amount * $loan->principal / 100), 2);
                }

                //reverse and create new transaction
                if (!empty($key->transaction)) {
                    $key->transaction->credit = $key->transaction->amount;
                    $key->transaction->debit = $key->transaction->amount;
                    $key->transaction->reversed = 1;
                    $key->transaction->save();
                }

                $loan_transaction = new LoanTransaction();
                $loan_transaction->created_by_id = Auth::id();
                $loan_transaction->loan_id = $loan->id;
                $loan_transaction->branch_id = $loan->branch_id;
                $loan_transaction->name = trans_choice('loan::general.fee', 1) . ' ' . $loan_transaction->name = trans_choice('loan::general.applied', 1);
                $loan_transaction->loan_transaction_type_id = 10;
                $loan_transaction->submitted_on = $loan->disbursed_on_date;
                $loan_transaction->created_on = date("Y-m-d");
                $loan_transaction->amount = $key->calculated_amount;
                $loan_transaction->debit = $key->calculated_amount;
                $loan_transaction->reversible = 1;
                $loan_transaction->save();
                $key->loan_transaction_id = $loan_transaction->id;
                $key->save();
                foreach ($loan->repayment_schedules->where('due_date', '>=', $request->rescheduled_from_date) as $loan_repayment_schedule) {
                    if ($key->loan_charge_option_id == 2) {
                        $loan_repayment_schedule->fees = $loan_repayment_schedule->fees + round(($key->amount * $loan_repayment_schedule->principal / 100), 2);
                    } elseif ($key->loan_charge_option_id == 3) {
                        $loan_repayment_schedule->fees = $loan_repayment_schedule->fees + round(($key->amount * ($loan_repayment_schedule->interest + $loan_repayment_schedule->principal) / 100), 2);
                    } elseif ($key->loan_charge_option_id == 4) {
                        $loan_repayment_schedule->fees = $loan_repayment_schedule->fees + round(($key->amount * $loan_repayment_schedule->interest / 100), 2);
                    } else {
                        $loan_repayment_schedule->fees = $loan_repayment_schedule->fees + $key->calculated_amount;
                    }
                    $loan_repayment_schedule->total_due = $loan_repayment_schedule->principal + $loan_repayment_schedule->interest + $loan_repayment_schedule->fees;
                    $loan_repayment_schedule->save();
                }
            }
        }

        $loan->save();

        $loan->expected_maturity_date = $next_payment_date;
        $loan->rescheduled_on_date = $request->rescheduled_on_date;
        $loan->rescheduled_notes = $request->rescheduled_notes;
        $loan->rescheduled_by_user_id = Auth::id();
        $loan->save();
        $loan_history = new LoanHistory();
        $loan_history->loan_id = $id;
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Rescheduled';
        $loan_history->save();
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Reschedule Loan');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $loan->id . '/show');
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show_application($id)
    {
        $users = User::whereHas('roles', function ($query) {
            return $query->where('name', '!=', 'client');
        })->get();
        $payment_types = PaymentType::where('active', 1)->get();
        $loan_application = LoanApplication::with('client')->with('loan_product')->find($id);
        return theme_view('loan::application.show', compact('loan_application', 'users', 'payment_types'));
    }

    public function show($id)
    {

        $users = User::whereHas('roles', function ($query) {
            return $query->where('name', '!=', 'client');
        })->get();
        $payment_types = PaymentType::where('active', 1)->get();
        $loan = Loan::with('repayment_schedules')->with('transactions')->with('charges')->with('client')->with('loan_product')->with('notes')->with('guarantors')->with('files')->with('collateral')->with('collateral.collateral_type')->with('notes.created_by')->find($id);
        $custom_fields = CustomField::where('category', 'add_loan')->where('active', 1)->get();
        $financial = FinancialPlan::where('loan_id', $id)->first();
        $kyc = KYC::where('loan_id', $id)->first();
        $bussiness_plan = BussinessPlan::where('loan_id', $id)->first();
        $source_captial = SourceCaptial::where('loan_id', $id)->first();
        $others = Others::where('loan_id', $id)->first();

        return theme_view('loan::loan.show', compact('loan', 'others', 'source_captial', 'bussiness_plan', 'kyc', 'financial', 'users', 'payment_types', 'custom_fields'));
    }


    public function edit($id)
    {
        $loan = Loan::with('charges')->with('client')->with('loan_product')->find($id);
        $kyc = KYC::Where('loan_id', $id)->first();
        $bussiness_plan = BussinessPlan::where('loan_id', $id)->first();
        $source_captial = SourceCaptial::where('loan_id', $id)->first();
        $financial_plan = FinancialPlan::where('loan_id', $id)->first();
        $others =  Others::where('loan_id', $id)->first();
        $client = $loan->client;
        $loan_product = $loan->loan_product;
        $funds = Fund::all();
        $loan_purposes = LoanPurpose::get();
        $users = User::whereHas('roles', function ($query) {
            return $query->where('name', '!=', 'client');
        })->get();
        $charges = [];
        $charges_list = [];
        $temp_charges = [];
        foreach ($loan->charges as $key) {
            $temp_charges[] = $key->loan_charge_id;
        }
        foreach ($loan_product->charges as $key) {
            if (!empty($key->charge)) {
                //charge type
                if ($key->charge->loan_charge_type_id == 1) {
                    $key->charge->loan_charge_type_id = trans_choice('loan::general.disbursement', 1);
                }
                if ($key->charge->loan_charge_type_id == 2) {
                    $key->charge->loan_charge_type_id = trans_choice('loan::general.specified_due_date', 1);
                }
                if ($key->charge->loan_charge_type_id == 3) {
                    $key->charge->loan_charge_type_id = trans_choice('loan::general.installment', 1) . ' ' . trans_choice('loan::general.fee', 2);
                }
                if ($key->charge->loan_charge_type_id == 4) {
                    $key->charge->loan_charge_type_id = trans_choice('loan::general.overdue', 1) . ' ' . trans_choice('loan::general.installment', 1) . ' ' . trans_choice('loan::general.fee', 2);
                }
                if ($key->charge->loan_charge_type_id == 5) {
                    $key->charge->loan_charge_type_id = trans_choice('loan::general.disbursement_paid_with_repayment', 1);
                }
                if ($key->charge->loan_charge_type_id == 6) {
                    $key->charge->loan_charge_type_id = trans_choice('loan::general.loan_rescheduling_fee', 1);
                }
                if ($key->charge->loan_charge_type_id == 7) {
                    $key->charge->loan_charge_type_id = trans_choice('loan::general.overdue_on_loan_maturity', 1);
                }
                if ($key->charge->loan_charge_type_id == 8) {
                    $key->charge->loan_charge_type_id = trans_choice('loan::general.last_installment_fee', 1);
                }
                //charge option
                if ($key->charge->loan_charge_option_id == 1) {
                    $key->charge->loan_charge_option_id = trans_choice('loan::general.flat', 1);
                }
                if ($key->charge->loan_charge_option_id == 2) {
                    $key->charge->loan_charge_option_id = trans_choice('loan::general.principal_due_on_installment', 1);
                }
                if ($key->charge->loan_charge_option_id == 3) {
                    $key->charge->loan_charge_option_id = trans_choice('loan::general.principal_interest_due_on_installment', 1);
                }
                if ($key->charge->loan_charge_option_id == 4) {
                    $key->charge->loan_charge_option_id = trans_choice('loan::general.interest_due_on_installment', 1);
                }
                if ($key->charge->loan_charge_option_id == 5) {
                    $key->charge->loan_charge_option_id = trans_choice('loan::general.total_outstanding_loan_principal', 1);
                }
                if ($key->charge->loan_charge_option_id == 6) {
                    $key->charge->loan_charge_option_id = trans_choice('loan::general.percentage_of_original_loan_principal_per_installment', 1);
                }
                if ($key->charge->loan_charge_option_id == 7) {
                    $key->charge->loan_charge_option_id = trans_choice('loan::general.original_loan_principal', 1);
                }
                $charges[$key->charge->id] = $key;
                if (in_array($key->charge->id, $temp_charges)) {
                    $charges_list[] = $key;
                }
            }
        }
        JavaScript::put([
            'loan_product' => $loan_product,
            'charges' => $charges,
            'original_charges' => $charges,
            'charges_list' => $charges_list,
            'funds' => $funds,
            'loan_purposes' => $loan_purposes,

        ]);
        $custom_fields = CustomField::where('category', 'add_loan')->where('active', 1)->get();

        return theme_view('loan::loan.edit', compact('client', 'loan_product', 'users', 'loan_purposes', 'funds', 'loan', 'others', 'source_captial', 'bussiness_plan', 'kyc', 'financial_plan', 'custom_fields'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'fund_id' => ['required'],
            'loan_product_id' => ['required'],
            'client_id' => ['required'],
            'applied_amount' => ['required', 'numeric'],
            'loan_term' => ['required', 'numeric'],
            'repayment_frequency' => ['required', 'numeric'],
            'repayment_frequency_type' => ['required'],
            'interest_rate' => ['required', 'numeric'],
            'expected_disbursement_date' => ['required', 'date'],
            'loan_officer_id' => ['required'],
            'loan_purpose_id' => ['required'],
            'expected_first_payment_date' => ['required', 'date'],
        ]);
        $loan_product = LoanProduct::find($request->loan_product_id);
        $loan = Loan::find($id);
        $kyc = KYC::Where('loan_id', $id)->first();
        $bussiness_plan = BussinessPlan::where('loan_id', $id)->first();
        $source_captial = SourceCaptial::where('loan_id', $id)->first();
        $financial_plan = FinancialPlan::where('loan_id', $id)->first();
        $others =  Others::where('loan_id', $id)->first();
        //update loan
        $loan->loan_purpose_id = $request->loan_purpose_id;
        $loan->loan_officer_id = $request->loan_officer_id;
        $loan->expected_disbursement_date = $request->expected_disbursement_date;
        $loan->expected_first_payment_date = $request->expected_first_payment_date;
        $loan->fund_id = $request->fund_id;
        $loan->applied_amount = $request->applied_amount;
        $loan->loan_term = $request->loan_term;
        $loan->repayment_frequency = $request->repayment_frequency;
        $loan->repayment_frequency_type = $request->repayment_frequency_type;
        $loan->interest_rate = $request->interest_rate;
        $loan->interest_rate_type = $loan_product->interest_rate_type;
        $loan->save();

        //update kyc
        $kyc->bussiness_sector_id = $request->bussiness_sector_id;
        $kyc->reason_bussiness = $request->reason_bussiness;
        $kyc->knowledge_id = $request->knowledge_id;
        $kyc->owners_of_bussiness = $request->owners_of_bussiness;
        $kyc->bank_name = $request->bank_name;
        $kyc->bank_branch_name = $request->bank_branch_name;
        $kyc->bank_saving_account = $request->bank_saving_account;
        $kyc->martial_status = $request->martial_status;
        $kyc->tin_number = $request->tin_number;
        $kyc->tin_number2 = $request->tin_number2;
        $kyc->physical_bussiness = $request->physical_bussiness;
        $kyc->loan_before = $request->loan_before;
        $kyc->number_rounds_loan = $request->number_rounds_loan;
        $kyc->reason_histroy_loan = $request->reason_histroy_loan;
        $kyc->save();
        //update bussiness_plan
        $bussiness_plan->estimated_market_size = $request->estimated_market_size;
        $bussiness_plan->market_potential = $request->market_potential;
        $bussiness_plan->sales_approach = $request->sales_approach;
        $bussiness_plan->expected_risk_of_bussiness = $request->expected_risk_of_bussiness;
        $bussiness_plan->direct_employee = $request->direct_employee;
        $bussiness_plan->indirect_employee = $request->indirect_employee;
        $bussiness_plan->supplier_amount = $request->supplier_amount;
        $bussiness_plan->customer_buying_power = $request->customer_buying_power;
        $bussiness_plan->pricing_strategy = $request->pricing_strategy;
        $bussiness_plan->save();
        //update source of captial
        $source_captial->own_capital = $request->own_capital;
        $source_captial->other_source_of_income = $request->other_source_of_income;
        $source_captial->total_captial = $request->total_captial;
        $source_captial->save();

        //updae financial plan;
        $financial_plan->current_asset_amount = $request->current_asset_amount;
        $financial_plan->fixed_asset_amount = $request->fixed_asset_amount;
        $financial_plan->current_liability_amount = $request->current_liability_amount;
        $financial_plan->long_term_liabilit_amount = $request->long_term_liabilit_amount;
        $financial_plan->annual_income_bussiness = $request->annual_income_bussiness;
        $monthly_income = ($request->annual_income_bussiness / 12);
        $daily_income = ($request->annual_income_bussiness / 365);
        $financial_plan->monthly_income_bussiness = $monthly_income;
        $financial_plan->daily_income_bussiness = ($daily_income);
        $financial_plan->annual_expense = $request->annual_expense;
        $monthly_expense = ($request->annual_expense / 12);
        $daily_expense = ($request->annual_expense / 365);
        $financial_plan->monthly_expense = $monthly_expense;
        $financial_plan->daily_expesnse = $daily_expense;

        $financial_plan->raw_material_total = $request->raw_material_total;
        $financial_plan->recuring_costs_total = $request->recuring_costs_total;
        $financial_plan->sales_reveanu_total = $request->sales_reveanu_total;
        $financial_plan->gross_profit_total = ($request->sales_reveanu_total - $request->raw_material_total);
        $financial_plan->expenses_total = ($request->recuring_costs_total + ($request->fixed_asset_amount / 5));
        $financial_plan->net_profit = (($request->sales_reveanu_total - $request->raw_material_total) - ($request->recuring_costs_total + ($request->fixed_asset_amount / 5)));
        if ((($request->sales_reveanu_total - $request->raw_material_total) - ($request->recuring_costs_total + ($request->fixed_asset_amount / 5))) > 0) {
            $financial_plan->profitablity = 'YES';
        } else {
            $financial_plan->profitablity = 'NO';
        }

        $financial_plan->asset_depression = ($request->fixed_asset_amount / 5);
        $financial_plan->monthly_profit = (((($request->sales_reveanu_total - $request->raw_material_total) - ($request->recuring_costs_total + ($request->fixed_asset_amount / 5))) + ($request->fixed_asset_amount / 5)) / 12);
        $financial_plan->applied_loan_amount_repayment = ($request->applied_amount * ((.00583 * (1.00583 ^ 36)) / ((1.00583 ^ 36) - 1)));
        $financial_plan->amount_remaning = ((((($request->sales_reveanu_total - $request->raw_material_total) - ($request->recuring_costs_total + ($request->fixed_asset_amount / 5))) + ($request->fixed_asset_amount / 5)) / 12) - ($request->applied_amount * ((.00583 * (1.00583 ^ 36)) / ((1.00583 ^ 36) - 1))));
        if (((((($request->sales_reveanu_total - $request->raw_material_total) - ($request->recuring_costs_total + ($request->fixed_asset_amount / 5))) + ($request->fixed_asset_amount / 5)) / 12) - ($request->applied_amount * ((.00583 * (1.00583 ^ 36)) / ((1.00583 ^ 36) - 1)))) > 0) {
            $financial_plan->feasiblity_check = 'YES';
        } else {
            $financial_plan->feasiblity_check = 'NO';
        }
        $financial_plan->save();
        //update Others
        $others->book_of_records = $request->book_of_records;
        $others->edir_status = $request->edir_status;
        $others->edir_payment_status = $request->edir_payment_status;
        $others->equib_status = $request->equib_status;
        $others->equib_payment_status = $request->equib_payment_status;
        $others->community_role_status = $request->community_role_status;
        $others->utillites_payment_status = $request->utillites_payment_status;
        $others->fines_penalities_status = $request->fines_penalities_status;
        $others->save();
        //save charges
        LoanLinkedCharge::where('loan_id', $id)->delete();
        if (!empty($request->charges)) {
            foreach ($request->charges as $key => $value) {
                $loan_charge = LoanCharge::find($key);
                $loan_linked_charge = new LoanLinkedCharge();
                $loan_linked_charge->loan_id = $loan->id;
                $loan_linked_charge->name = $loan_charge->name;
                $loan_linked_charge->loan_charge_id = $key;
                if ($loan_charge->allow_override == 1) {
                    $loan_linked_charge->amount = $value;
                } else {
                    $loan_linked_charge->amount = $loan_charge->amount;
                }
                $loan_linked_charge->loan_charge_type_id = $loan_charge->loan_charge_type_id;
                $loan_linked_charge->loan_charge_option_id = $loan_charge->loan_charge_option_id;
                $loan_linked_charge->is_penalty = $loan_charge->is_penalty;
                $loan_linked_charge->save();
            }
        }
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Update Loan');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $loan->id . '/show');
    }

    public function approve_loan(Request $request, $id)
    {

        $request->validate([
            'approved_on_date' => ['required', 'date'],
            'approved_amount' => ['required', 'numeric'],
        ]);
        $loan = Loan::find($id);
        $previous_status = $loan->status;
        $loan->approved_by_user_id = Auth::id();
        $loan->approved_amount = $request->approved_amount;
        $loan->approved_on_date = $request->approved_on_date;
        $loan->status = 'approved';
        $loan->approved_notes = $request->approved_notes;
        $loan->save();
        $loan_history = new LoanHistory();
        $loan_history->loan_id = $loan->id;
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Approved';
        $loan_history->save();
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Approve Loan');
        //fire loan status changed event
        event(new LoanStatusChanged($loan, $previous_status));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $loan->id . '/show');
    }

    public function undo_approval(Request $request, $id)
    {

        $loan = Loan::find($id);
        $previous_status = $loan->status;
        $loan->approved_by_user_id = null;
        $loan->approved_amount = null;
        $loan->approved_on_date = null;
        $loan->status = 'submitted';
        $loan->approved_notes = null;
        $loan->save();
        $loan_history = new LoanHistory();
        $loan_history->loan_id = $loan->id;
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Unapproved';
        $loan_history->save();
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Undo Loan Approval');
        //fire loan status changed event
        event(new LoanStatusChanged($loan, $previous_status));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $loan->id . '/show');
    }

    public function reject_loan(Request $request, $id)
    {

        $request->validate([
            'rejected_notes' => ['required'],
        ]);
        $loan = Loan::find($id);
        $previous_status = $loan->status;
        $loan->rejected_by_user_id = Auth::id();
        $loan->rejected_on_date = date("Y-m-d");
        $loan->status = 'rejected';
        $loan->rejected_notes = $request->rejected_notes;
        $loan->save();
        $loan_history = new LoanHistory();
        $loan_history->loan_id = $loan->id;
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Rejected';
        $loan_history->save();
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Reject Loan');
        //fire loan status changed event
        event(new LoanStatusChanged($loan, $previous_status));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $loan->id . '/show');
    }

    public function undo_rejection(Request $request, $id)
    {

        $loan = Loan::find($id);
        $previous_status = $loan->status;
        $loan->rejected_by_user_id = null;
        $loan->rejected_on_date = null;
        $loan->status = 'submitted';
        $loan->rejected_notes = null;
        $loan->save();
        $loan_history = new LoanHistory();
        $loan_history->loan_id = $loan->id;
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Unrejected';
        $loan_history->save();
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Undo Loan Rejection');
        //fire loan status changed event
        event(new LoanStatusChanged($loan, $previous_status));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $loan->id . '/show');
    }

    public function withdraw_loan(Request $request, $id)
    {

        $request->validate([
            'withdrawn_notes' => ['required'],
        ]);
        $loan = Loan::find($id);
        $previous_status = $loan->status;
        $loan->withdrawn_by_user_id = Auth::id();
        $loan->withdrawn_on_date = date("Y-m-d");
        $loan->status = 'withdrawn';
        $loan->withdrawn_notes = $request->withdrawn_notes;
        $loan->save();
        $loan_history = new LoanHistory();
        $loan_history->loan_id = $loan->id;
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Withdrawn';
        $loan_history->save();
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Withdraw Loan');
        //fire loan status changed event
        event(new LoanStatusChanged($loan, $previous_status));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $loan->id . '/show');
    }

    public function undo_withdrawn(Request $request, $id)
    {

        $loan = Loan::find($id);
        $previous_status = $loan->status;
        $loan->withdrawn_by_user_id = null;
        $loan->withdrawn_on_date = null;
        $loan->status = 'submitted';
        $loan->withdrawn_notes = null;
        $loan->save();
        $loan_history = new LoanHistory();
        $loan_history->loan_id = $loan->id;
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Unwithdrawn';
        $loan_history->save();
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Undo Loan Withdrawal');
        //fire loan status changed event
        event(new LoanStatusChanged($loan, $previous_status));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $loan->id . '/show');
    }

    public function write_off_loan(Request $request, $id)
    {

        $request->validate([
            'written_off_on_date' => ['required'],
            'written_off_notes' => ['required'],
        ]);
        $loan = Loan::with('repayment_schedules')->find($id);
        $principal = $loan->repayment_schedules->sum('principal') - $loan->repayment_schedules->sum('principal_written_off_derived') - $loan->repayment_schedules->sum('principal_repaid_derived');
        $interest = $loan->repayment_schedules->sum('interest') - $loan->repayment_schedules->sum('interest_written_off_derived') - $loan->repayment_schedules->sum('interest_repaid_derived') - $loan->repayment_schedules->sum('interest_waived_derived');
        $fees = $loan->repayment_schedules->sum('fees') - $loan->repayment_schedules->sum('fees_written_off_derived') - $loan->repayment_schedules->sum('fees_repaid_derived') - $loan->repayment_schedules->sum('fees_waived_derived');
        $penalties = $loan->repayment_schedules->sum('penalties') - $loan->repayment_schedules->sum('penalties_written_off_derived') - $loan->repayment_schedules->sum('penalties_repaid_derived') - $loan->repayment_schedules->sum('penalties_waived_derived');
        $balance = $principal + $interest + $fees + $penalties;
        $previous_status = $loan->status;
        $loan->written_off_by_user_id = Auth::id();
        $loan->written_off_on_date = date("Y-m-d");
        $loan->status = 'written_off';
        $loan->written_off_notes = $request->written_off_notes;
        $loan->save();
        $loan_transaction = new LoanTransaction();
        $loan_transaction->created_by_id = Auth::id();
        $loan_transaction->loan_id = $loan->id;
        $loan_transaction->name = trans_choice('loan::general.write_off', 1);
        $loan_transaction->loan_transaction_type_id = 6;
        $loan_transaction->submitted_on = $loan->written_off_on_date;
        $loan_transaction->created_on = date("Y-m-d");
        $loan_transaction->amount = $balance;
        $loan_transaction->credit = $balance;
        $loan_transaction->save();
        //check if accounting is enabled
        if ($loan->loan_product->accounting_rule == "cash" || $loan->loan_product->accounting_rule == "accrual_periodic" || $loan->loan_product->accounting_rule == "accrual_upfront") {
            //credit account
            $journal_entry = new JournalEntry();
            $journal_entry->created_by_id = Auth::id();
            $journal_entry->transaction_number = 'L' . $loan_transaction->id;
            $journal_entry->branch_id = $loan->branch_id;
            $journal_entry->currency_id = $loan->currency_id;
            $journal_entry->chart_of_account_id = $loan->loan_product->loan_portfolio_chart_of_account_id;
            $journal_entry->transaction_type = 'loan_write_off';
            $journal_entry->date = $loan->written_off_on_date;
            $date = explode('-', $loan->written_off_on_date);
            $journal_entry->month = $date[1];
            $journal_entry->year = $date[0];
            $journal_entry->credit = $balance;
            $journal_entry->reference = $loan->id;
            $journal_entry->save();
            //debit account
            $journal_entry = new JournalEntry();
            $journal_entry->created_by_id = Auth::id();
            $journal_entry->transaction_number = 'L' . $loan_transaction->id;
            $journal_entry->branch_id = $loan->branch_id;
            $journal_entry->currency_id = $loan->currency_id;
            $journal_entry->chart_of_account_id = $loan->loan_product->losses_written_off_chart_of_account_id;
            $journal_entry->transaction_type = 'loan_write_off';
            $journal_entry->date = $loan->written_off_on_date;
            $date = explode('-', $loan->written_off_on_date);
            $journal_entry->month = $date[1];
            $journal_entry->year = $date[0];
            $journal_entry->debit = $balance;
            $journal_entry->reference = $loan->id;
            $journal_entry->save();
        }
        $loan_history = new LoanHistory();
        $loan_history->loan_id = $loan->id;
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Written off';
        $loan_history->save();
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Writeoff Loan');
        event(new TransactionUpdated($loan));
        //fire loan status changed event
        event(new LoanStatusChanged($loan, $previous_status));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $loan->id . '/show');
    }

    public function undo_write_off(Request $request, $id)
    {

        $loan = Loan::find($id);
        $previous_status = $loan->status;
        $loan->written_off_by_user_id = null;
        $loan->written_off_on_date = null;
        $loan->status = 'active';
        $loan->written_off_notes = null;
        $loan->save();
        foreach (LoanTransaction::where('loan_id', $loan->id)->where('loan_transaction_type_id', 6)->where('reversed', 0)->get() as $key) {
            $key->amount = 0;
            $key->debit = $key->credit;
            $key->reversed = 1;
            $key->save();
        }
        $loan_history = new LoanHistory();
        $loan_history->loan_id = $loan->id;
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Unwritten off';
        $loan_history->save();
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Undo Loan writeoff');
        event(new TransactionUpdated($loan));
        //fire loan status changed event
        event(new LoanStatusChanged($loan, $previous_status));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $loan->id . '/show');
    }

    public function change_loan_officer(Request $request, $id)
    {

        $request->validate([
            'loan_officer_id' => ['required'],
        ]);
        $loan = Loan::find($id);
        $previous_loan_officer_id = $loan->loan_officer_id;
        $loan->loan_officer_id = $request->loan_officer_id;
        $loan->save();
        if ($previous_loan_officer_id != $request->loan_officer_id) {
            $previous_loan_officer = LoanOfficerHistory::where('loan_id', $loan->id)->where('loan_officer_id', $request->loan_officer_id)->where('end_date', '')->first();
            if (!empty($previous_loan_officer)) {
                $previous_loan_officer->end_date = date("Y-m-d");
                $previous_loan_officer->save();
            }
            $loan_officer_history = new LoanOfficerHistory();
            $loan_officer_history->loan_id = $loan->id;
            $loan_officer_history->created_by_id = Auth::id();
            $loan_officer_history->loan_officer_id = $request->loan_officer_id;
            $loan_officer_history->start_date = date("Y-m-d");
            $loan_officer_history->save();
        }
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Change Loan Officer');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $loan->id . '/show');
    }

    public function disburse_loan(Request $request, $id)
    {


        $request->validate([
            'disbursed_on_date' => ['required', 'date'],
            'first_payment_date' => ['required', 'date', 'after:disbursed_on_date'],
            'payment_type_id' => ['required'],
        ]);

        $loan = Loan::find($id);
        if ($loan->status != 'approved') {
            \flash(trans_choice('loan::general.loan', 1) . ' ' . trans_choice('core::general.not', 1) . ' ' . trans_choice('loan::general.approved', 1))->warning()->important();
            return redirect()->back();
        }
        //payment details
        $payment_detail = new PaymentDetail();
        $payment_detail->created_by_id = Auth::id();
        $payment_detail->payment_type_id = $request->payment_type_id;
        $payment_detail->transaction_type = 'loan_transaction';
        $payment_detail->cheque_number = $request->cheque_number;
        $payment_detail->receipt = $request->receipt;
        $payment_detail->account_number = $request->account_number;
        $payment_detail->bank_name = $request->bank_name;
        $payment_detail->routing_code = $request->routing_code;
        $payment_detail->save();
        $previous_status = $loan->status;
        $loan->disbursed_by_user_id = Auth::id();
        $loan->disbursed_on_date = $request->disbursed_on_date;
        $loan->first_payment_date = $request->first_payment_date;
        $loan->principal = $loan->approved_amount;
        $loan->status = 'active';

        //prepare loan schedule
        //determine interest rate
        $interest_rate = determine_period_interest_rate($loan->interest_rate, $loan->repayment_frequency_type, $loan->interest_rate_type = "year", $loan->repayment_frequency);
        $balance = round($loan->principal, 2);
        $period = ($loan->loan_term / $loan->repayment_frequency) - ($loan->grace_on_principal_paid);
        $payment_from_date = $request->disbursed_on_date;
        $next_payment_date = $request->first_payment_date;
        $total_principal = 0;
        $total_interest = 0;

        for ($i = 1; $i <= $period; $i++) {
            $loan_repayment_schedule = new LoanRepaymentSchedule();
            $loan_repayment_schedule->created_by_id = Auth::id();
            $loan_repayment_schedule->loan_id = $loan->id;
            $loan_repayment_schedule->installment = $i;
            $loan_repayment_schedule->due_date = $next_payment_date;
            $loan_repayment_schedule->from_date = $payment_from_date;
            $date = explode('-', $next_payment_date);
            $loan_repayment_schedule->month = $date[1];
            $loan_repayment_schedule->year = $date[0];
            //determine which method to use
            //flat  method
            if ($loan->interest_methodology == 'flat') {
                $principal = round($loan->principal / $period, 2);
                $interest = round($interest_rate * $loan->principal, 2);
                if ($loan->grace_on_interest_charged >= $i) {
                    $loan_repayment_schedule->interest = 0;
                } else {
                    $loan_repayment_schedule->interest = $interest;
                }
                if ($i == $period) {
                    //account for values lost during rounding
                    $loan_repayment_schedule->principal = round($balance, 2);
                } else {
                    $loan_repayment_schedule->principal = $principal;
                }
                //determine next balance
                $balance = ($balance - $principal);
            }
            //reducing balance
            if ($loan->interest_methodology == 'declining_balance') {
                if ($loan->amortization_method == 'equal_installments') {
                    $amortized_payment = round(determine_amortized_payment($interest_rate, $loan->principal, $period), 2);
                    //determine if we have grace period for interest
                    $interest = round($interest_rate * $balance, 2);
                    $principal = round(($amortized_payment - $interest), 2);
                    if ($loan->grace_on_interest_charged >= $i) {
                        $loan_repayment_schedule->interest = 0;
                    } else {
                        $loan_repayment_schedule->interest = $interest;
                    }
                    if ($i == $period) {
                        //account for values lost during rounding
                        $loan_repayment_schedule->principal = round($balance, 2);
                    } else {
                        $loan_repayment_schedule->principal = $principal;
                    }
                    //determine next balance
                    $balance = ($balance - $principal);
                }
                if ($loan->amortization_method == 'equal_principal_payments') {
                    $principal = round($loan->principal / $period, 2);
                    //determine if we have grace period for interest
                    $interest = round($interest_rate * $balance, 2);
                    if ($loan->grace_on_interest_charged >= $i) {
                        $loan_repayment_schedule->interest = 0;
                    } else {
                        $loan_repayment_schedule->interest = $interest;
                    }
                    if ($i == $period) {
                        //account for values lost during rounding
                        $loan_repayment_schedule->principal = round($balance, 2);
                    } else {
                        $loan_repayment_schedule->principal = $principal;
                    }
                    //determine next balance
                    $balance = ($balance - $principal);
                }
            }
            $payment_from_date = Carbon::parse($next_payment_date)->add(1, 'day')->format("Y-m-d");
            if ($loan->repayment_frequency_type == 'months') {
                $next_payment_date = Carbon::parse($next_payment_date)->addMonthsNoOverflow($loan->repayment_frequency)->format("Y-m-d");
            } else {
                $next_payment_date = Carbon::parse($next_payment_date)->add($loan->repayment_frequency, $loan->repayment_frequency_type)->format("Y-m-d");
            }
            $total_principal = $total_principal + $loan_repayment_schedule->principal;
            $total_interest = $total_interest + $loan_repayment_schedule->interest;
            $loan_repayment_schedule->total_due = $loan_repayment_schedule->principal + $loan_repayment_schedule->interest;
            $loan_repayment_schedule->save();
        }
        $loan->expected_maturity_date = $next_payment_date;
        $loan->principal_disbursed_derived = $total_principal;
        $loan->interest_disbursed_derived = $total_interest;

        $loan_history = new LoanHistory();
        $loan_history->loan_id = $loan->id;
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Disbursed';
        $loan_history->save();
        //add disbursal transaction
        $loan_transaction = new LoanTransaction();
        $loan_transaction->created_by_id = Auth::id();
        $loan_transaction->loan_id = $loan->id;
        $loan_transaction->branch_id = $loan->branch_id;
        $loan_transaction->payment_detail_id = $payment_detail->id;
        $loan_transaction->name = trans_choice('loan::general.disbursement', 1);
        $loan_transaction->loan_transaction_type_id = 1;
        $loan_transaction->submitted_on = $loan->disbursed_on_date;
        $loan_transaction->created_on = date("Y-m-d");
        $loan_transaction->amount = $loan->principal;
        $loan_transaction->debit = $loan->principal;
        $disbursal_transaction_id = $loan_transaction->id;
        $loan_transaction->save();
        //add interest transaction
        $loan_transaction = new LoanTransaction();
        $loan_transaction->created_by_id = Auth::id();
        $loan_transaction->loan_id = $loan->id;
        $loan_transaction->branch_id = $loan->branch_id;
        $loan_transaction->name = trans_choice('loan::general.interest', 1) . ' ' . $loan_transaction->name = trans_choice('loan::general.applied', 1);
        $loan_transaction->loan_transaction_type_id = 11;
        $loan_transaction->submitted_on = $loan->disbursed_on_date;
        $loan_transaction->created_on = date("Y-m-d");
        $loan_transaction->amount = $total_interest;
        $loan_transaction->debit = $total_interest;
        $loan_transaction->save();
        $installment_fees = 0;
        $disbursement_fees = 0;
        foreach ($loan->charges as $key) {
            //specified due date
            if ($key->loan_charge_type_id == 2) {
                if ($key->loan_charge_option_id == 1) {
                    $key->calculated_amount = $key->amount;
                    $installment_fees = $installment_fees + $key->calculated_amount;
                }
            }
            //disbursement
            if ($key->loan_charge_type_id == 1) {
                if ($key->loan_charge_option_id == 1) {
                    $key->calculated_amount = $key->amount;
                    $key->amount_paid_derived = $key->calculated_amount;
                    $key->is_paid = 1;
                    $disbursement_fees = $disbursement_fees + $key->calculated_amount;
                }
                if ($key->loan_charge_option_id == 2) {
                    $key->calculated_amount = round(($key->amount * $total_principal / 100), 2);
                    $key->amount_paid_derived = $key->calculated_amount;
                    $key->is_paid = 1;
                    $disbursement_fees = $disbursement_fees + $key->calculated_amount;
                }
                if ($key->loan_charge_option_id == 3) {
                    $key->calculated_amount = round(($key->amount * ($total_interest + $total_principal) / 100), 2);
                    $key->amount_paid_derived = $key->calculated_amount;
                    $key->is_paid = 1;
                    $disbursement_fees = $disbursement_fees + $key->calculated_amount;
                }
                if ($key->loan_charge_option_id == 4) {
                    $key->calculated_amount = round(($key->amount * $total_interest / 100), 2);
                    $key->amount_paid_derived = $key->calculated_amount;
                    $key->is_paid = 1;
                    $disbursement_fees = $disbursement_fees + $key->calculated_amount;
                }
                if ($key->loan_charge_option_id == 5) {
                    $key->calculated_amount = round(($key->amount * $total_principal / 100), 2);
                    $key->amount_paid_derived = $key->calculated_amount;
                    $key->is_paid = 1;
                    $disbursement_fees = $disbursement_fees + $key->calculated_amount;
                }
                if ($key->loan_charge_option_id == 6) {
                    $key->calculated_amount = round(($key->amount * $total_principal / 100), 2);
                    $key->amount_paid_derived = $key->calculated_amount;
                    $key->is_paid = 1;
                    $disbursement_fees = $disbursement_fees + $key->calculated_amount;
                }
                if ($key->loan_charge_option_id == 7) {
                    $key->calculated_amount = round(($key->amount * $loan->principal / 100), 2);
                    $key->amount_paid_derived = $key->calculated_amount;
                    $key->is_paid = 1;
                    $disbursement_fees = $disbursement_fees + $key->calculated_amount;
                }
            }


            //installment_fee
            if ($key->loan_charge_type_id == 3) {
                if ($key->loan_charge_option_id == 1) {
                    $key->calculated_amount = $key->amount;
                    $installment_fees = $installment_fees + $key->calculated_amount;
                }
                if ($key->loan_charge_option_id == 2) {
                    $key->calculated_amount = round(($key->amount * $total_principal / 100), 2);
                    $installment_fees = $installment_fees + $key->calculated_amount;
                }
                if ($key->loan_charge_option_id == 3) {
                    $key->calculated_amount = round(($key->amount * ($total_interest + $total_principal) / 100), 2);
                    $installment_fees = $installment_fees + $key->calculated_amount;
                }
                //here
                if ($key->loan_charge_option_id == 4) {
                    $key->calculated_amount = round(($key->amount * ($loan->principal * $interest_rate)), 2);
                    $installment_fees = $installment_fees + $key->calculated_amount;
                }
                if ($key->loan_charge_option_id == 5) {
                    $key->calculated_amount = round(($key->amount * $total_principal / 100), 2);
                    $installment_fees = $installment_fees + $key->calculated_amount;
                }
                if ($key->loan_charge_option_id == 6) {
                    $key->calculated_amount = round(($key->amount * $total_principal / 100), 2);
                    $installment_fees = $installment_fees + $key->calculated_amount;
                }
                if ($key->loan_charge_option_id == 7) {
                    $key->calculated_amount = round(($key->amount * $loan->principal), 2);
                    $installment_fees = $installment_fees + $key->calculated_amount;
                }
                //create transaction
                $loan_transaction = new LoanTransaction();
                $loan_transaction->created_by_id = Auth::id();
                $loan_transaction->loan_id = $loan->id;
                $loan_transaction->branch_id = $loan->branch_id;
                $loan_transaction->name = trans_choice('loan::general.fee', 1) . ' ' . $loan_transaction->name = trans_choice('loan::general.applied', 1);
                $loan_transaction->loan_transaction_type_id = 10;
                $loan_transaction->submitted_on = $loan->disbursed_on_date;
                $loan_transaction->created_on = date("Y-m-d");
                $loan_transaction->amount = $key->calculated_amount;
                $loan_transaction->debit = $key->calculated_amount;
                $loan_transaction->reversible = 1;
                $loan_transaction->save();
                $key->loan_transaction_id = $loan_transaction->id;
                $key->save();
                //add the charges to the schedule
                //work

                foreach ($loan->repayment_schedules as $loan_repayment_schedule) {
                    $ip = 0;
                    if ($key->loan_charge_option_id == 2) {
                        $loan_repayment_schedule->fees = $loan_repayment_schedule->fees + round(($key->amount * $loan_repayment_schedule->principal / 100), 2);
                    } elseif ($key->loan_charge_option_id == 3) {
                        $loan_repayment_schedule->fees = $loan_repayment_schedule->fees + round(($key->amount * ($loan_repayment_schedule->interest + $loan_repayment_schedule->principal) / 100), 2);
                    } elseif ($key->loan_charge_option_id == 4) {
                        $loan_repayment_schedule->fees = $loan_repayment_schedule->fees + $key->calculated_amount;
                    } else {
                        $loan_repayment_schedule->fees = $loan_repayment_schedule->fees + $key->calculated_amount;
                    }
                    $loan_repayment_schedule->total_due = $loan_repayment_schedule->principal + $loan_repayment_schedule->interest + $loan_repayment_schedule->fees;
                    $loan_repayment_schedule->save();
                    $ip++;
                    if ($ip == 1) {
                        break;
                    }
                }
            }
        }
        if ($disbursement_fees > 0) {
            $loan_transaction = new LoanTransaction();
            $loan_transaction->created_by_id = Auth::id();
            $loan_transaction->loan_id = $loan->id;
            $loan_transaction->branch_id = $loan->branch_id;
            $loan_transaction->name = trans_choice('loan::general.disbursement', 1) . ' ' . $loan_transaction->name = trans_choice('loan::general.charge', 2);
            $loan_transaction->loan_transaction_type_id = 5;
            $loan_transaction->submitted_on = $loan->disbursed_on_date;
            $loan_transaction->created_on = date("Y-m-d");
            $loan_transaction->amount = $disbursement_fees;
            $loan_transaction->credit = $disbursement_fees;
            $loan_transaction->fees_repaid_derived = $disbursement_fees;
            $loan_transaction->save();
            $disbursement_fees_transaction_id = $loan_transaction->id;
        }
        $loan->disbursement_charges = $disbursement_fees;
        $loan->save();
        //check if accounting is enabled
        if ($loan->loan_product->accounting_rule == "cash" || $loan->loan_product->accounting_rule == "accrual_periodic" || $loan->loan_product->accounting_rule == "accrual_upfront") {
            //loan disbursal
            //credit account
            $journal_entry = new JournalEntry();
            $journal_entry->created_by_id = Auth::id();
            $journal_entry->payment_detail_id = $payment_detail->id;
            $journal_entry->transaction_number = 'L' . $disbursal_transaction_id;
            $journal_entry->branch_id = $loan->branch_id;
            $journal_entry->currency_id = $loan->currency_id;
            $journal_entry->chart_of_account_id = $loan->loan_product->fund_source_chart_of_account_id;
            $journal_entry->transaction_type = 'loan_disbursement';
            $journal_entry->date = $loan->disbursed_on_date;
            $date = explode('-', $loan->disbursed_on_date);
            $journal_entry->month = $date[1];
            $journal_entry->year = $date[0];
            $journal_entry->credit = $loan->principal;
            $journal_entry->reference = $loan->id;
            $journal_entry->save();
            //debit account
            $journal_entry = new JournalEntry();
            $journal_entry->created_by_id = Auth::id();
            $journal_entry->transaction_number = 'L' . $disbursal_transaction_id;
            $journal_entry->payment_detail_id = $payment_detail->id;
            $journal_entry->branch_id = $loan->branch_id;
            $journal_entry->currency_id = $loan->currency_id;
            $journal_entry->chart_of_account_id = $loan->loan_product->loan_portfolio_chart_of_account_id;
            $journal_entry->transaction_type = 'loan_disbursement';
            $journal_entry->date = $loan->disbursed_on_date;
            $date = explode('-', $loan->disbursed_on_date);
            $journal_entry->month = $date[1];
            $journal_entry->year = $date[0];
            $journal_entry->debit = $loan->principal;
            $journal_entry->reference = $loan->id;
            $journal_entry->save();
            //
            if ($disbursement_fees > 0) {
                //credit account
                $journal_entry = new JournalEntry();
                $journal_entry->created_by_id = Auth::id();
                $journal_entry->payment_detail_id = $payment_detail->id;
                $journal_entry->transaction_number = 'L' . $disbursement_fees_transaction_id;
                $journal_entry->branch_id = $loan->branch_id;
                $journal_entry->currency_id = $loan->currency_id;
                $journal_entry->chart_of_account_id = $loan->loan_product->income_from_fees_chart_of_account_id;
                $journal_entry->transaction_type = 'repayment_at_disbursement';
                $journal_entry->date = $loan->disbursed_on_date;
                $date = explode('-', $loan->disbursed_on_date);
                $journal_entry->month = $date[1];
                $journal_entry->year = $date[0];
                $journal_entry->credit = $loan->principal;
                $journal_entry->reference = $loan->id;
                $journal_entry->save();
                //debit account
                $journal_entry = new JournalEntry();
                $journal_entry->created_by_id = Auth::id();
                $journal_entry->transaction_number = 'L' . $disbursement_fees_transaction_id;
                $journal_entry->payment_detail_id = $payment_detail->id;
                $journal_entry->branch_id = $loan->branch_id;
                $journal_entry->currency_id = $loan->currency_id;
                $journal_entry->chart_of_account_id = $loan->loan_product->fund_source_chart_of_account_id;
                $journal_entry->transaction_type = 'repayment_at_disbursement';
                $journal_entry->date = $loan->disbursed_on_date;
                $date = explode('-', $loan->disbursed_on_date);
                $journal_entry->month = $date[1];
                $journal_entry->year = $date[0];
                $journal_entry->debit = $loan->principal;
                $journal_entry->reference = $loan->id;
                $journal_entry->save();
            }
        }
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Disburse Loan');
        //fire loan status changed event
        event(new LoanStatusChanged($loan, $previous_status));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $loan->id . '/show');
    }

    public function undo_disbursement(Request $request, $id)
    {

        $loan = Loan::find($id);
        $previous_status = $loan->status;
        $loan->disbursed_by_user_id = null;
        $loan->disbursed_on_date = null;
        $loan->status = 'approved';
        $loan->disbursed_notes = null;
        $loan->save();
        //destroy loan repayment schedules
        LoanLinkedCharge::where('loan_id', $loan->id)->update(["loan_transaction_id" => null]);
        LoanRepaymentSchedule::where('loan_id', $loan->id)->delete();
        LoanTransaction::where('loan_id', $loan->id)->delete();
        //reverse journal entries
        JournalEntry::whereIn('transaction_type', ['repayment_at_disbursement', 'loan_disbursement', 'loan_repayment'])->where('reference', $loan->id)->update(["reversed" => 1]);
        $loan_history = new LoanHistory();
        $loan_history->loan_id = $loan->id;
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Undisbursed';
        $loan_history->save();
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Undo Loan Disbursement');
        //fire loan status changed event
        event(new LoanStatusChanged($loan, $previous_status));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $loan->id . '/show');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */


    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
         $loan = Loan::find($id);
        $kyc = KYC::Where('loan_id', $id);
        $bussiness_plan = BussinessPlan::where('loan_id', $id);
        $source_captial = SourceCaptial::where('loan_id', $id);
        $financial_plan = FinancialPlan::where('loan_id', $id);
        $others =  Others::where('loan_id', $id);
        $loan_linked_charge = LoanLinkedCharge::where('loan_id',$id);
        $loan_history = new LoanHistory();
        $loan_history->loan_id = $loan->id;
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Deleted';
        $loan_history->save();
        $loan->delete();
        $kyc->delete();
        $bussiness_plan->delete();
        $source_captial->delete();
        $financial_plan->delete();
        $loan_linked_charge->delete();
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Delete Loan');
        \flash(trans_choice("core::general.successfully_deleted", 1))->success()->important();
        //fire loan status changed event
        event(new LoanStatusChanged($loan));
        return redirect('/loan');
    }

    //transactions
    public function show_transaction($id)
    {
        $loan_transaction = LoanTransaction::with('payment_detail')->with('loan')->find($id);
        return theme_view('loan::loan_transaction.show', compact('loan_transaction'));
    }

    public function pdf_transaction($id)
    {
        $loan_transaction = LoanTransaction::with('payment_detail')->with('loan')->find($id);
        $pdf = PDF::loadView(theme_view_file('loan::loan_transaction.pdf'), compact('loan_transaction'));
        return $pdf->download(trans_choice('loan::general.transaction', 1) . ' ' . trans_choice('loan::general.detail', 2) . ".pdf");
    }

    public function print_transaction($id)
    {
        $loan_transaction = LoanTransaction::with('payment_detail')->with('loan')->find($id);
        return theme_view('loan::loan_transaction.print', compact('loan_transaction'));
    }

    //schedules
    public function email_schedule($id)
    {
        $loan = Loan::with('repayment_schedules')->find($id);
        //return theme_view('loan::loan_schedule.email', compact('loan'));
    }

    public function pdf_schedule($id)
    {
        $loan = Loan::with('repayment_schedules')->find($id);
        $pdf = PDF::loadView(theme_view_file('loan::loan_schedule.pdf'), compact('loan'))->setPaper('a4', 'landscape');
        return $pdf->download(trans_choice('loan::general.repayment', 1) . ' ' . trans_choice('loan::general.schedule', 1) . ".pdf");
    }

    public function print_schedule($id)
    {
        $loan = Loan::with('repayment_schedules')->find($id);
        return theme_view('loan::loan_schedule.print', compact('loan'));
    }

    //repayments
    public function create_repayment($id)
    {
        $payment_types = PaymentType::where('active', 1)->get();
        $custom_fields = CustomField::where('category', 'add_repayment')->where('active', 1)->get();
        return theme_view('loan::loan_repayment.create', compact('id', 'payment_types', 'custom_fields'));
    }

    public function store_repayment(Request $request, $id)
    {

        $request->validate([
            'amount' => ['required', 'numeric'],
            'date' => ['required', 'date'],
            'payment_type_id' => ['required'],
        ]);
        $loan = Loan::with('loan_product')->find($id);
        //payment details
        $payment_detail = new PaymentDetail();
        $payment_detail->created_by_id = Auth::id();
        $payment_detail->payment_type_id = $request->payment_type_id;
        $payment_detail->transaction_type = 'loan_transaction';
        $payment_detail->cheque_number = $request->cheque_number;
        $payment_detail->receipt = $request->receipt;
        $payment_detail->account_number = $request->account_number;
        $payment_detail->bank_name = $request->bank_name;
        $payment_detail->routing_code = $request->routing_code;
        $payment_detail->description = $request->description;
        $payment_detail->save();
        $loan_transaction = new LoanTransaction();
        $loan_transaction->created_by_id = Auth::id();
        //new changes
        $loan_transaction->coop_id = Auth::user()->coop_id;
        $loan_transaction->loan_id = $loan->id;
        $loan_transaction->payment_detail_id = $payment_detail->id;
        $loan_transaction->name = trans_choice('loan::general.repayment', 1);
        $loan_transaction->loan_transaction_type_id = 2;
        $loan_transaction->submitted_on = $request->date;
        $loan_transaction->created_on = date("Y-m-d");
        $loan_transaction->amount = $request->amount;
        $loan_transaction->credit = $request->amount;
        $loan_transaction->save();
        activity()->on($loan_transaction)
            ->withProperties(['id' => $loan_transaction->id])
            ->log('Create Loan Repayment');
        //fire transaction updated event
        event(new TransactionUpdated($loan));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $loan->id . '/show');
    }

    public function edit_repayment($id)
    {
        $loan_transaction = LoanTransaction::find($id);
        $payment_types = PaymentType::where('active', 1)->get();
        $custom_fields = CustomField::where('category', 'add_repayment')->where('active', 1)->get();
        return theme_view('loan::loan_repayment.edit', compact('loan_transaction', 'payment_types', 'custom_fields'));
    }

    public function update_repayment(Request $request, $id)
    {

        $request->validate([
            'amount' => ['required', 'numeric'],
            'date' => ['required', 'date'],
            'payment_type_id' => ['required'],
        ]);
        $loan_transaction = LoanTransaction::find($id);
        $loan = $loan_transaction->loan;
        //payment details
        $payment_detail = PaymentDetail::find($loan_transaction->payment_detail_id);
        $payment_detail->payment_type_id = $request->payment_type_id;
        $payment_detail->cheque_number = $request->cheque_number;
        $payment_detail->receipt = $request->receipt;
        $payment_detail->account_number = $request->account_number;
        $payment_detail->bank_name = $request->bank_name;
        $payment_detail->routing_code = $request->routing_code;
        $payment_detail->description = $request->description;
        $payment_detail->save();
        $loan_transaction->submitted_on = $request->date;
        $loan_transaction->amount = $request->amount;
        $loan_transaction->credit = $request->amount;
        $loan_transaction->save();
        activity()->on($loan_transaction)
            ->withProperties(['id' => $loan_transaction->id])
            ->log('Update Loan Repayment');
        //fire transaction updated event
        event(new TransactionUpdated($loan));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $loan->id . '/show');
    }

    public function waive_charge(Request $request, $id)
    {

        $loan_linked_charge = LoanLinkedCharge::with('loan')->with('transaction')->find($id);
        $loan_linked_charge->waived = 1;
        $loan_linked_charge->save();
        $loan = $loan_linked_charge->loan;
        $loan_transaction = $loan_linked_charge->transaction;
        $loan_transaction->credit = $loan_transaction->amount;
        $loan_transaction->debit = $loan_transaction->amount;
        $loan_transaction->reversed = 1;
        $loan_transaction->save();
        if ($loan_linked_charge->loan_charge_type_id == 2 || $loan_linked_charge->loan_charge_type_id == 4 || $loan_linked_charge->loan_charge_type_id == 6 || $loan_linked_charge->loan_charge_type_id == 2 || $loan_linked_charge->loan_charge_type_id == 7 || $loan_linked_charge->loan_charge_type_id == 8) {
            $repayment_schedule = LoanRepaymentSchedule::where('loan_id', $loan->id)->where('due_date', $loan_transaction->due_date)->first();
            if ($loan_linked_charge->is_penalty == 1) {
                $repayment_schedule->penalties_waived_derived = $repayment_schedule->penalties_waived_derived + $loan_linked_charge->calculated_amount;
            } else {
                $repayment_schedule->fees_waived_derived = $repayment_schedule->fees_waived_derived + $loan_linked_charge->calculated_amount;
            }
            $repayment_schedule->save();
        }
        if ($loan_linked_charge->loan_charge_type_id == 3) {
            $amount = 0;
            foreach ($loan->repayment_schedules as $repayment_schedule) {
                if ($loan_linked_charge->loan_charge_option_id == 1) {
                    $amount = $loan_linked_charge->calculated_amount;
                }
                if ($loan_linked_charge->loan_charge_option_id == 2) {
                    $amount = round(($loan_linked_charge->amount * $repayment_schedule->principal / 100), 2);
                }
                if ($loan_linked_charge->loan_charge_option_id == 3) {
                    $amount = round(($loan_linked_charge->amount * ($repayment_schedule->interest + $repayment_schedule->principal) / 100), 2);
                }
                if ($loan_linked_charge->loan_charge_option_id == 4) {
                    $amount = round(($loan_linked_charge->amount * $repayment_schedule->interest / 100), 2);
                }
                if ($loan_linked_charge->loan_charge_option_id == 5) {
                    $amount = round(($loan_linked_charge->amount * $loan->principal / 100), 2);
                }
                if ($loan_linked_charge->loan_charge_option_id == 6) {
                    $amount = round(($loan_linked_charge->amount * $loan->principal / 100), 2);
                }
                if ($loan_linked_charge->loan_charge_option_id == 7) {
                    $amount = round(($loan_linked_charge->amount * $loan->principal / 100), 2);
                }
                $repayment_schedule->fees_waived_derived = $repayment_schedule->fees_waived_derived + $amount;
                $repayment_schedule->save();
            }
        }
        activity()->on($loan_linked_charge)
            ->withProperties(['id' => $loan_linked_charge->id])
            ->log('Waive Loan Charge');
        //fire transaction updated event
        event(new TransactionUpdated($loan));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $loan->id . '/show');
    }

    public function create_loan_linked_charge($id)
    {
        $loan = Loan::with('loan_product')->with('loan_product.charges')->with('loan_product.charges.charge')->find($id);
        
        $charges = [];
        foreach ($loan->loan_product->charges as $key) {
            if ($key->charge->loan_charge_type_id == 2) {
                $charges[$key->charge->id] = $key->charge;
            }
        }
        JavaScript::put([
            'charges' =>LoanCharge::get(),
        ]);
        return theme_view('loan::loan_linked_charge.create', compact('loan', 'charges'));
    }

    public function store_loan_linked_charge(Request $request, $id)
    {
        $loan = Loan::with('repayment_schedules')->find($id);
        $request->validate([
            'amount' => ['required'],
            'loan_charge_id' => ['required'],
            'date' => ['required', 'date'],
        ]);
        $loan_charge = LoanCharge::find($request->loan_charge_id);
        $loan_linked_charge = new LoanLinkedCharge();
        $loan_linked_charge->loan_id = $loan->id;
        $loan_linked_charge->loan_charge_id = $loan_charge->id;
        $loan_linked_charge->name = $loan_charge->name;
        if ($loan_charge->allow_override == 1) {
            $loan_linked_charge->amount = $request->amount;
        } else {
            $loan_linked_charge->amount = $loan_charge->amount;
        }
        $loan_linked_charge->loan_charge_type_id = $loan_charge->loan_charge_type_id;
        $loan_linked_charge->loan_charge_option_id = $loan_charge->loan_charge_option_id;
        $loan_linked_charge->is_penalty = $loan_charge->is_penalty;
        $loan_linked_charge->save();
        //find schedule to apply this charge
        $repayment_schedule = $loan->repayment_schedules->where('due_date', '>=', $request->date)->where('from_date', '<=', $request->date)->first();
        if (empty($repayment_schedule)) {
            if (Carbon::parse($request->date)->lessThan($loan->first_payment_date)) {
                $repayment_schedule = $loan->repayment_schedules->first();
            } else {
                $repayment_schedule = $loan->repayment_schedules->last();
            }
        }
        //calculate the amount
        if ($loan_linked_charge->loan_charge_option_id == 1) {
            $amount = $loan_linked_charge->amount;
        }
        if ($loan_linked_charge->loan_charge_option_id == 2) {
            $amount = round(($loan_linked_charge->amount * ($repayment_schedule->principal - $repayment_schedule->principal_repaid_derived - $repayment_schedule->principal_written_off_derived) / 100), 2);
        }
        if ($loan_linked_charge->loan_charge_option_id == 3) {
            $amount = round(($loan_linked_charge->amount * (($repayment_schedule->interest - $repayment_schedule->interest_repaid_derived - $repayment_schedule->interest_waived_derived - $repayment_schedule->interest_written_off_derived) + ($repayment_schedule->principal - $repayment_schedule->principal_repaid_derived - $repayment_schedule->principal_written_off_derived)) / 100), 2);
        }
        if ($loan_linked_charge->loan_charge_option_id == 4) {
            $amount = round(($loan_linked_charge->amount * ($repayment_schedule->interest - $repayment_schedule->interest_repaid_derived - $repayment_schedule->interest_waived_derived - $repayment_schedule->interest_written_off_derived) / 100), 2);
        }
        if ($loan_linked_charge->loan_charge_option_id == 5) {
            $amount = round(($loan_linked_charge->amount * ($loan->repayment_schedules->sum('principal') - $loan->repayment_schedules->sum('principal_repaid_derived') - $loan->repayment_schedules->sum('principal_written_off_derived')) / 100), 2);
        }
        if ($loan_linked_charge->loan_charge_option_id == 6) {
            $amount = round(($loan_linked_charge->amount * $loan->principal / 100), 2);
        }
        if ($loan_linked_charge->loan_charge_option_id == 7) {
            $amount = round(($loan_linked_charge->amount * $loan->principal / 100), 2);
        }
        $repayment_schedule->fees = $repayment_schedule->fees + $amount;
        $repayment_schedule->save();
        $loan_linked_charge->calculated_amount = $amount;
        $loan_linked_charge->due_date = $repayment_schedule->due_date;
        //create transaction
        $loan_transaction = new LoanTransaction();
        $loan_transaction->created_by_id = Auth::id();
        $loan_transaction->loan_id = $loan->id;
        $loan_transaction->name = trans_choice('loan::general.fee', 1) . ' ' . $loan_transaction->name = trans_choice('loan::general.applied', 1);
        $loan_transaction->loan_transaction_type_id = 10;
        $loan_transaction->submitted_on = $repayment_schedule->due_date;
        $loan_transaction->created_on = date("Y-m-d");
        $loan_transaction->amount = $loan_linked_charge->calculated_amount;
        $loan_transaction->due_date = $repayment_schedule->due_date;
        $loan_transaction->debit = $loan_linked_charge->calculated_amount;
        $loan_transaction->reversible = 1;
        $loan_transaction->save();
        $loan_linked_charge->loan_transaction_id = $loan_transaction->id;
        $loan_linked_charge->save();
        activity()->on($loan_charge)
            ->withProperties(['id' => $loan_charge->id])
            ->log('Create Loan Charge');
        //fire transaction updated event
        event(new TransactionUpdated($loan));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $id . '/show');
    }

    public function waive_interest(Request $request, $id)
    {
        $loan = Loan::with('repayment_schedules')->find($id);
        $request->validate([
            'interest_waived_amount' => ['required'],
            'date' => ['required', 'date'],
        ]);

        //find schedule to apply this charge
        $repayment_schedule = $loan->repayment_schedules->where('due_date', '>=', $request->date)->where('from_date', '<=', $request->date)->first();
        if (empty($repayment_schedule)) {
            if (Carbon::parse($request->date)->lessThan($loan->first_payment_date)) {
                $repayment_schedule = $loan->repayment_schedules->first();
            } else {
                $repayment_schedule = $loan->repayment_schedules->last();
            }
        }
        $amount = $request->interest_waived_amount;
        foreach ($loan->repayment_schedules->where('due_date', '>=', $repayment_schedule->due_date) as $repayment_schedule) {
            $interest = $repayment_schedule->interest - $repayment_schedule->interest_written_off_derived - $repayment_schedule->interest_repaid_derived - $repayment_schedule->interest_waived_derived;
            if ($interest <= 0) {
                continue;
            }
            if ($amount >= $interest) {
                $repayment_schedule->interest_waived_derived = $repayment_schedule->interest_waived_derived + $interest;
                $amount = $amount - $interest;
            } else {
                $repayment_schedule->interest_waived_derived = $repayment_schedule->interest_waived_derived + $amount;
                $amount = 0;
            }
            $repayment_schedule->save();
            if ($amount <= 0) {
                break;
            }
        }
        $repayment_schedule->fees = $repayment_schedule->fees + $amount;
        $repayment_schedule->save();
        //create transaction
        $loan_transaction = new LoanTransaction();
        $loan_transaction->created_by_id = Auth::id();
        $loan_transaction->loan_id = $loan->id;
        $loan_transaction->name = trans_choice('loan::general.waive', 1) . ' ' . $loan_transaction->name = trans_choice('loan::general.interest', 1);
        $loan_transaction->loan_transaction_type_id = 4;
        $loan_transaction->submitted_on = $request->date;
        $loan_transaction->created_on = date("Y-m-d");
        $loan_transaction->amount = $request->interest_waived_amount;
        $loan_transaction->credit = $request->interest_waived_amount;
        $loan_transaction->reversible = 0;
        $loan_transaction->save();
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Waive Loan Interest');
        //fire transaction updated event
        event(new TransactionUpdated($loan));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $id . '/show');
    }

    public function reverse_repayment(Request $request, $id)
    {

        $loan_transaction = LoanTransaction::find($id);
        $loan = $loan_transaction->loan;

        $loan_transaction->amount = 0;
        $loan_transaction->debit = $loan_transaction->credit;
        $loan_transaction->reversed = 1;
        $loan_transaction->save();
        activity()->on($loan_transaction)
            ->withProperties(['id' => $loan_transaction->id])
            ->log('Reverse Loan Repayment');
        //fire transaction updated event
        event(new TransactionUpdated($loan));
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect('loan/' . $loan->id . '/show');
    }

    public function create_loan_calculator()
    {
        $loan_products = LoanProduct::where('active', 1)->get();
        JavaScript::put([
            'loan_products' => $loan_products
        ]);
        return theme_view('loan::loan_calculator.create', compact('loan_products'));
    }

    public function process_loan_calculator(Request $request)
    {
        $loan_product = LoanProduct::with('charges')->with('charges.charge')->find($request->loan_product_id);
        $loan_details = [];
        $loan_details['principal'] = $request->applied_amount;
        $loan_details['disbursement_date'] = $request->expected_disbursement_date;

        $schedules = [];
        $loan_principal = $request->applied_amount;
        $interest_rate = determine_period_interest_rate($request->interest_rate, $request->repayment_frequency_type, 'year');
        $balance = round($loan_principal, $loan_product->decimals);
        $period = ($request->loan_term / $request->repayment_frequency);
        $payment_from_date = $request->expected_disbursement_date;
        $next_payment_date = $request->expected_first_payment_date;
        $total_principal = 0;
        $total_interest = 0;
        for ($i = 1; $i <= $period; $i++) {
            $schedule = [];

            $schedule['installment'] = $i;
            $schedule['due_date'] = $next_payment_date;
            $schedule['from_date'] = $payment_from_date;
            $schedule['fees'] = 0;

            //flat  method
            if ($loan_product->interest_methodology == 'flat') {
                $principal = round($loan_principal / $period, $loan_product->decimals);
                $interest = round($interest_rate * $loan_principal, $loan_product->decimals);
                if ($loan_product->grace_on_interest_charged >= $i) {
                    $schedule['interest'] = 0;
                } else {
                    $schedule['interest'] = $interest;
                }
                if ($i == $period) {
                    //account for values lost during rounding
                    $schedule['principal'] = round($balance, $loan_product->decimals);
                } else {
                    $schedule['principal'] = $principal;
                }
                //determine next balance
                $balance = ($balance - $principal);
            }
            //reducing balance
            if ($loan_product->interest_methodology == 'declining_balance') {
                if ($loan_product->amortization_method == 'equal_installments') {
                    $amortized_payment = round(determine_amortized_payment($interest_rate, $loan_principal, $period), $loan_product->decimals);
                    //determine if we have grace period for interest
                    $interest = round($interest_rate * $balance, $loan_product->decimals);
                    $principal = round(($amortized_payment - $interest), $loan_product->decimals);
                    if ($loan_product->grace_on_interest_charged >= $i) {
                        $schedule['interest'] = 0;
                    } else {
                        $schedule['interest'] = $interest;
                    }
                    if ($i == $period) {
                        //account for values lost during rounding
                        $schedule['principal'] = round($balance, $loan_product->decimals);
                    } else {
                        $schedule['principal'] = $principal;
                    }
                    //determine next balance
                    $balance = ($balance - $principal);
                }
                if ($loan_product->amortization_method == 'equal_principal_payments') {
                    $principal = round($loan_principal / $period, $loan_product->decimals);
                    //determine if we have grace period for interest
                    $interest = round($interest_rate * $balance, $loan_product->decimals);
                    if ($loan_product->grace_on_interest_charged >= $i) {
                        $schedule['interest'] = 0;
                    } else {
                        $schedule['interest'] = $interest;
                    }
                    if ($i == $period) {
                        //account for values lost during rounding
                        $schedule['principal'] = round($balance, $loan_product->decimals);
                    } else {
                        $schedule['principal'] = $principal;
                    }
                    //determine next balance
                    $balance = ($balance - $principal);
                }
            }
            $payment_from_date = Carbon::parse($next_payment_date)->add(1, 'day')->format("Y-m-d");
            $next_payment_date = Carbon::parse($next_payment_date)->add($loan_product->repayment_frequency, $loan_product->repayment_frequency_type)->format("Y-m-d");
            $total_principal = $total_principal + $schedule['principal'];
            $total_interest = $total_interest + $schedule['interest'];
            $schedules[] = $schedule;
        }

        $installment_fees = 0;
        $disbursement_fees = 0;
        foreach ($loan_product->charges as $key) {
            //disbursement

            if ($key->charge->loan_charge_type_id == 1) {
                $amount = 0;
                if ($key->charge->loan_charge_option_id == 1) {
                    $amount = $key->charge->amount;
                }
                if ($key->charge->loan_charge_option_id == 2) {
                    $amount = round(($key->charge->amount * $total_principal / 100), $loan_product->decimals);
                }
                if ($key->charge->loan_charge_option_id == 3) {
                    $amount = round(($key->charge->amount * ($total_interest + $total_principal) / 100), $loan_product->decimals);
                }
                if ($key->charge->loan_charge_option_id == 4) {
                    $amount = round(($key->charge->amount * $total_interest / 100), $loan_product->decimals);
                }
                if ($key->charge->loan_charge_option_id == 5) {
                    $amount = round(($key->charge->amount * $total_principal / 100), $loan_product->decimals);
                }
                if ($key->charge->loan_charge_option_id == 6) {
                    $amount = round(($key->charge->amount * $total_principal / 100), $loan_product->decimals);
                }
                if ($key->charge->loan_charge_option_id == 7) {
                    $amount = round(($key->charge->amount * $loan_principal / 100), $loan_product->decimals);
                }
                $disbursement_fees = $disbursement_fees + $amount;
            }
            //installment_fee
            if ($key->charge->loan_charge_type_id == 3) {
                $amount = 0;
                if ($key->charge->loan_charge_option_id == 1) {
                    $amount = $key->charge->amount;
                }
                if ($key->charge->loan_charge_option_id == 2) {
                    $amount = round(($key->charge->amount * $total_principal / 100), $loan_product->decimals);
                }
                if ($key->charge->loan_charge_option_id == 3) {
                    $amount = round(($key->charge->amount * ($total_interest + $total_principal) / 100), $loan_product->decimals);
                }
                if ($key->charge->loan_charge_option_id == 4) {
                    $amount = round(($key->charge->amount * $total_interest / 100), $loan_product->decimals);
                }
                if ($key->charge->loan_charge_option_id == 5) {
                    $amount = round(($key->charge->amount * $total_principal / 100), $loan_product->decimals);
                }
                if ($key->charge->loan_charge_option_id == 6) {
                    $amount = round(($key->charge->amount * $total_principal / 100), $loan_product->decimals);
                }
                if ($key->charge->loan_charge_option_id == 7) {
                    $amount = round(($key->charge->amount * $loan_principal / 100), $loan_product->decimals);
                }
                $installment_fees = $installment_fees + $amount;
                //add the charges to the schedule
                foreach ($schedules as &$temp) {
                    if ($key->charge->loan_charge_option_id == 2) {
                        $temp['fees'] = $temp['fees'] + round(($key->charge->amount * $temp['principal'] / 100), $loan_product->decimals);
                    } elseif ($key->charge->loan_charge_option_id == 3) {
                        $temp['fees'] = $temp['fees'] + round(($key->charge->amount * ($temp['interest'] + $temp['principal']) / 100), $loan_product->decimals);
                    } elseif ($key->charge->loan_charge_option_id == 4) {
                        $temp['fees'] = $temp['fees'] + round(($key->charge->amount * $temp['interest'] / 100), $loan_product->decimals);
                    } else {
                        $temp['fees'] = $temp['fees'] + $key->charge->amount;
                    }
                }
            }
        }
        $loan_details['total_interest'] = $total_interest;
        $loan_details['decimals'] = $loan_product->decimals;
        $loan_details['disbursement_fees'] = $disbursement_fees;
        $loan_details['total_fees'] = $disbursement_fees + $installment_fees;
        $loan_details['total_due'] = $disbursement_fees + $installment_fees + $total_interest + $total_principal;
        $loan_details['maturity_date'] = $next_payment_date;
        activity()->log('Use Loan Calculator');
        return theme_view('loan::loan_calculator.show', compact('loan_details', 'schedules'));
    }

    public function approve_application(Request $request)
    {
        $loan_application = LoanApplication::with('loan_product')->with('client')->first();
        $client = $loan_application->client;
        $loan_product = $loan_application->loan_product;
        $funds = Fund::all();
        $loan_purposes = LoanPurpose::get();
        $users = User::whereHas('roles', function ($query) {
            return $query->where('name', '!=', 'client');
        })->get();
        $charges = [];
        $charges_list = [];
        foreach ($loan_product->charges as $key) {
            if (!empty($key->charge)) {
                //charge type
                array_push($charges_list, ['id' => $key->charge->id, 'text' => $key->charge->name]);
                if ($key->charge->loan_charge_type_id == 1) {
                    $key->charge->loan_charge_type_id = trans_choice('loan::general.disbursement', 1);
                }
                if ($key->charge->loan_charge_type_id == 2) {
                    $key->charge->loan_charge_type_id = trans_choice('loan::general.specified_due_date', 1);
                }
                if ($key->charge->loan_charge_type_id == 3) {
                    $key->charge->loan_charge_type_id = trans_choice('loan::general.installment', 1) . ' ' . trans_choice('loan::general.fee', 2);
                }
                if ($key->charge->loan_charge_type_id == 4) {
                    $key->charge->loan_charge_type_id = trans_choice('loan::general.overdue', 1) . ' ' . trans_choice('loan::general.installment', 1) . ' ' . trans_choice('loan::general.fee', 2);
                }
                if ($key->charge->loan_charge_type_id == 5) {
                    $key->charge->loan_charge_type_id = trans_choice('loan::general.disbursement_paid_with_repayment', 1);
                }
                if ($key->charge->loan_charge_type_id == 6) {
                    $key->charge->loan_charge_type_id = trans_choice('loan::general.loan_rescheduling_fee', 1);
                }
                if ($key->charge->loan_charge_type_id == 7) {
                    $key->charge->loan_charge_type_id = trans_choice('loan::general.overdue_on_loan_maturity', 1);
                }
                if ($key->charge->loan_charge_type_id == 8) {
                    $key->charge->loan_charge_type_id = trans_choice('loan::general.last_installment_fee', 1);
                }
                //charge option
                if ($key->charge->loan_charge_option_id == 1) {
                    $key->charge->loan_charge_option_id = trans_choice('loan::general.flat', 1);
                }
                if ($key->charge->loan_charge_option_id == 2) {
                    $key->charge->loan_charge_option_id = trans_choice('loan::general.principal_due_on_installment', 1);
                }
                if ($key->charge->loan_charge_option_id == 3) {
                    $key->charge->loan_charge_option_id = trans_choice('loan::general.principal_interest_due_on_installment', 1);
                }
                if ($key->charge->loan_charge_option_id == 4) {
                    $key->charge->loan_charge_option_id = trans_choice('loan::general.interest_due_on_installment', 1);
                }
                if ($key->charge->loan_charge_option_id == 5) {
                    $key->charge->loan_charge_option_id = trans_choice('loan::general.total_outstanding_loan_principal', 1);
                }
                if ($key->charge->loan_charge_option_id == 6) {
                    $key->charge->loan_charge_option_id = trans_choice('loan::general.percentage_of_original_loan_principal_per_installment', 1);
                }
                if ($key->charge->loan_charge_option_id == 7) {
                    $key->charge->loan_charge_option_id = trans_choice('loan::general.original_loan_principal', 1);
                }
                $charges[$key->charge->id] = $key;
            }
        }
        JavaScript::put([
            'loan_product' => $loan_product,
            'charges' => $charges,
            'original_charges' => $charges,
            'charges_list' => $charges_list

        ]);

        return theme_view('loan::application.approve', compact('client', 'loan_product', 'users', 'loan_purposes', 'funds', 'loan_application'));
    }

    public function store_approve_application(Request $request, $id)
    {

        $request->validate([
            'fund_id' => ['required'],
            'loan_product_id' => ['required'],
            'client_id' => ['required'],
            'applied_amount' => ['required', 'numeric'],
            'loan_term' => ['required', 'numeric'],
            'repayment_frequency' => ['required', 'numeric'],
            'repayment_frequency_type' => ['required'],
            'interest_rate' => ['required', 'numeric'],
            'expected_disbursement_date' => ['required', 'date'],
            'loan_officer_id' => ['required'],
            'loan_purpose_id' => ['required'],
            'expected_first_payment_date' => ['required', 'date'],
        ]);
        $loan_product = LoanProduct::find($request->loan_product_id);
        $client = Client::find($request->client_id);
        $loan = new Loan();
        $loan->currency_id = $loan_product->currency_id;
        $loan->loan_product_id = $loan_product->id;
        $loan->client_id = $client->id;
        $loan->branch_id = $client->branch_id;
        $loan->loan_transaction_processing_strategy_id = $loan_product->loan_transaction_processing_strategy_id;
        $loan->loan_purpose_id = $request->loan_purpose_id;
        $loan->loan_officer_id = $request->loan_officer_id;
        $loan->expected_disbursement_date = $request->expected_disbursement_date;
        $loan->expected_first_payment_date = $request->expected_first_payment_date;
        $loan->fund_id = $request->fund_id;
        $loan->created_by_id = Auth::id();
        $loan->applied_amount = $request->applied_amount;
        $loan->loan_term = $request->loan_term;
        $loan->repayment_frequency = $request->repayment_frequency;
        $loan->repayment_frequency_type = $request->repayment_frequency_type;
        $loan->interest_rate = $request->interest_rate;
        $loan->interest_rate_type = $loan_product->interest_rate_type;
        $loan->grace_on_principal_paid = $loan_product->grace_on_principal_paid;
        $loan->grace_on_interest_paid = $loan_product->grace_on_interest_paid;
        $loan->grace_on_interest_charged = $loan_product->grace_on_interest_charged;
        $loan->interest_methodology = $loan_product->interest_methodology;
        $loan->amortization_method = $loan_product->amortization_method;
        $loan->auto_disburse = $loan_product->auto_disburse;
        $loan->submitted_on_date = date("Y-m-d");
        $loan->submitted_by_user_id = Auth::id();
        $loan->save();
        //save charges
        if (!empty($request->charges)) {
            foreach ($request->charges as $key => $value) {
                $loan_charge = LoanCharge::find($key);
                $loan_linked_charge = new LoanLinkedCharge();
                $loan_linked_charge->loan_id = $loan->id;
                $loan_linked_charge->name = $loan_charge->name;
                $loan_linked_charge->loan_charge_id = $key;
                if ($loan_charge->allow_override == 1) {
                    $loan_linked_charge->amount = $value;
                } else {
                    $loan_linked_charge->amount = $loan_charge->amount;
                }
                $loan_linked_charge->loan_charge_type_id = $loan_charge->loan_charge_type_id;
                $loan_linked_charge->loan_charge_option_id = $loan_charge->loan_charge_option_id;
                $loan_linked_charge->is_penalty = $loan_charge->is_penalty;
                $loan_linked_charge->save();
            }
        }
        $loan_history = new LoanHistory();
        $loan_history->loan_id = $loan->id;
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Created';
        $loan_history->save();
        $loan_officer_history = new LoanOfficerHistory();
        $loan_officer_history->loan_id = $loan->id;
        $loan_officer_history->created_by_id = Auth::id();
        $loan_officer_history->loan_officer_id = $request->loan_officer_id;
        $loan_officer_history->start_date = date("Y-m-d");
        $loan_officer_history->save();
        //update loan application
        $loan_application = LoanApplication::find($id);
        $loan_application->status = 'approved';
        $loan_application->loan_id = $loan->id;
        $loan_application->save();
        activity()->on($loan_application)
            ->withProperties(['id' => $loan_application->id])
            ->log('Approve Loan Application');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        //fire loan status changed event
        event(new LoanStatusChanged($loan));
        return redirect('loan/' . $loan->id . '/show');
    }

    public function reject_application(Request $request, $id)
    {

        $loan_application = LoanApplication::find($id);
        $loan_application->status = 'rejected';
        $loan_application->save();
        activity()->on($loan_application)
            ->withProperties(['id' => $loan_application->id])
            ->log('Reject Loan Application');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect()->back();
    }

    public function undo_reject_application(Request $request, $id)
    {

        $loan_application = LoanApplication::find($id);
        $loan_application->status = 'pending';
        $loan_application->save();
        activity()->on($loan_application)
            ->withProperties(['id' => $loan_application->id])
            ->log('Undo Loan Application Rejection');
        \flash(trans_choice("core::general.successfully_saved", 1))->success()->important();
        return redirect()->back();
    }
}