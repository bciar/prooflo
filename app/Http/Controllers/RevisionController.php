<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Project;
use App\ProjectFile;
use App\Proof;

use App\Contracts\IRevisionService;
use App\Contracts\IProofService;
use App\Helpers\ApiResponse;

class RevisionController extends Controller
{
    private $revisionService;
    private $proofService;

    public function __construct(IRevisionService $revisionService)
    {
        $this->revisionService = $revisionService;
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'project_id' => 'required|numeric',
        ]);

        $project_id = $request->only('project_id');
        if ($project_id > 0) {
            $revision = $this->revisionService->create($project_id);
            if ($revision) {
                return ApiResponse::success('Revision created successfully', $revision);
            }
        }
        return ApiResponse::error('001', 'Error creating revision. There is an open revision in this project already.');
    }

    public function getRevisionsByProject($project_id)
    {
        if ($project_id > 0) {
            $revisions = $this->revisionService->getRevisionByProject($project_id);
            if ($revisions) {
                return ApiResponse::success('', $revisions);
            }
        }
        return ApiResponse::error('001', 'Error getting project revisions');
    }

    public function changeRevisionStatus($revision_id, $status)
    {
        if ($project_id > 0 && $version > 0 && $status != '') {
            $revision = $this->revisionService->changeRevisionStatus($revision_id, $status);
            if ($revision) {
                return ApiResponse::success('Revision status updated successfully', $revision);
            }
        }
        return ApiResponse::error('001', 'Error updating revision status');
    }

    public function loadRevisionById($revision_id)
    {
        $revision = $this->revisionService->getRevisionById($revision_id);
        if ($revision) {
            return ApiResponse::success('Revision loaded successfully', $revision);
        }
        return ApiResponse::error('001', 'Error loading the revision');
    }
}
