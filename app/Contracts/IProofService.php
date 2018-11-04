<?php

namespace App\Contracts;

interface IProofService
{
   public function createProof($project_file);
   public function getByRevisionId($revision_id);
   public function getByRevisionVersion($version);
   public function saveProofState($data);
   public function createIssue($data);
   public function deleteIssue($issue_id);
   public function addComment($data);
   public function deleteComment($comment_id);
   public function addPictureToIssue($issue_id, $project_file);
   public function addPictureToComment($comment_id, $project_file);
   public function changeProofStatus($proof_id, $status);
   public function changeIssueStatus($issue_id, $status);
}