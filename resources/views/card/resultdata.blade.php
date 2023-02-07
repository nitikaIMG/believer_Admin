@extends('main')

@section('heading')
    Card Team Manager
@endsection('heading')

@section('sub-heading')
    View Challenge Result
@endsection('sub-heading')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <div class="row w-100 align-items-center mx-0">
                <div class="col-md col-12 mb-md-0 mb-2 text-md-left text-center">View All challenge Result</div>
                <div class="col-md-auto col-12 px-md-3 px-0 text-center">
                    {{-- <input type="hidden" id="fantasy_type"  value="<?php echo $fantasy_type; ?>">
                  <form action="<?php //echo action('CardController@downloadteamdata');
?>" method="get">
                    <input type="hidden" name="name"  value="<?php echo $name; ?>">
                    <input type="hidden" name="fantasy_type"  value="<?php echo $fantasy_type; ?>">
                    <button type="submit" class="btn btn-secondary text-uppercase btn-sm rounded-pill" data-toggle="tooltip" title="Download All Team Details" font-weight-600><i class="fad fa-download"></i>&nbsp; Download</button>
                </form> --}}
                </div>
            </div>
        </div>
        <div class="card-body">

            @include('alert_msg')

            <link rel="stylesheet" type="text/css"
                href="https://cdn.datatables.net/v/dt/dt-1.10.16/r-2.2.1/datatables.min.css">
            <div class="datatable table-responsive">
                <table class="table table-bordered table-striped table-hover text-center text-nowrap" width="100%"
                    cellspacing="0">
                    <?php if(!empty($gamedata->toArray())){ ?>
                    <thead>
                        <tr>
                            <th class="myclass">Sno.</th>
                            <th class="myclass1">Field</th>
                            <th class="myclass2">Player Name1</th>
                            <th class="myclass3">Player Name2</th>
                            <th class="myclass4">Field Value1</th>
                            <th class="myclass5">Field Value2</th>
                            <th class="myclass6">Won User</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($gamedata as $key=> $data){ 
                            $player1 = DB::connection('mysql2')->table('cardplayers')->where('id', $data->player_id1)->first();
                            $player2 = DB::connection('mysql2')->table('cardplayers')->where('id', $data->player_id2)->first();
                            $userdetail1 = DB::connection('mysql2')->table('registerusers')->where('id', $data->wonuser)->first();
                            ?>
                            <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $data->field }}</td>
                            <td>{{ $player1->player_name }}</td>
                            <td>{{ $player2->player_name }}</td>
                            <td>{{ $data->fieldvalue1 }}</td>
                            <td>{{ $data->fieldvalue2 }}</td>
                            <td>{{ $userdetail1->team }}</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="myclass">Sno.</th>
                            <th class="myclass1">Field</th>
                            <th class="myclass2">Player Name1</th>
                            <th class="myclass3">Player Name2</th>
                            <th class="myclass4">Field Value1</th>
                            <th class="myclass5">Field Value2</th>
                            <th class="myclass6">Won User</th>
                        </tr>
                    </tfoot>
                    <?php }else{ ?>
                    <h1 style="text-align: center;color:red">Data Not Available</h1>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
@endsection('content')
