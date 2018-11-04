<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Project;
use App\ProjectFile;
use App\Proof;
use App\Helpers\ApiResponse;

use App\Contracts\IRevisionService;
use App\Contracts\IProofService;
use App\Http\Requests\ProofRequest;

class ProofController extends Controller
{
    private $proofService;

    public function __construct(IProofService $proofService)
    {
        $this->proofService = $proofService;
    }

    public function saveProofState(ProofRequest $request)
    {
        $data = $request->all();
        $proof = $this->proofService->saveProofState($data);
        if ($proof) {
            return ApiResponse::success('Resource saved successfully', $proof);
        } else {
            return ApiResponse::error('001', 'Proof not found');
        }
    }

    public function createIssue(Request $request)
    {
        $validatedData = $request->validate([
            'proof_id' => 'sometimes',
            'description' => 'required',
            'group' => 'sometimes',
            'label' => 'sometimes',
            'id' => 'sometimes',
        ]);

        $data = $request->all();
        $issue = $this->proofService->createIssue($data);
        if ($issue) {
            return ApiResponse::success('Resource created successfully', $issue);
        } else {
            return ApiResponse::error('001', 'Error saving the issue');
        }
    }
    public function deleteIssue($issue_id)
    {
        if ($issue_id > 0) {
            $issue = $this->proofService->deleteIssue($issue_id);
            if ($issue) {
                return ApiResponse::success('Resource deleted successfully', $issue);
            }
            return ApiResponse::error('001', 'Error deleting the issue');
        }
    }

    public function deleteComment($comment_id)
    {
        if ($comment_id > 0) {
            $comment = $this->proofService->deleteComment($comment_id);
            if ($comment) {
                return ApiResponse::success('Resource deleted successfully', '');
            }
            return ApiResponse::error('001', 'Error deleting the comment');
        }
    }

    public function addComment(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'sometimes:integer',
            'issue_id' => 'required|integer',
            'description' => 'required',
        ]);
        $data = $request->all();
        $comment = $this->proofService->addComment($data);
        if ($comment) {
            return ApiResponse::success('Resource created successfully', $comment);
        } else {
            return ApiResponse::error('001', 'Error saving the comment');
        }
    }

    public function changeProofStatus($proof_id, $status)
    {
        if ($proof_id > 0 && $status != '') {
            $proof = $this->proofService->changeProofStatus($proof_id, $status);
            if ($proof) {
                return ApiResponse::success('Proof status updated successfully', $proof);
            }
        }
        return ApiResponse::error('001', 'Error updating proof status');
    }

    public function changeIssueStatus($issue_id, $status)
    {
        if ($issue_id > 0 && $status != '') {
            $issue = $this->proofService->changeIssueStatus($issue_id, $status);
            if ($issue) {
                return ApiResponse::success('Issue status updated successfully', $issue);
            }
        }
        return ApiResponse::error('001', 'Error updating issue status');
    }
}