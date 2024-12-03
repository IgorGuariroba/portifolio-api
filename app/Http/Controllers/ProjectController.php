<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $projects = $this->getProjects($request);
            $projects->getCollection()->transform(function ($project) {
                return $this->transformProject($project);
            });

            return $this->createResponse($projects);
        } catch (\Exception $e) {
            return $this->createErrorResponse($e);
        }
    }

    private function getProjects(Request $request)
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        $sort = $request->input('sort', 'id');
        $order = $request->input('order', 'asc');

        return Project::orderBy($sort, $order)->paginate($perPage, ['*'], 'page', $page);
    }

    private function transformProject($project)
    {
        return [
            'id' => $project->id,
            'title' => $project->title,
            'description' => $project->description,
            'technologies' => $project->technologies,
            'link' => $project->link,
            'image_path' => $project->image_path,
            'links' => [
                'self' => route('projects.show', ['id' => $project->id]),
                'update' => route('admin.projects.update', ['id' => $project->id]),
                'delete' => route('admin.projects.destroy', ['id' => $project->id]),
            ],
        ];
    }

    private function createResponse($projects): JsonResponse
    {
        return response()->json([
            'data' => $projects->items(),
            'current_page' => $projects->currentPage(),
            'last_page' => $projects->lastPage(),
            'per_page' => $projects->perPage(),
            'total' => $projects->total(),
            'links' => [
                'self' => $projects->url($projects->currentPage()),
                'first' => $projects->url(1),
                'last' => $projects->url($projects->lastPage()),
                'prev' => $projects->previousPageUrl(),
                'next' => $projects->nextPageUrl(),
            ],
        ]);
    }

    private function createErrorResponse(\Exception $e): JsonResponse
    {
        return response()->json([
            'error' => 'Falha ao buscar os projetos.'
        ], 500);
    }
}
