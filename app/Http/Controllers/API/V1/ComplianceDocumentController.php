<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\ComplianceDocument;
use App\Models\ComplianceItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ComplianceDocumentController extends Controller
{
    public function index(Request $request, int $complianceItem): JsonResponse
    {
        $item = $this->item($request, $complianceItem);

        return response()->json($item->complianceDocuments()
            ->latest('upload_date')
            ->paginate(min(max($request->integer('per_page', 20), 1), 100)));
    }

    public function store(Request $request, int $complianceItem): JsonResponse
    {
        $item = $this->item($request, $complianceItem);
        $attributes = $request->validate([
            'file' => ['required', 'file', 'max:20480', 'mimes:pdf,jpg,jpeg,png,doc,docx'],
            'document_type' => ['required', Rule::in(['certificate', 'report', 'identity', 'licence', 'insurance', 'correspondence', 'other'])],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'expiry_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);
        $file = $request->file('file');
        $path = $file->store("compliance/{$item->team_id}/{$item->id}", 'local');

        $document = ComplianceDocument::create([
            ...collect($attributes)->except('file')->all(),
            'team_id' => $item->team_id,
            'compliance_item_id' => $item->id,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'uploaded_by' => $request->user()->id,
            'upload_date' => now(),
        ]);

        return response()->json(['data' => $document->fresh()], 201);
    }

    public function download(Request $request, int $complianceItem, int $document): StreamedResponse
    {
        $record = $this->document($request, $complianceItem, $document);

        abort_unless($record->file_path && Storage::disk('local')->exists($record->file_path), 404);

        return Storage::disk('local')->download($record->file_path, $record->file_name);
    }

    public function verify(Request $request, int $complianceItem, int $document): JsonResponse
    {
        $record = $this->document($request, $complianceItem, $document);
        $attributes = $request->validate([
            'verified' => ['required', 'boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        $record->update([
            'is_verified' => $attributes['verified'],
            'verified_by' => $attributes['verified'] ? $request->user()->id : null,
            'verified_date' => $attributes['verified'] ? now() : null,
            'notes' => $attributes['notes'] ?? $record->notes,
        ]);

        return response()->json(['data' => $record->fresh()]);
    }

    public function destroy(Request $request, int $complianceItem, int $document): JsonResponse
    {
        $record = $this->document($request, $complianceItem, $document);
        $path = $record->file_path;
        $record->delete();

        if ($path) {
            Storage::disk('local')->delete($path);
        }

        return response()->json(null, 204);
    }

    private function item(Request $request, int $id): ComplianceItem
    {
        $teamId = $request->user()?->current_team_id;

        if (! $teamId || ! $request->user()->allTeams()->contains('id', $teamId)) {
            throw ValidationException::withMessages([
                'team' => ['Select an organisation you belong to before using this endpoint.'],
            ]);
        }

        return ComplianceItem::where('team_id', $teamId)->findOrFail($id);
    }

    private function document(Request $request, int $itemId, int $documentId): ComplianceDocument
    {
        $item = $this->item($request, $itemId);

        return ComplianceDocument::where('team_id', $item->team_id)
            ->where('compliance_item_id', $item->id)
            ->findOrFail($documentId);
    }
}
