<?php

namespace App\Http\Resources;

use DateTime;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;


class SurveyResourceDashboard extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'image_url'   => $this->image ? URL::to($this->image) : null,
            'title'       => $this->title,
            'slug'        => $this->slug,
            'status'      => $this->status !== 'draft',
            'questions'   => $this->questions()->count(),
            'answers'     => $this->answers()->count(),
            'created_at'  => (new DateTime($this->created_at))->format('Y-m-d H:i:s'),
            'expiry_date' => (new \DateTime($this->expiry_date))->format('Y-m-d'),
         ];
    }
}
