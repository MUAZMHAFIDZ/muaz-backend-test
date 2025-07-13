<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    protected string $token;
    protected string $message;

    public function __construct($resource, string $token, string $message = 'Login berhasil.')
    {
        parent::__construct($resource);
        $this->token = $token;
        $this->message = $message;
    }

    public function toArray(Request $request): array
    {
        return [
            'status' => 'success',
            'message' => $this->message,
            'data' => [
                'token' => $this->token,
                'admin' => [
                    'id' => $this->id,
                    'name' => $this->name,
                    'username' => $this->username,
                    'phone' => $this->phone,
                    'email' => $this->email,
                ],
            ]
        ];
    }
}
