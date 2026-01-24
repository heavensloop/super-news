<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Psl\Type;

class VerifyEmailRequest extends FormRequest
{
    private User $requestUser;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->id;

        $id = Type\int()->coerce($this->id);
        $this->requestUser = Type\instance_of(\App\Models\User::class)->assert(User::find($id));

        if (! hash_equals((string) $this->requestUser->getKey(), (string) $this->get('id'))) {
            return false;
        }

        if (! hash_equals(sha1($this->requestUser->getEmailForVerification()), (string) $this->get('hash'))) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    public function user($guard = null)
    {
        return $this->requestUser;
    }
}
