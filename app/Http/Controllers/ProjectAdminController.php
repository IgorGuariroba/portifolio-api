<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectAdminController extends Controller
{
    public function store(Request $request)
    {
        // Validação dos dados recebidos
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'technologies' => 'required|array',
            'link' => 'nullable|url',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Verifica se a validação falhou
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Upload da imagem, se houver, com tratamento de erro
        $imagePath = null;
        try {
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('projects', 'public');
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Falha no upload da imagem.'], 500);
        }

        // Criação do projeto
        $project = Project::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'technologies' => $request->input('technologies'), // Se a coluna for JSON
            'link' => $request->input('link', ''),
            'image_path' => $imagePath,
        ]);

        // Retorna a resposta com o projeto criado
        return response()->json(['message' => 'Projeto criado com sucesso.', 'project' => $project], 201);
    }
}
