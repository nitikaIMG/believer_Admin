@extends('main')

@section('heading')
    Card Player Manager
@endsection('heading')

@section('sub-heading')
    View All Contest Result
@endsection('sub-heading')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <div class="row w-100 align-items-center mx-0">
                <div class="col-md col-12 mb-md-0 mb-2 text-md-left text-center">View All Challenge Result</div>
                <div class="col-md-auto col-12 px-md-3 px-0 text-center">
                    {{-- <form action="<?php echo action('CardController@downloadallplayerdetails'); ?>" method="get">
                  <input type="hidden" name="team"  value="<?php echo $team; ?>">
                  <input type="hidden" name="playername"  value="<?php echo $playername; ?>">
                    <input type="hidden" name="role"  value="<?php echo $role; ?>">
                    <input type="hidden" name="fantasy_type"  value="<?php echo $fantasy_type; ?>">
                    <button type="submit" class="btn btn-secondary text-uppercase btn-sm rounded-pill" data-toggle="tooltip" title="Download All Player Details" font-weight-600><i class="fad fa-download"></i>&nbsp; Download</a></button>
                </form> --}}

                </div>
            </div>
        </div>
        <div class="card-body">


            @include('alert_msg')


            <div class="datatable table-responsive">
                <table class="table table-bordered table-hover last-btn-center text-nowrap" id="view_contestresult_datatable"
                    width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Sno.</th>
                            <th>Challenge Id</th>
                            <th>Entryfee</th>
                            <th>Win Amount</th>
                            <th>Bonus Percentage</th>
                            <th>Status</th>
                            <th>UserId1</th>
                            <th>UserId2</th>
                            <th>Team1</th>
                            <th>Team2</th>
                            <th>Winner</th>
                            <th class="myclass1">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Sno.</th>
                            <th>Challenge Id</th>
                            <th>Entryfee</th>
                            <th>Win Amount</th>
                            <th>Bonus Percentage</th>
                            <th>Status</th>
                            <th>UserId1</th>
                            <th>UserId2</th>
                            <th>Team1</th>
                            <th>Team2</th>
                            <th>Winner</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <script>
        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            // var fantasy_type = $('#fantasy_type').val();
            $.fn.dataTable.ext.errMode = 'none';
            $('#view_contestresult_datatable').DataTable({
                'responsive': false,
                'bFilter': false,
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": '<?php echo URL::asset('my-admin/view_contestresult_datatable'); ?>',
                    "dataType": "json",
                    "type": "POST",
                    "data": {
                        _token: "{{ csrf_token() }}"
                    }
                },
                "columns": [{
                        "data": "id"
                    },
                    {
                        "data": "challengeid"
                    },
                    {
                        "data": "entryfee"
                    }, 
                    {
                        "data": "win_amount"
                    },
                    {
                        "data": "bonus_percentage"
                    },
                    {
                        "data": "status"
                    },
                    {
                        "data": "user_id1"
                    },
                    {
                        "data": "user_id2"
                    },
                    {
                        "data": "team1id"
                    },
                    {
                        "data": "team2id"
                    },
                    {
                        "data": "winner"
                    },
                    {
                        "data": "action"
                    }
                ]

            });

        });
    </script>
@endsection('content')
