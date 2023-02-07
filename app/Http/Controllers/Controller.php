<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    function validationHandle($validation)
    {
        foreach ($validation->getMessages() as $field_name => $messages){
            if(!isset($firstError)){
                $firstError = $messages[0];
            }
            //$errors[$field_name] = $messages[0];
        }
        return $firstError;
    }
}
