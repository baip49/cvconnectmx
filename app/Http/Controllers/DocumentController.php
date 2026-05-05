<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\CandidateDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function show(Request $request, $slug)
    {
        $user = auth()->user();

        // 1. Verificar que el usuario está logueado
        if (! $user) {
            abort(403, 'Acceso restringido: Debes iniciar sesión.');
        }

        $document = CandidateDocument::where('slug', $slug)->firstOrFail();

        // 2. Lógica de autorización
        $isOwner = $user->candidate?->id === $document->candidate_id;
        $isAdmin = $user->role?->name === 'admin';

        $isCompanyWithApplication = false;
        if ($user->company) {
            $isCompanyWithApplication = Application::where('candidate_id', $document->candidate_id)
                ->whereHas('vacancy', function ($query) use ($user) {
                    $query->where('company_id', $user->company->id);
                })
                ->exists();
        }

        if (! ($isOwner || $isCompanyWithApplication || $isAdmin)) {
            abort(403, 'Acceso restringido: No tienes permiso para ver este documento.');
        }

        // 3. Servir el archivo
        if (! Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'Archivo no encontrado');
        }

        return response()->file(Storage::disk('local')->path($document->file_path));
    }
}
