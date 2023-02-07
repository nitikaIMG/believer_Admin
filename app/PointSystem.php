<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class PointSystem extends Model
{
    protected $table = 'point_system';

    protected $guarded = [];
    
    public function batting($type) {
        
        return DB::table('point_system')
                    ->where('fantasy_type', $this->fantasy_type)
                    ->where('format', $this->format)
                    ->where('type', $type)
                    ->select(
                        'run',
                        'boundary_bonus',
                        'six_bonus',
                        'half_century_bonus',
                        'century_bonus',
                        'dismissal_for_a_duck'
                    )
                    ->get();
    }
    
    public function bowling($type) {
        
        return DB::table('point_system')
                    ->where('fantasy_type', $this->fantasy_type)
                    ->where('format', $this->format)
                    ->where('type', $type)
                    ->select(
                        'wicket',
                        '2_wicket_haul_bonus',
                        '3_wicket_haul_bonus',
                        '4_wicket_haul_bonus',
                        '5_wicket_haul_bonus',
                        'maiden_over'
                    )
                    ->get();
    }
    
    public function fielding($type) {
        
        return DB::table('point_system')
                    ->where('fantasy_type', $this->fantasy_type)
                    ->where('format', $this->format)
                    ->where('type', $type)
                    ->select(
                        'catch',
                        'stumping_run_out',
                        'run_out_thrower',
                        'run_out_catcher'
                    )
                    ->get();
    }
    
    public function others($type) {
        
        return DB::table('point_system')
                    ->where('fantasy_type', $this->fantasy_type)
                    ->where('format', $this->format)
                    ->where('type', $type)
                    ->select(
                        'captain',
                        'vice_captain',
                        'in_starting_11'
                    )
                    ->get();
    }
    
    public function economy_rate_below($type) {
        
        return DB::table('point_system')
                    ->where('fantasy_type', $this->fantasy_type)
                    ->where('format', $this->format)
                    ->where('type', $type)
                    ->where('below', '!=', 0)
                    ->select(
                        'below',
                        'point'
                    )
                    ->first();
    }
    
    public function economy_rate_between($type) {
        
        return DB::table('point_system')
                    ->where('fantasy_type', $this->fantasy_type)
                    ->where('format', $this->format)
                    ->where('type', $type)
                    ->where('from', '!=', 0)
                    ->where('to', '!=', 0)
                    ->select(
                        'from',
                        'to',
                        'point'
                    )
                    ->get();
    }
    
    public function economy_rate_above($type) {
        
        return DB::table('point_system')
                    ->where('fantasy_type', $this->fantasy_type)
                    ->where('format', $this->format)
                    ->where('type', $type)
                    ->where('above', '!=', 0)
                    ->select(
                        'above',
                        'point'
                    )
                    ->first();
    }
    
    public function economy_rate_min_over($type) {
        
        return DB::table('point_system')
                    ->where('fantasy_type', $this->fantasy_type)
                    ->where('format', $this->format)
                    ->where('type', $type)
                    ->where('economy_rate_min_over', '!=', 0)
                    ->value('economy_rate_min_over');
    }
    
    public function strike_rate_min_ball($type) {
        
        return DB::table('point_system')
                    ->where('fantasy_type', $this->fantasy_type)
                    ->where('format', $this->format)
                    ->where('type', $type)
                    ->where('strike_rate_min_ball', '!=', 0)
                    ->value('strike_rate_min_ball');
    }
    
    public function strike_rate($type) {
        
        return DB::table('point_system')
                    ->where('fantasy_type', $this->fantasy_type)
                    ->where('format', $this->format)
                    ->where('type', $type)
                    ->select(
                        'strike_rate_min_ball',
                        'below',
                        'from',
                        'to'
                    )
                    ->get();
    }
    
    public function strike_rate_below($type) {
        
        return DB::table('point_system')
                    ->where('fantasy_type', $this->fantasy_type)
                    ->where('format', $this->format)
                    ->where('type', $type)
                    ->where('below', '!=', 0)
                    ->select(
                        'below',
                        'point'
                    )
                    ->first();
    }

    public function strike_rate_above($type) {
        
        return DB::table('point_system')
                    ->where('fantasy_type', $this->fantasy_type)
                    ->where('format', $this->format)
                    ->where('type', $type)
                    ->where('above', '!=', 0)
                    ->select(
                        'above',
                        'point'
                    )
                    ->first();
    }
    
    public function strike_rate_between($type) {
        
        return DB::table('point_system')
                    ->where('fantasy_type', $this->fantasy_type)
                    ->where('format', $this->format)
                    ->where('type', $type)
                    ->where('from', '!=', 0)
                    ->where('to', '!=', 0)
                    ->select(
                        'from',
                        'to',
                        'point'
                    )
                    ->get();
    }
    
    public function type($type, $field)
    {
        $field_value = DB::table('point_system')
                        ->where('fantasy_type', $this->fantasy_type)
                        ->where('format', $this->format)
                        ->where('type', $type)
                        ->where($field, '!=', 0)
                        ->value($field);
        
        if(!empty($field_value)) {
            return $field_value;
        } else {
            return 0;
        }
    }
    
    public function economy_rate_where_between($type, $from, $to) {
        
        return DB::table('point_system')
                    ->where('fantasy_type', $this->fantasy_type)
                    ->where('format', $this->format)
                    ->where('type', $type)
                    ->where('from', $from)
                    ->where('to', $to)
                    ->value('point');
    }
    
    public function strike_rate_where_between($type, $from, $to) {
        
        return DB::table('point_system')
                    ->where('fantasy_type', $this->fantasy_type)
                    ->where('format', $this->format)
                    ->where('type', $type)
                    ->where('from', $from)
                    ->where('to', $to)
                    ->value('point');
    }
    
    public function below_or_above_point($type, $below_or_above, $value) {
        
        return DB::table('point_system')
                    ->where('fantasy_type', $this->fantasy_type)
                    ->where('format', $this->format)
                    ->where('type', $type)
                    ->where($below_or_above, $value)
                    ->value('point');
    }
    
}
