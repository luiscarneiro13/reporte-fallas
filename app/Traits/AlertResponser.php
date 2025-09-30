<?php

namespace App\Traits;

use Exception;

trait AlertResponser
{
    public function alertSuccess($index, $message)
    {
        return redirect()->route($index)->with(['state' => 'success', 'message' => $message]);
    }

    public function alertError($index, $message = 'Ha ocurrido un error')
    {
        return redirect()->route($index)->with(['state' => 'success', 'message' => $message]);
    }
}
