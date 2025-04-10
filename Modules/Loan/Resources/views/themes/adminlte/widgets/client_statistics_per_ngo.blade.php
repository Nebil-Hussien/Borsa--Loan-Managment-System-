<div class="grid-stack-item client_statistics_per_ngo" 
gs-x="{{ $config['x'] }}" gs-y="{{ $config['y'] }}" 
gs-w="{{ $config['width'] }}"
gs-h="{{ $config['height'] }}" 
gs-id="ClientStatisticsPerNGO" draggable="true">
    <div class="grid-stack-item-content">
          <div class="card card-bordered card-preview">
            <div class="card-header with-border">
                <h3 class="card-title">{{ trans_choice('core::general.Client_stattistics_per_ngo', 1) }}</h3>
                <div class="card-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-card-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                </div>
                <!-- /.box-tools -->
            </div>
            <div class="card-body">
                        {!! $chart->container() !!}
            </div>
          </div>
    </div>
</div>
{!! $chart->script() !!}
