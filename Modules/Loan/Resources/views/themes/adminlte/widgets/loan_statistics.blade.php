<div class="grid-stack-item loan_statistics" gs-x="{{ $config['x'] }}" gs-y="{{ $config['y'] }}"
    gs-w="{{ $config['width'] }}" gs-h="{{ $config['height'] }}" gs-id="LoanStatistics">
    <div class="grid-stack-item-content">
          <div class="card card-bordered card-preview">
            <div class="card-header with-border">
                <h3 class="card-title">{{ trans_choice('loan::general.loan',1) }} {{ trans_choice('loan::general.statistic',2) }}</h3>

                <div class="card-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-card-widget="collapse"><i
                                class="fa fa-minus"></i>
                    </button>
                </div>
                <!-- /.box-tools -->
            </div>
            <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-green elevation-1">
                        <i class="fas fa-coins"></i>
                    </span>

                    <div class="info-box-content">
                        <span class="info-box-text">{{ trans_choice('loan::general.loan', 2) }}
                            {{ trans_choice('loan::general.disbursed', 2) }}</span>
                        <span
                            class="info-box-number">{{ number_format($loan_disbursed,2) }}</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-green elevation-1">
                        <i class="fas fa-dollar-sign"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ trans_choice('loan::general.total', 1) }}
                            {{ trans_choice('loan::general.repayment', 2) }}</span>
                        <span
                            class="info-box-number">{{ number_format($total_repayment,2) }}</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
            </div>
            <?php
            $total_principal = 0;
            $total_principal_waived = 0;
            $total_principal_paid = 0;
            $total_principal_written_off = 0;
            $total_principal_outstanding = 0;
            $total_principal_overdue = 0;
            $total_interest = 0;
            $total_interest_waived = 0;
            $total_interest_paid = 0;
            $total_interest_written_off = 0;
            $total_interest_outstanding = 0;
            $total_interest_overdue = 0;
            $total_fees = 0;
            $total_fees_waived = 0;
            $total_fees_paid = 0;
            $total_fees_written_off = 0;
            $total_fees_outstanding = 0;
            $total_fees_overdue = 0;
            $total_penalties = 0;
            $total_penalties_waived = 0;
            $total_penalties_paid = 0;
            $total_penalties_written_off = 0;
            $total_penalties_outstanding = 0;
            $total_penalties_overdue = 0;
            $total_arrears_amount = 0;
            
            foreach ($loans as $loan) {
                $total_principal = $total_principal + $loan->repayment_schedules->sum('principal');
                $total_principal_paid = $total_principal_paid + $loan->repayment_schedules->sum('principal_repaid_derived');
                $total_principal_written_off = $total_principal_written_off + $loan->repayment_schedules->sum('principal_written_off_derived');
                $total_interest = $total_interest + $loan->repayment_schedules->sum('interest');
                $total_interest_waived = $total_interest_waived + $loan->repayment_schedules->sum('interest_waived_derived');
                $total_interest_paid = $total_interest_paid + $loan->repayment_schedules->sum('interest_repaid_derived');
                $total_interest_written_off = $total_interest_written_off + $loan->repayment_schedules->sum('interest_written_off_derived');
                $total_fees = $total_fees + $loan->repayment_schedules->sum('fees') + $loan->disbursement_charges;
                $total_fees_waived = $total_fees_waived + $loan->repayment_schedules->sum('fees_waived_derived');
                $total_fees_paid = $total_fees_paid + $loan->repayment_schedules->sum('fees_repaid_derived') + $loan->disbursement_charges;
                $total_fees_written_off = $total_fees_written_off + $loan->repayment_schedules->sum('fees_written_off_derived');
            
                $total_penalties = $total_penalties + $loan->repayment_schedules->sum('penalties');
                $total_penalties_waived = $total_penalties_waived + $loan->repayment_schedules->sum('penalties_waived_derived');
                $total_penalties_paid = $total_penalties_paid + $loan->repayment_schedules->sum('penalties_repaid_derived');
                $total_penalties_written_off = $total_penalties_written_off + $loan->repayment_schedules->sum('penalties_written_off_derived');
                //arrears
                $arrears_last_schedule = $loan->repayment_schedules
                    ->sortByDesc('due_date')
                    ->where('due_date', '<', date('Y-m-d'))
                    ->where('total_due', '>', 0)
                    ->first();
                if (!empty($arrears_last_schedule)) {
                    $overdue_schedules = $loan->repayment_schedules->where('due_date', '<=', $arrears_last_schedule->due_date);
                    $total_principal_overdue = $total_principal_overdue + $overdue_schedules->sum('principal') - $overdue_schedules->sum('principal_written_off_derived') - $overdue_schedules->sum('principal_repaid_derived');
                    $total_interest_overdue = $total_interest_overdue + $overdue_schedules->sum('interest') - $overdue_schedules->sum('interest_written_off_derived') - $overdue_schedules->sum('interest_repaid_derived') - $overdue_schedules->sum('interest_waived_derived');
                    $total_fees_overdue = $total_fees_overdue + $overdue_schedules->sum('fees') - $overdue_schedules->sum('fees_written_off_derived') - $overdue_schedules->sum('fees_repaid_derived') - $overdue_schedules->sum('fees_waived_derived');
                    $total_penalties_overdue = $total_penalties_overdue + $overdue_schedules->sum('penalties') - $overdue_schedules->sum('penalties_written_off_derived') - $overdue_schedules->sum('penalties_repaid_derived') - $overdue_schedules->sum('penalties_waived_derived');
                }
            }
            $total_principal_outstanding = $total_principal - $total_principal_waived - $total_principal_paid - $total_principal_written_off;
            $total_interest_outstanding = $total_interest - $total_interest_waived - $total_interest_paid - $total_interest_written_off;
            $total_fees_outstanding = $total_fees - $total_fees_waived - $total_fees_paid - $total_fees_written_off;
            $total_penalties_outstanding = $total_penalties - $total_penalties_waived - $total_penalties_paid - $total_penalties_written_off;
            $total_balance = $total_principal_outstanding + $total_interest_outstanding + $total_fees_outstanding + $total_penalties_outstanding;
            $total_arrears_amount = $total_principal_overdue + $total_interest_overdue + $total_fees_overdue + $total_penalties_overdue;
            ?>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow elevation-1">
                        <i class="fas fa-money-bill"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ trans_choice('loan::general.total', 1) }}
                            {{ trans_choice('loan::general.principal', 2) }}
                            {{ trans_choice('loan::general.outstanding', 3) }}</span>
                        <span class="info-box-number">{{ number_format($total_principal_outstanding, 2) }}</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
            </div>
            <?php
            $IntrestDue = 0;
            $arrearsRate = 0.00;
            $repaymentRate = 0.00;
            $poAtRisk = 0.00;
            $netDue = 0;
            $x = 0;
            $y = 0;
            $z =0;
            $k =0;
            $loan_disbursed = 0;
            $loan_principal_due_paid = 0;
            $loan_principal_due = 0;
            
            $loan_principal_due_paid = $repayment_schedules->sum('principal_repaid_derived');
            $loan_principal_due = $repayment_schedules->sum('principal');
            $x = $loan_principal_due - $loan_principal_due_paid;
            $y = $x / $total_principal_outstanding;
            $poAtRisk = $y * 100;
            $z = $x / $loan_principal_due;
            $arrearsRate = $z * 100;
            $k = $loan_principal_due_paid / $loan_principal_due;
            $repaymentRate = $k * 100;
            $IntrestDue =  $repayment_schedules->sum('interest') - $repayment_schedules->sum('interest_repaid_derived');
            ?>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-green elevation-3">
                        <i class="fas fa-chart-line"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ trans_choice('loan::general.interest', 1) }}
                            {{ trans_choice('loan::general.due', 2) }}</span>
                        <span class="info-box-number">{{ number_format($IntrestDue,2)}}</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-red elevation-3">
                        <i class="fas fa-minus"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ trans_choice('loan::general.arrears', 1) }}
                            {{ trans_choice('loan::general.rate', 2) }}</span>
                        <span class="info-box-number">{{ number_format($arrearsRate,2) }}%</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
            </div>
             <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-purple elevation-3">
                        <i class="fas fa-money-bill"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ trans_choice('core::general.po_at_risk', 1) }}</span>
                        <span class="info-box-number">{{ number_format($poAtRisk,2)}}%</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
        </div>
    <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-pink elevation-3">
                        <i class="fas fa-hand-holding-usd"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">{{ trans_choice('loan::general.repayment', 1) }}
                            {{ trans_choice('loan::general.rate', 2) }}</span>
                        <span class="info-box-number">{{ number_format($repaymentRate,2) }}%</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
        </div>
                </div>
            </div>
        </div>
    </div>
</div>
