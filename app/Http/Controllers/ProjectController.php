<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    private const DEFAULT_PAGE = 1;
    private const DEFAULT_PER_PAGE = 10;
    private const DEFAULT_SORT = 'id';
    private const DEFAULT_ORDER = 'asc';

    public function index(Request $request): JsonResponse
    {
        try {
            $projects = $this->fetchProjects($request);
            $projects->getCollection()->transform(fn($project) => $this->transformProjectData($project));

            return $this->buildSuccessResponse($projects);
        } catch (\Exception $e) {
            return $this->buildErrorResponse($e);
        }
    }

    private function fetchProjects(Request $request)
    {
        $page = $request->input('page', self::DEFAULT_PAGE);
        $perPage = $request->input('per_page', self::DEFAULT_PER_PAGE);
        $sort = $request->input('sort', self::DEFAULT_SORT);
        $order = $request->input('order', self::DEFAULT_ORDER);

        return Project::orderBy($sort, $order)->paginate($perPage, ['*'], 'page', $page);
    }

    private function transformProjectData($project): array
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

    private function buildSuccessResponse($projects): JsonResponse
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

    private function buildErrorResponse(\Exception $e): JsonResponse
    {
        return response()->json([
            'error' => 'Failed to fetch projects.',
            'message' => $e->getMessage()
        ], 500);
    }
}
