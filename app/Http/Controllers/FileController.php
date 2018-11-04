<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\FileRequest;
use App\Contracts\IFileService;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;

class FileController extends Controller
{
    private $fileService;

    public function __construct(IFileService $fileService)
    {
        $this->fileService = $fileService;
    }
    public function upload(FileRequest $request)
    {
        $project_file = $this->fileService->uploadFile($request->all());
        if ($project_file) {
            $result['id'] = $project_file->id;
            $result['path'] = $project_file->path;
            $result['thumb'] = $project_file->thumb_path;

            return ApiResponse::success('File saved successfully', $result);
        } else {
            return ApiResponse::error('001', 'Error saving file');
        }
    }

    public function deleteFile($id)
    {
       if($id > 0){
        $delete_result = $this->fileService->deleteFile($id);
        if ($delete_result) {
            $object_type = get_class($delete_result);
            $reflection = new \ReflectionClass($object_type);
            return ApiResponse::success('File removed successfully', $reflection->getShortName());
          }
       }
        return ApiResponse::error('001', 'Error removing the file');
    }

    public function getById($file_id)
    {
        if ($file_id > 0) {
            $file = $this->fileService->getById($file_id);
            if ($file) {
                return ApiResponse::success('Resource resolved successfully', $file);
            }
        }
        return ApiResponse::error('001', 'Error resolving the file');
    }
}