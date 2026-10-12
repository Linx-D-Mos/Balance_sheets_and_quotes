<?php

namespace App\Services\Project;

use App\Enums\ProjectStatusEnum;
use App\Models\Project;
use App\Models\ProjectStatus;
use Illuminate\Support\Facades\DB;

class ProjectService
{
    /**
     * Save a new project to the database.
     *
     * @param array $data
     * @return Project
     */
    public function save(array $data): Project
    {
        return DB::transaction(function () use ($data) {
            $draftStatus = ProjectStatus::ofCode(ProjectStatusEnum::DRAFT)->firstOrFail();

            $code = $this->generateNextProjectCode();

            return Project::create([
                'client_id' => $data['client_id'],
                'project_status_id' => $draftStatus->id,
                'code' => $code,
                'title' => $data['title'],
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'project_description' => $data['project_description'] ?? null,
                'actual_start_date' => null,
                'actual_end_date' => null,
            ]);
        });
    }

    /**
     * Actualiza la ficha operativa del proyecto protegiendo la inmutabilidad
     * cliente, código, estado y fechas operativas de faena.
     *
     * @param Project $project
     * @param array $data
     * @return Project
     */
    public function update(Project $project, array $data): Project
    {
        return DB::transaction(function () use ($project, $data) {
            $project->update([
                'title' => $data['title'] ?? $project->title,
                'address' => $data['address'] ?? $project->address,
                'city' => $data['city'] ?? $project->city,
                'state' => $data['state'] ?? $project->state,
                'project_description' => $data['project_description'] ?? $project->project_description,
            ]);

            return $project->refresh();
        });
    }
    /**
     * Generate the next project code with format PRJ-YYYY-XXXX protected against race conditions.
     */
    protected function generateNextProjectCode(): string
    {
        $year = now()->year;
        $prefix = "PRJ-{$year}-";

        $maxSequence = Project::where('code', 'LIKE', "{$prefix}%")
            ->lockForUpdate()
            ->selectRaw("COALESCE(MAX(CAST(SUBSTRING(code FROM '[0-9]+$') AS INTEGER)), 0) as max_seq")
            ->value('max_seq');

        $nextSeq = ((int) $maxSequence) + 1;

        return sprintf('%s%04d', $prefix, $nextSeq);
    }
}
