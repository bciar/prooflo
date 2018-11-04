<?php

namespace App\Contracts;

interface IProjectService
{
   public function createProject(array $data);
   public function createSendProject(array $data);
   public function getActiveRevision($project_id);
   public function getRevisionVersions($project_id);
   public function sendProject($project_id, $user_type);
   public function submitDesicion(array $data);
   public function sendBackRevision($project_id, $user_type);
   public function inviteCollaborator($email, $project_id);
}