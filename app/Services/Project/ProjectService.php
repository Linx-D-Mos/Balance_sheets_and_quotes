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
     * Generate the next project code with format PRJ-YYYY-XXXX protected against race conditions.
     */
    protected function generateNextProjectCode(): string
    {
        $year = now()->year;
        $prefix = "PRJ-{$year}-";

        //Pesimist lock to avoid race conditions when generating the next project code
        $lastProject = Project::where('code', 'like', "{$prefix}%")
            ->lockForUpdate()
            ->orderByDesc('code')
            ->first();

        if (! $lastProject) {
            return $prefix . '0001';
        } else {
            $parts = explode('-', $lastProject->code);
            $sequence = isset($parts[2]) ? ((int) $parts[2]) + 1 : 1;
        }
        return sprintf('%s%04d', $prefix, $sequence);
    }
}
