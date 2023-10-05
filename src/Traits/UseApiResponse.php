<?php

namespace Softok2\RestApiClient\Traits;

trait UseApiResponse
{
    /**
     * @return mixed
     */
    public function onError()
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Something went wrong'
        ], 500);
    }


    /**
     * @return mixed
     */
    abstract public function onSuccess(): mixed;
}
