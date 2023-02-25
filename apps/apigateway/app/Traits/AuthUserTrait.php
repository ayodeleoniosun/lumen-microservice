<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait AuthUserTrait
{
    public function attachUserToPayload(Request $request): array {
        $userId = auth()->user()->id;
        $payload = $request->all();
        $payload['user_id'] = $userId;

        return $payload;
    }
}
