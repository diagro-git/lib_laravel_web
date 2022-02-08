<?php
namespace Diagro\Web\Exception;

use Exception;
use Illuminate\Http\Request;

class InvalidFrontAppIdException extends Exception
{


    public function report() {}


    public function render(Request $request)
    {
        return response()->view('diagro::errors.invalid_front_app_id');
    }


}