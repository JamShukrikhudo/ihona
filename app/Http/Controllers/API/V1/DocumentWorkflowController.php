<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentVersion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentWorkflowController extends Controller
{
    public function versions(Request $request, int $document): JsonResponse
    {
        $record = $this->document($request, $document);

        return response()->json([
            'data' => $record->versions()->with('uploader:id,name')->latest('version')->get(),
        ]);
    }

    public function storeVersion(Request $request, int $document): JsonResponse
    {
        $record = $this->document($request, $document);
        $validated = $request->validate([
            'file' => ['required', 'file', 'max:20480'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
        $file = $validated['file'];
        $contents = $file->getContent();
        $version = DB::transaction(function () use ($request, $record, $file, $contents, $validated) {
            $number = (int) $record->versions()->lockForUpdate()->max('version') + 1;
            $path = $file->store("agency-documents/{$record->team_id}/{$record->id}", 'local');

            return $record->versions()->create([
                'team_id' => $record->team_id,
                'uploaded_by' => $request->user()->id,
                'version' => $number,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
                'size' => $file->getSize(),
                'checksum' => hash('sha256', $contents),
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        $record->update([
            'file_path' => $version->file_path,
            'file_type' => $version->mime_type,
            'size' => $version->size,
        ]);

        return response()->json(['data' => $version->load('uploader:id,name')], 201);
    }

    public function download(Request $request, int $document, int $version): StreamedResponse
    {
        $entry = $this->version($request, $document, $version);
        abort_unless(Storage::disk('local')->exists($entry->file_path), 404);

        return Storage::disk('local')->download($entry->file_path, $entry->file_name, [
            'Content-Type' => $entry->mime_type,
        ]);
    }

    public function signatures(Request $request, int $document): JsonResponse
    {
        $record = $this->document($request, $document);

        return response()->json([
            'data' => $record->digitalSignatures()->with(['user:id,name', 'version:id,version'])->latest('signed_at')->get(),
        ]);
    }

    public function sign(Request $request, int $document): JsonResponse
    {
        $record = $this->document($request, $document);
        abort_unless($record->is_signable, 422, 'This document is not signable.');
        $validated = $request->validate([
            'signature_data' => ['required', 'string', 'max:100000'],
            'document_version_id' => ['nullable', 'integer'],
        ]);
        $version = isset($validated['document_version_id'])
            ? $record->versions()->findOrFail($validated['document_version_id'])
            : $record->versions()->latest('version')->first();

        $signature = $record->digitalSignatures()->create([
            'team_id' => $record->team_id,
            'user_id' => $request->user()->id,
            'document_version_id' => $version?->id,
            'signature_data' => $validated['signature_data'],
            'signed_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json(['data' => $signature->load(['user:id,name', 'version:id,version'])], 201);
    }

    private function document(Request $request, int $id): Document
    {
        return Document::query()->where('team_id', $request->user()->current_team_id)->findOrFail($id);
    }

    private function version(Request $request, int $document, int $version): DocumentVersion
    {
        return $this->document($request, $document)->versions()->findOrFail($version);
    }
}
