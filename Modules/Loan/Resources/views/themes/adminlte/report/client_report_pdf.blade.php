<style>
    body {
        font-size: 9px;
    }

    .table {
        width: 100%;
        border: 1px solid #ccc;
        border-collapse: collapse;
    }

    .table th,
    td {
        padding: 5px;
        text-align: left;
        border: 1px solid #ccc;
    }

    .light-heading th {
        background-color: #eeeeee
    }

    .green-heading th {
        background-color: #4CAF50;
        color: white;
    }

    .text-center {
        text-align: center;
    }

    .table-striped tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .text-danger {
        color: #a94442;
    }

    .text-success {
        color: #3c763d;
    }

</style>
<h3 class="text-center">
    {{ \Modules\Setting\Entities\Setting::where('setting_key', 'core.company_name')->first()->setting_value }}</h3>
<h3 class="text-center"> {{ trans_choice('loan::general.client_report', 1) }}</h3>
<table class="table table-bordered table-condensed table-hover">
    <thead>
        <tr>
            <th colspan="2">
                @if (!empty($data->first()) && !empty($branch_id))
                    {{ trans_choice('core::general.branch', 1) }}:

                    {{ $data->first()->branches }}
                @endif
            </th>
            <th colspan="12">

            </th>
            <th colspan="2"></th>
        </tr>
        <tr style="background-color: #D1F9FF">

            <th>{{ trans_choice('loan::general.client_id', 1) }}</th>
            <th>{{ trans_choice('loan::general.client_name', 1) }}</th>
            <th>{{ trans_choice('core::general.gender', 1) }}</th>
            <th>{{ trans_choice('core::general.age', 1) }}</th>
            <th>{{ trans_choice('core::general.mobile', 1) }}</th>
            <th>{{ trans_choice('core::general.email', 1) }}</th>
            <th>{{ trans_choice('core::general.status', 1) }}</th>
            <th>{{ trans_choice('core::general.created_at', 1) }}</th>
            <th>{{ trans_choice('core::general.branch', 1) }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $key)
            <tr>

                <td>
                    {{ $key->id }}
                </td>
                <td>
                    {{ $key->client }}
                </td>
                <td>{{ $key->gender }}</td>
                <td>{{ $key->age }}</td>
                <td>{{ $key->mobile }}</td>
                <td>{{ $key->email }}</td>
                <td>{{ $key->status }} </td>
                <td>{{ $key->created_at }}</td>
                <td>{{ $key->branches }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
    </tfoot>
</table>
