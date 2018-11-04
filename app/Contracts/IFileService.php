<?php

namespace App\Contracts;

interface IFileService
{
   public function uploadFile(array $data);
   public function deleteFile($id);
   public function getById($id);
}