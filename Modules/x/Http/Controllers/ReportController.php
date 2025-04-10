<?php

namespace Modules\Loan\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Branch\Entities\Branch;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Entities\LoanProduct;
use Modules\Loan\Exports\LoanExport;
use Modules\User\Entities\User;
use PDF;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:loan.loans.reports.repayments'])->only(['repayment']);
        $this->middleware(['permission:loan.loans.reports.collection_sheet'])->only(['collection_sheet']);
        $this->middleware(['permission:loan.loans.reports.mfi_officer_collection_sheet'])->only(['mfi_officer_collection_sheet']);
        $this->middleware(['permission:loan.loans.reports.expected_repayments'])->only(['expected_repayment']);
        $this->middleware(['permission:loan.loans.reports.arrears'])->only(['arrears']);
        $this->middleware(['permission:loan.loans.reports.disbursements'])->only(['disbursement']);
        $this->middleware(['permission:loan.loans.reports.loan_status_report'])->only(['loan_status_report']);
        $this->middleware(['permission:loan.loans.reports.loan_application_report'])->only(['loan_application_report']);
        $this->middleware(['permission:loan.loans.reports.loan_approved_report'])->only(['loan_approved_report']);
        $this->middleware(['permission:loan.loans.reports.loan_rejected_report'])->only(['loan_rejected_report']);
        $this->middleware(['permission:loan.loans.reports.client_reports'])->only(['client_reports']);
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return theme_view('loan::report.index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function collection_sheet(Request $request)
    {
        $user_id = Auth::id();
        if ($user_id == 56) {
            $branch_id = 3;
        } else if ($user_id == 55) {
            $branch_id = 4;
        } else if ($user_id == 54) {
            $branch_id = 2;
        } else if ($user_id == 51) {
            $branch_id = 7;
        } else if ($user_id == 51) {
            $branch_id = 7;
        } else {
            $branch_id = $request->branch_id;
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $loan_product_id = $request->loan_product_id;
        $loan_officer_id = Auth::id();
        $users = User::whereHas('roles', function ($query) {
            return $query->where('name', '!=', 'client');
        })->get();
        $loan_products = LoanProduct::all();
        $branches = Branch::all();
        $data = [];
        if (!empty($start_date)) {
            $data = DB::table("loan_repayment_schedules")
                ->join("loans", "loan_repayment_schedules.loan_id", "loans.id")
                ->join("loan_products", "loans.loan_product_id", "loan_products.id")
                ->join("branches", "loans.branch_id", "branches.id")
                ->join("clients", "loans.client_id", "clients.id")
                ->leftJoin("users", "loans.loan_officer_id", "users.id")
                ->when($start_date, function ($query) use ($start_date, $end_date) {
                    $query->whereBetween('loan_repayment_schedules.due_date', [$start_date, $end_date]);
                })
                ->when($branch_id, function ($query) use ($branch_id) {
                    $query->where('loans.branch_id', $branch_id);
                })
                ->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                    $query->where('loan_repayment_schedules.created_by_id', $loan_officer_id);
                })
                ->when($loan_product_id, function ($query) use ($loan_product_id) {
                    $query->where('loans.loan_product_id', $loan_product_id);
                })
                ->where('loans.status', 'active')
                ->selectRaw("concat(clients.first_name,' ',clients.last_name) client,concat(users.first_name,' ',users.last_name) loan_officer,branches.name branch,clients.mobile,loans.client_id,loan_products.name loan_product,loan_repayment_schedules.loan_id,loans.expected_maturity_date,loan_repayment_schedules.total_due,(loan_repayment_schedules.principal+loan_repayment_schedules.interest+loan_repayment_schedules.fees+loan_repayment_schedules.penalties-loan_repayment_schedules.principal_written_off_derived-loan_repayment_schedules.interest_written_off_derived-loan_repayment_schedules.fees_written_off_derived-loan_repayment_schedules.penalties_written_off_derived-loan_repayment_schedules.interest_waived_derived-loan_repayment_schedules.fees_waived_derived-loan_repayment_schedules.penalties_waived_derived) expected_amount,loan_repayment_schedules.due_date")
                ->get();
            //check if we should download
            if ($request->download) {
                if ($request->type == 'pdf') {
                    $pdf = PDF::loadView(theme_view_file('loan::report.collection_sheet_pdf'), compact(
                        'start_date',
                        'end_date',
                        'branch_id',
                        'data',
                        'branches',
                        'users',
                        'loan_officer_id',
                        'loan_product_id',
                        'loan_products'
                    ));
                    return $pdf->download(trans_choice('loan::general.collection_sheet', 1) . '(' . $start_date . ' to ' . $end_date . ').pdf');
                }
                $view = theme_view(
                    'loan::report.collection_sheet_pdf',
                    compact(
                        'start_date',
                        'end_date',
                        'branch_id',
                        'data',
                        'branches',
                        'users',
                        'loan_officer_id',
                        'loan_product_id',
                        'loan_products'
                    )
                );
                if ($request->type == 'excel_2007') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.collection_sheet', 1) . '(' . $start_date . ' to ' . $end_date . ').xlsx');
                }
                if ($request->type == 'excel') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.collection_sheet', 1) . '(' . $start_date . ' to ' . $end_date . ').xls');
                }
                if ($request->type == 'csv') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.collection_sheet', 1) . '(' . $start_date . ' to ' . $end_date . ').csv');
                }
            }
        }
        return theme_view(
            'loan::report.collection_sheet',
            compact(
                'start_date',
                'end_date',
                'branch_id',
                'data',
                'branches',
                'users',
                'loan_officer_id',
                'loan_product_id',
                'loan_products'
            )
        );
    }


    public function mfi_officer_collection_sheet(Request $request)
    {
        $branch_id = $request->branch_id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $loan_product_id = $request->loan_product_id;
        $users = User::whereHas('roles', function ($query) {
            return $query->where('name', '!=', 'client');
        })->get();
        $loan_products = LoanProduct::all();
        $branches = Branch::all();
        $data = [];
        if (!empty($start_date)) {
            $data = DB::table("loan_repayment_schedules")
                ->join("loans", "loan_repayment_schedules.loan_id", "loans.id")
                ->join("loan_products", "loans.loan_product_id", "loan_products.id")
                ->join("branches", "loans.branch_id", "branches.id")
                ->join("clients", "loans.client_id", "clients.id")
                ->leftJoin("users", "loans.loan_officer_id", "users.id")
                ->when($start_date, function ($query) use ($start_date, $end_date) {
                    $query->whereBetween('loan_repayment_schedules.due_date', [$start_date, $end_date]);
                })
                ->when($branch_id, function ($query) use ($branch_id) {
                    $query->where('loans.branch_id', $branch_id);
                })
                ->when($loan_product_id, function ($query) use ($loan_product_id) {
                    $query->where('loans.loan_product_id', $loan_product_id);
                })
                ->where('loans.status', 'active')
                ->selectRaw("concat(clients.first_name,' ',clients.last_name) client,concat(users.first_name,' ',users.last_name) loan_officer,branches.name branch,clients.mobile,loans.client_id,loan_products.name loan_product,loan_repayment_schedules.loan_id,loans.expected_maturity_date,loan_repayment_schedules.total_due,(loan_repayment_schedules.principal+loan_repayment_schedules.interest+loan_repayment_schedules.fees+loan_repayment_schedules.penalties-loan_repayment_schedules.principal_written_off_derived-loan_repayment_schedules.interest_written_off_derived-loan_repayment_schedules.fees_written_off_derived-loan_repayment_schedules.penalties_written_off_derived-loan_repayment_schedules.interest_waived_derived-loan_repayment_schedules.fees_waived_derived-loan_repayment_schedules.penalties_waived_derived) expected_amount,loan_repayment_schedules.due_date")
                ->get();
            //check if we should download
            if ($request->download) {
                if ($request->type == 'pdf') {
                    $pdf = PDF::loadView(theme_view_file('loan::report.mfi_officer_collection_sheet_pdf'), compact(
                        'start_date',
                        'end_date',
                        'branch_id',
                        'data',
                        'branches',
                        'users',
                        'loan_product_id',
                        'loan_products'
                    ));
                    return $pdf->download(trans_choice('loan::general.mfi_officer_collection_sheet', 1) . '(' . $start_date . ' to ' . $end_date . ').pdf');
                }
                $view = theme_view(
                    'loan::report.mfi_officer_collection_sheet_pdf',
                    compact(
                        'start_date',
                        'end_date',
                        'branch_id',
                        'data',
                        'branches',
                        'users',
                        'loan_product_id',
                        'loan_products'
                    )
                );
                if ($request->type == 'excel_2007') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.mfi_officer_collection_sheet', 1) . '(' . $start_date . ' to ' . $end_date . ').xlsx');
                }
                if ($request->type == 'excel') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.mfi_officer_collection_sheet', 1) . '(' . $start_date . ' to ' . $end_date . ').xls');
                }
                if ($request->type == 'csv') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.mfi_officer_collection_sheet', 1) . '(' . $start_date . ' to ' . $end_date . ').csv');
                }
            }
        }
        return theme_view(
            'loan::report.mfi_officer_collection_sheet',
            compact(
                'start_date',
                'end_date',
                'branch_id',
                'data',
                'branches',
                'users',
                'loan_product_id',
                'loan_products'
            )
        );
    }
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function repayment(Request $request)
    {
        $user_id = Auth::id();
        if ($user_id == 56) {
            $branch_id = 3;
        } else if ($user_id == 55) {
            $branch_id = 4;
        } else if ($user_id == 54) {
            $branch_id = 2;
        } else if ($user_id == 51) {
            $branch_id = 7;
        } else if ($user_id == 51) {
            $branch_id = 7;
        } else {
            $branch_id = $request->branch_id;
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $officer_id = Auth::id();
        $users = User::whereHas('roles', function ($query) {
            return $query->where('name', '!=', 'client');
        })->get();
        $data = [];
        $branches = Branch::all();
        if (!empty($start_date)) {
            $data = DB::table("loan_transactions")
                ->join("loans", "loan_transactions.loan_id", "loans.id")
                ->join("branches", "loans.branch_id", "branches.id")
                ->join("clients", "loans.client_id", "clients.id")
                ->leftJoin("payment_details", "loan_transactions.payment_detail_id", "payment_details.id")
                ->leftJoin("users", "loan_transactions.created_by_id", "users.id")
                ->leftJoin("payment_types", "payment_details.payment_type_id", "payment_types.id")
                ->when($start_date, function ($query) use ($start_date, $end_date) {
                    $query->whereBetween('loan_transactions.submitted_on', [$start_date, $end_date]);
                })
                ->when($branch_id, function ($query) use ($branch_id) {
                    $query->where('loans.branch_id', $branch_id);
                })
                ->when($officer_id, function ($query) use ($officer_id) {
                    $query->where('payment_details.created_by_id', $officer_id);
                })
                ->where('loan_transaction_type_id', 2)
                ->selectRaw("concat(clients.first_name,' ',clients.last_name) client,concat(users.first_name,' ',users.last_name) loan_officer,branches.name branch,loans.client_id,loan_transactions.id,loan_transactions.loan_id,loan_transactions.principal_repaid_derived,loan_transactions.interest_repaid_derived,loan_transactions.fees_repaid_derived,loan_transactions.penalties_repaid_derived,loan_transactions.submitted_on,payment_types.name payment_type")
                ->get();
            //check if we should download
            if ($request->download) {
                if ($request->type == 'pdf') {
                    $pdf = PDF::loadView(theme_view_file('loan::report.repayment_pdf'), compact(
                        'start_date',
                        'end_date',
                        'branch_id',
                        'data',
                        'branches'
                    ));
                    return $pdf->download(trans_choice('loan::general.repayment', 1) . '(' . $start_date . ' to ' . $end_date . ').pdf');
                }
                $view = theme_view(
                    'loan::report.repayment_pdf',
                    compact(
                        'start_date',
                        'end_date',
                        'branch_id',
                        'data',
                        'branches'
                    )
                );
                if ($request->type == 'excel_2007') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.repayment', 1) . '(' . $start_date . ' to ' . $end_date . ').xlsx');
                }
                if ($request->type == 'excel') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.repayment', 1) . '(' . $start_date . ' to ' . $end_date . ').xls');
                }
                if ($request->type == 'csv') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.repayment', 1) . '(' . $start_date . ' to ' . $end_date . ').csv');
                }
            }
        }
        return theme_view(
            'loan::report.repayment',
            compact(
                'start_date',
                'end_date',
                'branch_id',
                'data',
                'branches'
            )
        );
    }
    //Loan status report 
    public function loan_status_report(Request $request)
    {
        $user_id = Auth::id();
        if ($user_id == 56) {
            $branch_id = 3;
        } else if ($user_id == 55) {
            $branch_id = 4;
        } else if ($user_id == 54) {
            $branch_id = 2;
        } else if ($user_id == 51) {
            $branch_id = 7;
        } else if ($user_id == 51) {
            $branch_id = 7;
        } else {
            $branch_id = $request->branch_id;
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $loan_product_id = $request->loan_product_id;
        $data = [];
        $branches = Branch::all();
        $loan_products = LoanProduct::all();
        if (!empty($start_date)) {
            $data = DB::table("loans")
                ->join("branches", "loans.branch_id", "branches.id")
                ->join("clients", "loans.client_id", "clients.id")
                ->join("loan_products", "loans.loan_product_id", "loan_products.id")
                ->join("funds", "loans.fund_id", "funds.id")
                ->when($start_date, function ($query) use ($start_date, $end_date) {
                    $query->whereBetween('loans.created_at', [$start_date, $end_date]);
                })
                ->when($branch_id, function ($query) use ($branch_id) {
                    $query->where('loans.branch_id', $branch_id);
                })
                ->selectRaw("clients.id,concat(clients.first_name,' ',clients.last_name) client,
                loan_products.name loan_products, branches.name branches, funds.name funds,
                loans.status,loans.applied_amount, loans.approved_amount, loans.approved_on_date, loans.approved_notes, loans.disbursed_on_date, loans.disbursed_notes, loans.rejected_on_date, loans.rejected_notes, loans.applied_amount, loans.approved_amount, loans.interest_rate, loans.loan_term,loans.created_at")
                ->get();

            //check if we should download
            if ($request->download) {
                if ($request->type == 'pdf') {
                    $pdf = PDF::loadView(theme_view_file('loan::report.loan_status_pdf'), compact(
                        'start_date',
                        'end_date',
                        'branch_id',
                        'data',
                        'branches',
                        'loan_product_id',
                        'loan_products'
                    ))->setPaper('A4', 'landscape');
                    return $pdf->download(trans_choice('loan::general.loan_status_report', 1) . '( as at ' . $end_date . ').pdf');
                }
                $view = theme_view(
                    'loan::report.loan_status_pdf',
                    compact(
                        'start_date',
                        'end_date',
                        'branch_id',
                        'data',
                        'branches',
                        'loan_product_id',
                        'loan_products'
                    )
                );
                if ($request->type == 'excel_2007') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.loan_status_report', 1) . '(as at ' . $end_date . ').xlsx');
                }
                if ($request->type == 'excel') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.loan_status_report', 1) . '(as at ' . $end_date . ').xls');
                }
                if ($request->type == 'csv') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.loan_status_report', 1) . '(as at' . $end_date . ').csv');
                }
            }
        }
        return theme_view(
            'loan::report.loan_status',
            compact('start_date', 'end_date', 'branch_id', 'data', 'branches', 'loan_product_id', 'loan_products')
        );
    }
    //loan_application_report
    public function loan_application_report(Request $request)
    {
        $user_id = Auth::id();
        if ($user_id == 56) {
            $branch_id = 3;
        } else if ($user_id == 55) {
            $branch_id = 4;
        } else if ($user_id == 54) {
            $branch_id = 2;
        } else if ($user_id == 51) {
            $branch_id = 7;
        } else if ($user_id == 51) {
            $branch_id = 7;
        } else {
            $branch_id = $request->branch_id;
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $loan_product_id = $request->loan_product_id;
        $data = [];
        $branches = Branch::all();
        $loan_products = LoanProduct::all();
        if (!empty($start_date)) {
            $data = DB::table("loan_applications")
                ->join("loans", "loan_applications.loan_id", "loans.id")
                ->join("branches", "loan_applications.branch_id", "branches.id")
                ->join("clients", "loan_applications.client_id", "clients.id")
                ->join("loan_products", "loan_applications.loan_product_id", "loan_products.id")
                ->join("funds", "loan_products.fund_id", "funds.id")
                ->when($start_date, function ($query) use ($start_date, $end_date) {
                    $query->whereBetween('loan_applications.created_at', [$start_date, $end_date]);
                })
                ->when($branch_id, function ($query) use ($branch_id) {
                    $query->where('loans.branch_id', $branch_id);
                })
                ->selectRaw("loan_applications.id,  clients.id client_ID, concat(clients.first_name,' ',clients.last_name) client,
                loan_products.name loan_products, branches.name branches, funds.name funds,
                loan_applications.status, loan_applications.created_at, loan_applications.amount,
                loans.approved_on_date")
                ->get();
            //check if we should download
            if ($request->download) {
                if ($request->type == 'pdf') {
                    $pdf = PDF::loadView(theme_view_file('loan::report.loan_application_report_pdf'), compact(
                        'start_date',
                        'end_date',
                        'branch_id',
                        'data',
                        'branches',
                        'loan_product_id',
                        'loan_products'
                    ))->setPaper('A4', 'landscape');
                    return $pdf->download(trans_choice('loan::general.loan_application_report', 1) . '( as at ' . $end_date . ').pdf');
                }
                $view = theme_view(
                    'loan::report.loan_application_report_pdf',
                    compact(
                        'start_date',
                        'end_date',
                        'branch_id',
                        'data',
                        'branches',
                        'loan_product_id',
                        'loan_products'
                    )
                );
                if ($request->type == 'excel_2007') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.loan_application_report', 1) . '(as at ' . $end_date . ').xlsx');
                }
                if ($request->type == 'excel') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.loan_application_report', 1) . '(as at ' . $end_date . ').xls');
                }
                if ($request->type == 'csv') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.loan_application_report', 1) . '(as at' . $end_date . ').csv');
                }
            }
        }
        return theme_view(
            'loan::report.loan_application_report',
            compact('start_date', 'end_date', 'branch_id', 'data', 'branches', 'loan_product_id', 'loan_products')
        );
    }

    //loan deliquency report
    public function loan_deliquency_report(Request $request)
    {
    }

    //loan status report on amount duee

    public function loan_amount_due_report(Request $request)
    {
    }
    //loan  stuatus report paid
    public function loan_Prepaid_report(Request $request)
    {
    }
    //loan repayment report
    public function loan_repayment_report(Request $request)
    {
    }



    //loan_approved
    public function loan_approved_report(Request $request)
    {
        $user_id = Auth::id();
        if ($user_id == 56) {
            $branch_id = 3;
        } else if ($user_id == 55) {
            $branch_id = 4;
        } else if ($user_id == 54) {
            $branch_id = 2;
        } else if ($user_id == 51) {
            $branch_id = 7;
        } else if ($user_id == 51) {
            $branch_id = 7;
        } else {
            $branch_id = $request->branch_id;
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $loan_product_id = $request->loan_product_id;
        $branches = Branch::all();
        $loan_products = LoanProduct::all();
        $status = 'approved';
        $data = [];
        if (!empty($status)) {
            $data = DB::table("loans")
                ->join("branches", "loans.branch_id", "branches.id")
                ->join("clients", "loans.client_id", "clients.id")
                ->join("loan_products", "loans.loan_product_id", "loan_products.id")
                ->join("funds", "loans.fund_id", "funds.id")
                ->join("loan_purposes", "loans.loan_purpose_id", "loan_purposes.id")
                ->when($branch_id, function ($query) use ($branch_id) {
                    $query->where('loans.branch_id', $branch_id);
                })->when($start_date, function ($query) use ($start_date, $end_date) {
                    $query->whereBetween('loans.approved_on_date', [$start_date, $end_date]);
                })
                ->where('loans.status', $status)
                ->selectRaw("clients.id, concat(clients.first_name,' ',clients.last_name) client,
            loan_products.name loan_products, branches.name branches, funds.name funds,
            loans.status, loans.approved_on_date, loans.approved_notes, loans.applied_amount, loans.approved_amount,
            loan_purposes.name loan_purposes,loans.interest_rate, loans.loan_term")
                ->get();
            //check if we should download
            if ($request->download) {
                if ($request->type == 'pdf') {
                    $pdf = PDF::loadView(theme_view_file('loan::report.loan_approved_report_pdf'), compact(
                        'start_date',
                        'end_date',
                        'branch_id',
                        'data',
                        'branches',
                        'loan_product_id',
                        'loan_products'
                    ))->setPaper('A4', 'landscape');
                    return $pdf->download(trans_choice('loan::general.loan_approved_report', 1) . '( as at ' . $end_date . ').pdf');
                }
                $view = theme_view(
                    'loan::report.loan_approved_report_pdf',
                    compact(
                        'start_date',
                        'end_date',
                        'branch_id',
                        'data',
                        'branches',
                        'loan_product_id',
                        'loan_products'
                    )
                );
                if ($request->type == 'excel_2007') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.loan_approved_report', 1) . '(as at ' . $end_date . ').xlsx');
                }
                if ($request->type == 'excel') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.loan_approved_report', 1) . '(as at ' . $end_date . ').xls');
                }
                if ($request->type == 'csv') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.loan_approved_report', 1) . '(as at' . $end_date . ').csv');
                }
            }
        }
        return theme_view(
            'loan::report.loan_approved_report',
            compact('start_date', 'end_date', 'branch_id', 'data', 'branches', 'loan_product_id', 'loan_products', 'status')
        );
    }
    //loan application rejected
    public function loan_rejected_report(Request $request)
    {
        $user_id = Auth::id();
        if ($user_id == 56) {
            $branch_id = 3;
        } else if ($user_id == 55) {
            $branch_id = 4;
        } else if ($user_id == 54) {
            $branch_id = 2;
        } else if ($user_id == 51) {
            $branch_id = 7;
        } else if ($user_id == 51) {
            $branch_id = 7;
        } else {
            $branch_id = $request->branch_id;
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $loan_product_id = $request->loan_product_id;
        $branches = Branch::all();
        $loan_products = LoanProduct::all();
        $status = 'rejected';
        $data = [];
        if (!empty($status)) {
            $data = DB::table("loans")
                ->join("branches", "loans.branch_id", "branches.id")
                ->join("clients", "loans.client_id", "clients.id")
                ->join("loan_products", "loans.loan_product_id", "loan_products.id")
                ->join("funds", "loans.fund_id", "funds.id")
                ->join("loan_purposes", "loans.loan_purpose_id", "loan_purposes.id")
                ->when($branch_id, function ($query) use ($branch_id) {
                    $query->where('loans.branch_id', $branch_id);
                })->when($start_date, function ($query) use ($start_date, $end_date) {
                    $query->whereBetween('loans.rejected_on_date', [$start_date, $end_date]);
                })
                ->where('loans.status', $status)
                ->selectRaw("clients.id,concat(clients.first_name,' ',clients.last_name) client,
                    loan_products.name loan_products, branches.name branches, funds.name funds,
                    loans.status, loans.rejected_on_date, loans.rejected_notes, loans.applied_amount, 
                    loan_purposes.name loan_purposes, loans.interest_rate, loans.loan_term")
                ->get();

            //check if we should download
            if ($request->download) {
                if ($request->type == 'pdf') {
                    $pdf = PDF::loadView(theme_view_file('loan::report.loan_rejected_report_pdf'), compact(
                        'start_date',
                        'end_date',
                        'branch_id',
                        'data',
                        'branches',
                        'loan_product_id',
                        'loan_products'
                    ))->setPaper('A4', 'landscape');
                    return $pdf->download(trans_choice('loan::general.loan_rejected_report', 1) . '( as at ' . $end_date . ').pdf');
                }
                $view = theme_view(
                    'loan::report.loan_rejected_report_pdf',
                    compact(
                        'start_date',
                        'end_date',
                        'branch_id',
                        'data',
                        'branches',
                        'loan_product_id',
                        'loan_products'
                    )
                );
                if ($request->type == 'excel_2007') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.loan_rejected_report', 1) . '(as at ' . $end_date . ').xlsx');
                }
                if ($request->type == 'excel') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.loan_rejected_report', 1) . '(as at ' . $end_date . ').xls');
                }
                if ($request->type == 'csv') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.loan_rejected_report', 1) . '(as at' . $end_date . ').csv');
                }
            }
        }
        return theme_view(
            'loan::report.loan_rejected_report',
            compact('start_date', 'end_date', 'branch_id', 'data', 'branches', 'loan_product_id', 'loan_products', 'status')
        );
    }
    //Clients of the loan
    public function client_reports(Request $request)
    {
        $user_id = Auth::id();
        if ($user_id == 56) {
            $branch_id = 3;
        } else if ($user_id == 55) {
            $branch_id = 4;
        } else if ($user_id == 54) {
            $branch_id = 2;
        } else if ($user_id == 51) {
            $branch_id = 7;
        } else if ($user_id == 51) {
            $branch_id = 7;
        } else {
            $branch_id = $request->branch_id;
        }
        $data = [];
        $branches = Branch::all();
        if (!empty($branch_id)) {
            $data = DB::table("clients")
                ->join("branches", "clients.branch_id", "branches.id")
                ->when($branch_id, function ($query) use ($branch_id) {
                    $query->where('clients.branch_id', $branch_id);
                })
                ->selectRaw("clients.id,concat(clients.first_name,' ',clients.middle_name,' ',clients.last_name) client,clients.gender, clients.age, clients.mobile, clients.email, clients.status, clients.created_at, branches.name branches")
                ->get();
            //check if we should download
            if ($request->download) {
                if ($request->type == 'pdf') {
                    $pdf = PDF::loadView(theme_view_file('loan::report.client_report_pdf'), compact(
                        'branch_id',
                        'data',
                        'branches'
                    ))->setPaper('A4', 'landscape');
                    return $pdf->download(trans_choice('loan::general.client_report', 1) . '.pdf');
                }
                $view = theme_view(
                    'loan::report.client_report_pdf',
                    compact(
                        'branch_id',
                        'data',
                        'branches'
                    )
                );
                if ($request->type == 'excel_2007') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.client_report', 1) . '.xlsx');
                }
                if ($request->type == 'excel') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.client_report', 1) . '.xls');
                }
                if ($request->type == 'csv') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.client_report', 1) . '.csv');
                }
            }
        }
        return theme_view(
            'loan::report.client_report',
            compact(
                'branch_id',
                'data',
                'branches'
            )
        );
    }


    //end of new code for report

    public function expected_repayment(Request $request)
    {
        $user_id = Auth::id();
        if ($user_id == 56) {
            $branch_id = 3;
        } else if ($user_id == 55) {
            $branch_id = 4;
        } else if ($user_id == 54) {
            $branch_id = 2;
        } else if ($user_id == 51) {
            $branch_id = 7;
        } else if ($user_id == 51) {
            $branch_id = 7;
        } else {
            $branch_id = $request->branch_id;
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $data = [];
        $branches = Branch::all();
        if (!empty($start_date)) {
            $data = DB::table("loan_repayment_schedules")
                ->join("loans", "loan_repayment_schedules.loan_id", "loans.id")
                ->join("branches", "loans.branch_id", "branches.id")
                ->when($start_date, function ($query) use ($start_date, $end_date) {
                    $query->whereBetween('loan_repayment_schedules.due_date', [$start_date, $end_date]);
                })
                ->when($branch_id, function ($query) use ($branch_id) {
                    $query->where('loans.branch_id', $branch_id);
                })
                ->where('loans.status', 'active')
                ->selectRaw("branches.name branch,loans.branch_id,coalesce(sum(loan_repayment_schedules.principal-loan_repayment_schedules.principal_written_off_derived),0) principal,coalesce(sum(loan_repayment_schedules.interest-loan_repayment_schedules.interest_written_off_derived-loan_repayment_schedules.interest_waived_derived),0) interest,coalesce(sum(loan_repayment_schedules.fees-loan_repayment_schedules.fees_written_off_derived-loan_repayment_schedules.fees_waived_derived),0) fees,coalesce(sum(loan_repayment_schedules.penalties-loan_repayment_schedules.penalties_written_off_derived-loan_repayment_schedules.penalties_waived_derived),0) penalties,coalesce(sum(loan_repayment_schedules.principal_repaid_derived),0) principal_repaid_derived,coalesce(sum(loan_repayment_schedules.interest_repaid_derived),0) interest_repaid_derived,coalesce(sum(loan_repayment_schedules.fees_repaid_derived),0) fees_repaid_derived,coalesce(sum(loan_repayment_schedules.penalties_repaid_derived),0) penalties_repaid_derived")
                ->groupBy('branches.id')
                ->get();
            //check if we should download
            if ($request->download) {
                if ($request->type == 'pdf') {
                    $pdf = PDF::loadView(theme_view_file('loan::report.expected_repayment_pdf'), compact(
                        'start_date',
                        'end_date',
                        'branch_id',
                        'data',
                        'branches'
                    ));
                    return $pdf->download(trans_choice('loan::general.expected', 1) . ' ' . trans_choice('loan::general.repayment', 1) . '(' . $start_date . ' to ' . $end_date . ').pdf');
                }
                $view = theme_view(
                    'loan::report.expected_repayment_pdf',
                    compact(
                        'start_date',
                        'end_date',
                        'branch_id',
                        'data',
                        'branches'
                    )
                );
                if ($request->type == 'excel_2007') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.expected', 1) . ' ' . trans_choice('loan::general.repayment', 1) . '(' . $start_date . ' to ' . $end_date . ').xlsx');
                }
                if ($request->type == 'excel') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.expected', 1) . ' ' . trans_choice('loan::general.repayment', 1) . '(' . $start_date . ' to ' . $end_date . ').xls');
                }
                if ($request->type == 'csv') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.expected', 1) . ' ' . trans_choice('loan::general.repayment', 1) . '(' . $start_date . ' to ' . $end_date . ').csv');
                }
            }
        }
        return theme_view(
            'loan::report.expected_repayment',
            compact(
                'start_date',
                'end_date',
                'branch_id',
                'data',
                'branches'
            )
        );
    }

    public function arrears(Request $request)
    {
        $user_id = Auth::id();
        if ($user_id == 56) {
            $branch_id = 3;
        } else if ($user_id == 55) {
            $branch_id = 4;
        } else if ($user_id == 54) {
            $branch_id = 2;
        } else if ($user_id == 51) {
            $branch_id = 7;
        } else if ($user_id == 51) {
            $branch_id = 7;
        } else {
            $branch_id = $request->branch_id;
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $data = [];
        $branches = Branch::all();
        if (!empty($end_date)) {
            $data = Loan::with("repayment_schedules")
                ->join(DB::raw("(select*from loan_repayment_schedules where loan_repayment_schedules.due_date<'$end_date' and total_due>0) loan_repayment_schedules"), "loan_repayment_schedules.loan_id", "loans.id")
                ->join("branches", "loans.branch_id", "branches.id")
                ->join("loan_products", "loans.loan_product_id", "loan_products.id")
                ->join("clients", "loans.client_id", "clients.id")
                ->leftJoin("users", "loans.loan_officer_id", "users.id")
                ->when($branch_id, function ($query) use ($branch_id) {
                    $query->where('loans.branch_id', $branch_id);
                })
                ->where('loans.status', 'active')
                ->selectRaw(" clients.id, concat(clients.first_name,' ',clients.last_name) client,clients.mobile,concat(users.first_name,' ',users.last_name) loan_officer,branches.name branch,clients.mobile,loans.client_id,loan_products.name loan_product,loans.expected_maturity_date,loans.disbursed_on_date,loans.id,(SELECT submitted_on FROM loan_transactions WHERE loan_id=loans.id ORDER BY submitted_on DESC LIMIT 1) last_payment_date,loans.principal, loans.approved_on_date")
                ->groupBy('loans.id')
                ->get();
            //check if we should download
            if ($request->download) {
                if ($request->type == 'pdf') {
                    $pdf = PDF::loadView(theme_view_file('loan::report.arrears_pdf'), compact(
                        'start_date',
                        'end_date',
                        'branch_id',
                        'data',
                        'branches'
                    ))->setPaper('A4', 'landscape');
                    return $pdf->download(trans_choice('loan::general.arrears', 1) . '( as at ' . $end_date . ').pdf');
                }
                $view = theme_view(
                    'loan::report.arrears_pdf',
                    compact(
                        'start_date',
                        'end_date',
                        'branch_id',
                        'data',
                        'branches'
                    )
                );
                if ($request->type == 'excel_2007') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.arrears', 1) . '(as at ' . $end_date . ').xlsx');
                }
                if ($request->type == 'excel') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.arrears', 1) . '(as at ' . $end_date . ').xls');
                }
                if ($request->type == 'csv') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.arrears', 1) . '(as at' . $end_date . ').csv');
                }
            }
        }
        return theme_view(
            'loan::report.arrears',
            compact(
                'start_date',
                'end_date',
                'branch_id',
                'data',
                'branches'
            )
        );
    }


    public function disbursement(Request $request)
    {
        $user_id = Auth::id();
        if ($user_id == 56) {
            $branch_id = 3;
        } else if ($user_id == 55) {
            $branch_id = 4;
        } else if ($user_id == 54) {
            $branch_id = 2;
        } else if ($user_id == 51) {
            $branch_id = 7;
        } else if ($user_id == 51) {
            $branch_id = 7;
        } else {
            $branch_id = $request->branch_id;
        }
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $loan_product_id = $request->loan_product_id;
        $status = $request->status;
        $users = User::whereHas('roles', function ($query) {
            return $query->where('name', '!=', 'client');
        })->get();
        $loan_products = LoanProduct::all();
        $data = [];
        $branches = Branch::all();
        if (!empty($start_date)) {
            $data = Loan::with("repayment_schedules")
                ->join("branches", "loans.branch_id", "branches.id")
                ->join("funds", "loans.fund_id", "funds.id")
                ->join("loan_purposes", "loans.loan_purpose_id", "loan_purposes.id")
                ->join("loan_products", "loans.loan_product_id", "loan_products.id")
                ->join("clients", "loans.client_id", "clients.id")
                ->leftJoin("users", "loans.loan_officer_id", "users.id")
                ->when($start_date, function ($query) use ($start_date, $end_date) {
                    $query->whereBetween('loans.disbursed_on_date', [$start_date, $end_date]);
                })
                ->when($branch_id, function ($query) use ($branch_id) {
                    $query->where('loans.branch_id', $branch_id);
                })
                ->when($loan_product_id, function ($query) use ($loan_product_id) {
                    $query->where('loans.loan_product_id', $loan_product_id);
                })
                ->when($status, function ($query) use ($status) {
                    $query->where('loans.status', $status);
                })
                ->selectRaw("concat(clients.first_name,' ',clients.last_name) client,clients.gender,clients.dob,clients.mobile,concat(users.first_name,' ',users.last_name) loan_officer,loan_purposes.name loan_purpose,funds.name fund,branches.name branch,clients.mobile,loans.client_id,loan_products.name loan_product,loans.expected_maturity_date,loans.disbursed_on_date,loans.id,loans.principal,loans.status,loans.repayment_frequency,loans.repayment_frequency_type")
                ->get();
            //check if we should download
            if ($request->download) {
                if ($request->type == 'pdf') {
                    $pdf = PDF::loadView(theme_view_file('loan::report.disbursement_pdf'), compact(
                        'start_date',
                        'end_date',
                        'branch_id',
                        'data',
                        'branches',
                        /* 'loan_officer_id', */
                        'loan_product_id',
                        'loan_products',
                        'users',
                        'status'
                    ))->setPaper('A4', 'landscape');
                    return $pdf->download(trans_choice('loan::general.disbursement', 1) . '(' . $start_date . '  to ' . $end_date . ').pdf');
                }
                $view = theme_view(
                    'loan::report.arrears_pdf',
                    compact(
                        'start_date',
                        'end_date',
                        'branch_id',
                        'data',
                        'branches',
                        /* 'loan_officer_id', */
                        'loan_product_id',
                        'loan_products',
                        'users',
                        'status'
                    )
                );
                if ($request->type == 'excel_2007') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.disbursement', 1) . '(' . $start_date . ' to ' . $end_date . ').xlsx');
                }
                if ($request->type == 'excel') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.disbursement', 1) . '(' . $start_date . ' to ' . $end_date . ').xls');
                }
                if ($request->type == 'csv') {
                    return Excel::download(new LoanExport($view), trans_choice('loan::general.disbursement', 1) . '(' . $start_date . ' to ' . $end_date . ').csv');
                }
            }
        }
        return theme_view(
            'loan::report.disbursement',
            compact(
                'start_date',
                'end_date',
                'branch_id',
                'data',
                'branches',
                /*'loan_officer_id', */
                'loan_product_id',
                'loan_products',
                'users',
                'status'
            )
        );
    }
}