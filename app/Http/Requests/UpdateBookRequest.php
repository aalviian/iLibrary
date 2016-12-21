<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class UpdateBookRequest extends StoreBookRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules['title'] = 'required|unique:books,title,'.$this->route('books');
        $rules['cover'] = 'required';
        return $rules;
    }
}
