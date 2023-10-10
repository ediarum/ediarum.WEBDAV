<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectRequest extends FormRequest
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
        $project_id = $this->route('project');

        return [
            'name' => ['required', Rule::unique('projects')->ignore($project_id)],
            'slug' => ['required','alpha_dash:ascii','lowercase', Rule::unique('projects')->ignore($project_id)],
            'data_folder_location' => 'required',
            'gitlab_url' => 'nullable',
            'gitlab_username' => 'nullable',
            'gitlab_personal_access_token' => 'nullable',
            'ediarum_backend_url' => 'nullable',
            'ediarum_backend_api_key' => 'nullable',
            'exist_base_url' => 'nullable',
            'exist_data_path' => 'nullable',
            'exist_username' => 'nullable',
            'exist_password' => 'nullable',
        ];
    }
}
