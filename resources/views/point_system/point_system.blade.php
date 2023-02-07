@extends('main')

@section('heading')
    Point System Manager
@endsection('heading')

@section('sub-heading')
    Update Point System
@endsection('sub-heading')

@section('content')


<div class="card mb-4">
    <div class="card-header">Update Point System</div>
    <div class="card-body">
      <div class="card-title">Point System</div>
          <ul class="nav nav-tabs" id="myTab" role="tablist">
              
              @if(!empty($all_point_system))
                  
                  @php
                      $i = 0;
                  @endphp
                  
                  @foreach($all_point_system as $point_system)
                          
                      <li class="nav-item" role="presentation">
                          <a class="nav-link text-capitalize
                              @if($i == 0)
                                  active
                              @endif
                          " id="{{str_replace(' ', '_', $point_system->format) ?? ''}}-tab" data-toggle="tab" href="#{{str_replace(' ', '_', $point_system->format) ?? ''}}" role="tab" aria-controls="{{str_replace(' ', '_', $point_system->format) ?? ''}}" aria-selected="true">{{$point_system->format ?? ''}}</a>
                      </li>
                      
                      @php
                          $i += 1;
                      @endphp
                      
                  @endforeach
              @endif
          </ul>
          
          <div class="tab-content" id="myTabContent">
              @if(!empty($all_point_system))
                  
                  @php
                      $i = 0;
                  @endphp
                  
                  @foreach($all_point_system as $point_system)
                      <div class="tab-pane fade show 
                              @if($i == 0)
                                  active
                              @endif" id="{{str_replace(' ', '_', $point_system->format) ?? ''}}" role="tabpanel" aria-labelledby="{{str_replace(' ', '_', $point_system->format) ?? ''}}-tab">
                          <div id="accordion">
                            <!--batting-->
                            <div class="card">
                              <div class="card-header" id="headingOne">
                                <h5 class="mb-0">
                                  <div data-toggle="collapse" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne" style="cursor:pointer;">
                                    Batting
                                  </div>
                                </h5>
                              </div>
                          
                              <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                                <div class="card-body">
                                  
                                  @if(!empty($point_system->batting('batting')))
                                      @foreach($point_system->batting('batting') as $batting)
                                          @foreach($batting as $bat => $points)
                                              <div class="row">
                                                  <div class="float-left text-uppercase col text-bold">{{str_replace('_', ' ', $bat)}}</div>
                                                  <div class="float-right text-uppercase col text-bold">
                                                      
                                                          <input value="{{$points}}" class="form-control" onchange="update_point_system('cricket', '{{$point_system->format}}', 'batting', '{{$bat}}', this.value)">
                                                      
                                                  </div>
                                              </div>
                                          @endforeach
                                      @endforeach
                                  @endif
                                  
                                </div>
                              </div>
                            </div>
                            
                            <!--Bowling-->
                            <div class="card">
                              <div class="card-header" id="headingOneBowling">
                                <h5 class="mb-0">
                                  <div data-toggle="collapse" data-target="#collapseOneBowling" aria-expanded="false" aria-controls="collapseOneBowling" style="cursor:pointer;">
                                    Bowling
                                  </div>
                                </h5>
                              </div>
                          
                              <div id="collapseOneBowling" class="collapse" aria-labelledby="headingOneBowling" data-parent="#accordion">
                                <div class="card-body">
                                  
                                  @if(!empty($point_system->bowling('bowling')))
                                      @foreach($point_system->bowling('bowling') as $bowling)
                                          @foreach($bowling as $ball => $points)
                                              @if($points != 0)
                                              <div class="row">
                                                  <div class="float-left text-uppercase col text-bold">{{str_replace('_', ' ', $ball)}}</div>
                                                  <div class="float-right text-uppercase col text-bold">
                                                      
                                                          <input value="{{$points}}" class="form-control" onchange="update_point_system('cricket', '{{$point_system->format}}', 'bowling', '{{$ball}}', this.value)">
                                                      
                                                  </div>
                                              </div>
                                              @endif
                                          @endforeach
                                      @endforeach
                                  @endif
                                  
                                </div>
                              </div>
                            </div>
                            
                            <!--fielding-->
                            <div class="card">
                              <div class="card-header" id="headingOnefielding">
                                <h5 class="mb-0">
                                  <div data-toggle="collapse" data-target="#collapseOnefielding" aria-expanded="false" aria-controls="collapseOnefielding" style="cursor:pointer;">
                                    Fielding
                                  </div>
                                </h5>
                              </div>
                          
                              <div id="collapseOnefielding" class="collapse" aria-labelledby="headingOnefielding" data-parent="#accordion">
                                <div class="card-body">
                                  
                                  @if(!empty($point_system->fielding('fielding')))
                                      @foreach($point_system->fielding('fielding') as $fielding)
                                          @foreach($fielding as $ball => $points)
                                              @if($points != 0)
                                              <div class="row">
                                                  <div class="float-left text-uppercase col text-bold">{{str_replace('_', ' ', $ball)}}</div>
                                                  <div class="float-right text-uppercase col text-bold">
                                                      
                                                          <input value="{{$points}}" class="form-control" onchange="update_point_system('cricket', '{{$point_system->format}}', 'fielding', '{{$ball}}', this.value)">
                                                      
                                                  </div>
                                              </div>
                                              @endif
                                          @endforeach
                                      @endforeach
                                  @endif
                                  
                                </div>
                              </div>
                            </div>
                            
                            <!--others-->
                            <div class="card">
                              <div class="card-header" id="headingOneothers">
                                <h5 class="mb-0">
                                  <div data-toggle="collapse" data-target="#collapseOneothers" aria-expanded="false" aria-controls="collapseOneothers" style="cursor:pointer;">
                                    Others
                                  </div>
                                </h5>
                              </div>
                          
                              <div id="collapseOneothers" class="collapse" aria-labelledby="headingOneothers" data-parent="#accordion">
                                <div class="card-body">
                                  
                                  @if(!empty($point_system->others('others')))
                                      @foreach($point_system->others('others') as $others)
                                          @foreach($others as $ball => $points)
                                              @if($points != 0)
                                              <div class="row">
                                                  <div class="float-left text-uppercase col text-bold">{{str_replace('_', ' ', $ball)}}</div>
                                                  <div class="float-right text-uppercase col text-bold">
                                                      
                                                          <input value="{{$points}}" class="form-control" onchange="update_point_system('cricket', '{{$point_system->format}}', 'others', '{{$ball}}', this.value)">
                                                      
                                                  </div>
                                              </div>
                                              @endif
                                          @endforeach
                                      @endforeach
                                  @endif
                                  
                                </div>
                              </div>
                            </div>
                            
                            @if(!empty($point_system->economy_rate_below('economy rate')))
                            <!--economy rate-->
                            <div class="card">
                              <div class="card-header" id="headingOneotherserate">
                                <h5 class="mb-0">
                                  <div data-toggle="collapse" data-target="#collapseOneotherserate" aria-expanded="false" aria-controls="collapseOneotherserate" style="cursor:pointer;">
                                    Economy Rate
                                  </div>
                                </h5>&nbsp;
                                <div class="row col">
                                    (Min {{$point_system->economy_rate_min_over('economy rate') ?? 0}} Overs To Be Bowled)
                                </div>
                              </div>
                          
                              <div id="collapseOneotherserate" class="collapse" aria-labelledby="headingOneotherserate" data-parent="#accordion">
                                <div class="card-body">
                                  
                                  @if(!empty($point_system->economy_rate_below('economy rate')))
                                      <div class="row">
                                          <div class="float-left text-uppercase col text-bold">
                                              Below {{$point_system->economy_rate_below('economy rate')->below}} runs per over
                                          </div>
                                          <div class="float-right text-uppercase col text-bold">
                                              
                                                  <input value="{{$point_system->economy_rate_below('economy rate')->point}}" class="form-control"
                                                  onchange="update_point_system('cricket', '{{$point_system->format}}', 'economy rate', '', this.value, '', '', '{{$point_system->economy_rate_below('economy rate')->below}}', '')"
                                                  >
                                              
                                          </div>
                                      </div>
                                  @endif
                                  
                                  
                                  @if(!empty($point_system->economy_rate_between('economy rate')))
                                      @foreach($point_system->economy_rate_between('economy rate') as $economy_rate)
                                          <div class="row">
                                              <div class="float-left text-uppercase col text-bold">
                                                  Between {{$economy_rate->from}}-{{$economy_rate->to}} runs per over
                                              </div>
                                              <div class="float-right text-uppercase col text-bold">
                                                  
                                                      <input value="{{$economy_rate->point}}" class="form-control"
                                                      onchange="update_point_system('cricket', '{{$point_system->format}}', 'economy rate', '', this.value, '{{$economy_rate->from}}', '{{$economy_rate->to}}', '', '')"
                                                  >
                                                  
                                              </div>
                                          </div>
                                      @endforeach
                                  @endif
                                  
                                  @if(!empty($point_system->economy_rate_above('economy rate')))
                                      <div class="row">
                                          <div class="float-left text-uppercase col text-bold">
                                              Above {{$point_system->economy_rate_above('economy rate')->above}} runs per over
                                          </div>
                                          <div class="float-right text-uppercase col text-bold">
                                              
                                                  <input value="{{$point_system->economy_rate_above('economy rate')->point}}" class="form-control"
                                                  onchange="update_point_system('cricket', '{{$point_system->format}}', 'economy rate', '', this.value, '', '', '', '{{$point_system->economy_rate_above('economy rate')->above}}')"
                                                  >
                                              
                                          </div>
                                      </div>
                                  @endif
                                  
                                </div>
                              </div>
                            </div>
                            @endif
                            
                            @if(!empty($point_system->strike_rate_min_ball('strike rate')))
                            <!--strike_rate rate-->
                            <div class="card">
                              <div class="card-header" id="headingOneothersestrike_rate">
                                <h5 class="mb-0">
                                  <div data-toggle="collapse" data-target="#collapseOneothersstrike_rate" aria-expanded="false" aria-controls="collapseOneothersstrike_rate" style="cursor:pointer;">
                                    Strike Rate
                                  </div>
                                </h5>&nbsp;
                                <div class="row col">
                                    (Min {{$point_system->strike_rate_min_ball('strike rate') ?? 0}} Balls To Be Played)
                                </div>
                              </div>
                          
                              <div id="collapseOneothersstrike_rate" class="collapse" aria-labelledby="collapseOneothersstrike_rate" data-parent="#accordion">
                                <div class="card-body">
                                  
                                  @if(!empty($point_system->strike_rate_below('strike rate')))
                                  <div class="row">
                                      <div class="float-left text-uppercase col text-bold">
                                          Below {{$point_system->strike_rate_below('strike rate')->below}} runs per 100 balls
                                      </div>
                                      <div class="float-right text-uppercase col text-bold">
                                          
                                              <input value="{{$point_system->strike_rate_below('strike rate')->point}}" class="form-control"
                                              onchange="update_point_system('cricket', '{{$point_system->format}}', 'economy rate', '', this.value, '', '', '{{$point_system->strike_rate_below('strike rate')->below}}', '')"
                                              >
                                          
                                      </div>
                                  </div>
                                @endif

                                  @if(!empty($point_system->strike_rate_between('strike rate')))
                                      @foreach($point_system->strike_rate_between('strike rate') as $strike_rate)
                                          <div class="row">
                                              <div class="float-left text-uppercase col text-bold">
                                                  Between {{$strike_rate->from}}-{{$strike_rate->to}} runs per 100 balls
                                              </div>
                                              <div class="float-right text-uppercase col text-bold">
                                                  
                                                      <input value="{{$strike_rate->point}}" class="form-control"
                                                      onchange="update_point_system('cricket', '{{$point_system->format}}', 'strike rate', '', this.value, '{{$strike_rate->from}}', '{{$strike_rate->to}}', '', '')"
                                                  >
                                                  
                                              </div>
                                          </div>
                                      @endforeach
                                  @endif
                                  
                                 

                                  @if(!empty($point_system->strike_rate_above('strike rate')))
                                          <div class="row">
                                              <div class="float-left text-uppercase col text-bold">
                                                  Above {{$point_system->strike_rate_above('strike rate')->above}} runs per 100 balls
                                              </div>
                                              <div class="float-right text-uppercase col text-bold">
                                                  
                                                <input value="{{$point_system->strike_rate_above('strike rate')->point}}" class="form-control"
                                                onchange="update_point_system('cricket', '{{$point_system->format}}', 'economy rate', '', this.value, '', '', '{{$point_system->strike_rate_above('strike rate')->above}}', '')"
                                                >
                                                  
                                              </div>
                                          </div>
                                  @endif
                                </div>
                              </div>
                            </div>
                            @endif
                          </div>
                      </div>
                      
                      @php
                          $i += 1;
                      @endphp
                      
                  @endforeach
              @endif
                      
          </div>
      </div>
    </div>
</div>
</div>


<script>  
function update_point_system(fantasy_type, format, type, field, point, from = '', to = '', below = '', above = ''){

  @php
    $permissions_string = auth()->user()->permissions;
    $permissions_array = explode(',', $permissions_string);
  @endphp

  @if( in_array("PointSystemController@update_point_system" ,$permissions_array ) || Auth::user()->role == '1' || $permissions_string == '*')

  Swal.fire(point);
  $.ajax({
     type:'POST',
     url:'<?php echo asset('/my-admin/update_point_system');?>',
     data:'_token=<?php echo csrf_token();?>&fantasy_type='+fantasy_type+'&format='+format+'&type='+type+'&field='+field+'&point='+point+'&from='+from+'&to='+to+'&below='+below+'&above='+above,
     success:function(data){
      if(data==1){
         location.reload();
      }
    }
  });

  @else   
    Swal.fire("Sorry you can't update points as you don't have permission");
    setTimeout(() => {
      location.reload();
    }, 1500);
  @endif
}


</script>

@endsection('content')
