<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\CandidateDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function show(Request $request, $slug)
    {
        $user = $request->user();

        abort_unless($user, 403, 'Acceso restringido: Debes iniciar sesión.');

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

        $disk = $this->resolveDisk($document->file_path);

        return Response::stream(function () use ($disk, $document): void {
            $stream = Storage::disk($disk)->readStream($document->file_path);

            if ($stream === false) {
                abort(404, 'Archivo no encontrado');
            }

            fpassthru($stream);

            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, [
            'Content-Type' => $this->contentTypeFor($document->file_path),
            'Content-Disposition' => 'inline; filename="'.basename($document->file_path).'"',
        ]);
    }

    private function resolveDisk(string $path): string
    {
        if (Storage::disk('s3')->exists($path)) {
            return 's3';
        }

        if (Storage::disk('local')->exists($path)) {
            return 'local';
        }

        abort(404, 'Archivo no encontrado');
    }

    private function contentTypeFor(string $path): string
    {
        return match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'pdf' => 'application/pdf',
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'txt' => 'text/plain',
            'csv' => 'text/csv',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            default => 'application/octet-stream',
        };
    }
}
