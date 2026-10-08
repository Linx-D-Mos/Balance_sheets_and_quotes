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
        return DB::transaction(function () use ($data){
            $draftStatus = ProjectStatus::ofCode(ProjectStatusEnum::DRAFT)->firstOrFail();
        });
    }
}
