<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VideoFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'post_type' => ['required','in:text,image,video'],
            'video_url' => ['string','nullable'],
            'image_url' => ['string','nullable'],
            'text' => ['string','nullable'],
            'caption' => ['string','nullable'],
        ];
    }
}
