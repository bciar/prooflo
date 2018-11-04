<?php

namespace App\Services;

use App\Project;
use App\ProjectFile;
use App\Proof;
use App\Issue;
use App\Revision;
use App\Comment;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProofRequest;
use App\Contracts\IProofService;
use App\Services\FileService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProofService implements IProofService
{
    public function createProof($project_file)
    {
        try {
            $proof = Proof::store($project_file);
            if ($proof != null) {
                return $proof;
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getByRevisionId($revision_id)
    {
        $proofs = Proof::getByRevisionId($revision_id);
        return $proofs;
    }
    
    public function getByRevisionVersion($version)
    {
        $proofs = Proof::getByRevisionVersion($version);
        return $proofs;
    }

    public function saveProofState($data)
    {
        $proof = Proof::find($data['id']);
        if ($proof) {
            $proof->canvas_data = $data['canvas_data'];
            $proof->save();
            return $proof;
        }

    }
    public function changeProofStatus($proof_id, $status)
    {
        if ($proof_id > 0) {
            if ($status != '') {
                $proof = Proof::find($proof_id);
                $proof->status = $status;
                $proof->save();
                if ($status == 'approved') {
                    $issues = Issue::where('proof_id', $proof->id)->get();
                    if (count($issues) > 0) {
                        foreach ($issues as $key => $issue) {
                           /*  if ($issue->status != $status) { */
                                $issue->status = 'solved';
                                $issue->save();
                            /* } */
                        }
                    }
                }
                
                return $proof;
            }
        }
    }

    public function changeIssueStatus($issue_id, $status)
    {
        if ($issue_id > 0) {
            if ($status != '') {
                $issue = Issue::find($issue_id);
                $issue->status = $status;
                $issue->save();
                $left_issues = Issue::where('proof_id', $issue->proof_id)->where('id', '!=', $issue->id)->get();
                $pending_issues = 0;
                foreach ($left_issues as $key => $left_issue) {
                    if ($left_issue->status != 'approved') {
                        $pending_issues++;
                    }
                }
                if($pending_issues == 0){
                    return $this->changeProofStatus($issue->proof_id, 'approved');
                }
                
                return $issue;
            }
        }
    }

    public function createIssue($data)
    {
        try {
            if (array_key_exists('id', $data)) {
                $issue = Issue::with('projectFiles')->with('user')->findOrFail($data['id']);
            } else {
                $issue = new Issue();
                $issue->group = $data['group'];
                $issue->label = $data['label'];
                $issue->proof_id = $data['proof_id'];
                
            }
            $issue->description = $data['description'];
            $issue->status = 'pending';
            $issue->user_id = Auth::user()->id;
            $issue->save();
            $issue = Issue::with('projectFiles')->with('user')->find($issue->id);

            return $issue;

        } catch (\Exception $e) {
            
        }

    }

    public function addComment($data)
    {
        try {
            if (array_key_exists('id', $data)) {
                if ($data['id'] > 0) {
                    $comment = Comment::with('projectFiles')->with('user')->findOrFail($data['id']);
                }
            } else {
                $comment = new Comment();
            }
            $comment->issue_id = $data['issue_id'];
            $comment->description = $data['description'];
            $comment->user_id = Auth::user()->id;
            $comment->save();
            $comment = Comment::with('projectFiles')->with('user')->find($comment->id);
            return $comment;

        } catch (\Exception $e) {
            return null;
        }

    }
    //TODO DELETE ISSUES'S COMMENTS 
    public function deleteIssue($issue_id)
    {
        $fileService = new FileService();
        $issue = Issue::find($issue_id);
        if ($issue) {
            if (count($issue->comments) > 0) {
                foreach ($issue->comments as $key => $comment) {
                    if ($comment->project_files_id > 0) {
                        $fileService->deleteFile($comment->project_files_id);
                    }
                    $comment->delete();
                    $issue->save();
                }
            }
            if ($issue->project_files_id > 0) {
                $fileService->deleteFile($issue->project_files_id);
            }
            $issue->delete();
            return $issue;
        }
        return null;
    }

    public function deleteComment($comment_id)
    {
        $fileService = new FileService();
        $comment = Comment::find($comment_id);
        if ($comment) {
            if ($comment->project_files_id > 0) {
                $fileService->deleteFile($comment->project_files_id);
            }
            $comment->delete();
            return $comment;
        }
        return null;
    }
    public function addPictureToIssue($issue_id, $project_file)
    {
        try {
            $issue = Issue::findOrFail($issue_id);
            $issue->projectFiles()->associate($project_file);
            $issue->save();
            return $issue->projectFiles();
        } catch (ModelNotFoundException $e) {
            return null;
        }

    }
    public function addPictureToComment($comment_id, $project_file)
    {
        try {
            $comment = Comment::findOrFail($comment_id);
            $comment->projectFiles()->associate($project_file);
            $comment->save();
            return $comment->projectFiles();
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }
}