<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportDataResource extends JsonResource
{
    /**
     * @param  \Illuminate\Http\Request  $request
     */
    public function toArray($request): array
    {
        $incident = $this->incident_datetime instanceof Carbon
            ? $this->incident_datetime
            : ($this->incident_datetime ? Carbon::parse($this->incident_datetime) : null);

        $created = $this->created_at instanceof Carbon
            ? $this->created_at
            : ($this->created_at ? Carbon::parse($this->created_at) : null);

        $finishRaw = $this->finish_time;
        if ($finishRaw instanceof Carbon) {
            $finish = $finishRaw;
        } elseif (is_numeric($finishRaw)) {
            $finish = Carbon::createFromTimestamp((int) $finishRaw);
        } elseif ($finishRaw) {
            $finish = Carbon::parse($finishRaw);
        } else {
            $finish = null;
        }

        return [
            'id' => $this->id,
            'code' => $this->code,
            'title' => $this->title,
            'category' => optional($this->category)->name ?? '-',
            'status' => $this->status,
            'incident_at' => $incident ? $incident->format('d/m/Y H:i') : '-',
            'province' => optional($this->province)->name ?? '-',
            'city' => optional($this->city)->name ?? '-',
            'district' => optional($this->district)->name ?? '-',
            'created_at' => $created ? $created->format('d/m/Y H:i') : '-',
            'finished_at' => $finish ? $finish->format('d/m/Y H:i') : '-',
            'action' => '<a href="' . route('pelaporan.show', $this->resource) . '" class="btn btn-sm btn-info">'
                . '<i class="fa fa-eye me-1"></i>Detail</a>',
        ];
    }
}
