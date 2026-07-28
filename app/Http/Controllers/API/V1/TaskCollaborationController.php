<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\AgencyTask;
use App\Models\AgencyTaskAttachment;
use App\Models\AgencyTaskComment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskCollaborationController extends Controller
{
    public function comments(Request $request, int $task): JsonResponse
    {
        $record = $this->task($request, $task);
        return response()->json(['data' => $record->comments()->with('user:id,name')->oldest()->get()]);
    }

    public function storeComment(Request $request, int $task): JsonResponse
    {
        $record = $this->task($request, $task);
        $validated = $request->validate(['body' => ['required', 'string', 'max:10000']]);
        $comment = $record->comments()->create($validated + [
            'team_id' => $record->team_id,
            'user_id' => $request->user()->id,
        ]);
        return response()->json(['data' => $comment->fresh('user:id,name')], 201);
    }

    public function destroyComment(Request $request, int $task, int $comment): JsonResponse
    {
        $record = $this->task($request, $task);
        $entry = $record->comments()->where('team_id', $record->team_id)->findOrFail($comment);
        $team = $request->user()->currentTeam;
        abort_unless(
            $entry->user_id === $request->user()->id
                || $request->user()->ownsTeam($team)
                || $request->user()->hasTeamRole($team, 'admin'),
            403,
        );
        $entry->delete();
        return response()->json(null, 204);
    }

    public function attachments(Request $request, int $task): JsonResponse
    {
        return response()->json(['data' => $this->task($request, $task)->attachments()->oldest()->get()]);
    }

    public function storeAttachment(Request $request, int $task): JsonResponse
    {
        $record = $this->task($request, $task);
        $validated = $request->validate(['file' => ['required', 'file', 'max:20480']]);
        $file = $validated['file'];
        $path = $file->store("task-attachments/{$record->team_id}");
        $attachment = $record->attachments()->create([
            'team_id' => $record->team_id,
            'uploaded_by' => $request->user()->id,
            'name' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);
        return response()->json(['data' => $attachment], 201);
    }

    public function destroyAttachment(Request $request, int $task, int $attachment): JsonResponse
    {
        $record = $this->task($request, $task);
        $entry = $record->attachments()->where('team_id', $record->team_id)->findOrFail($attachment);
        Storage::delete($entry->path);
        $entry->delete();
        return response()->json(null, 204);
    }

    public function checklist(Request $request, int $task, int $item): JsonResponse
    {
        $record = $this->task($request, $task);
        $validated = $request->validate([
            'completed' => ['required', 'boolean'],
            'label' => ['sometimes', 'string', 'max:255'],
        ]);
        $checklist = $record->checklist ?? [];
        abort_unless(array_key_exists($item, $checklist), 404);
        $checklist[$item] = array_merge($checklist[$item], $validated);
        $record->update(['checklist' => array_values($checklist)]);
        return response()->json(['data' => $record->fresh()]);
    }

    private function task(Request $request, int $id): AgencyTask
    {
        return AgencyTask::where('team_id', $request->user()->current_team_id)->findOrFail($id);
    }
}
