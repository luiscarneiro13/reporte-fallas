<?php

namespace App\Traits;

use Exception;

trait ApiResponser
{
    public function success($data)
    {
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function error($data)
    {
        return response()->json(['success' => false, 'data' => $data]);
    }
}
