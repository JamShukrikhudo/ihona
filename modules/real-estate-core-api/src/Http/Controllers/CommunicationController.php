<?php
declare(strict_types=1);
namespace Liberu\RealEstate\CoreApi\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Liberu\RealEstate\Core\Application\RecordCommunication;
use Liberu\RealEstate\Core\Models\Communication;
final class CommunicationController {
    public function store(Request $request, RecordCommunication $record): JsonResponse { $user = $request->user(); abort_unless($user?->current_team_id !== null, 403); $data = $request->validate(['related_type'=>['nullable','string'], 'related_id'=>['nullable','string'], 'channel'=>['required','string'], 'direction'=>['nullable','string'], 'subject'=>['nullable','string'], 'body'=>['nullable','string'], 'occurred_at'=>['required','date']]); return response()->json(['data'=>$record->handle($user->current_team_id, $user->getAuthIdentifier(), $data)], 201); }
    public function update(Request $request, Communication $communication): JsonResponse { abort_unless((string) $request->user()?->current_team_id === (string) $communication->team_id, 404); $communication->update($request->validate(['status'=>['sometimes','string'], 'subject'=>['sometimes','string'], 'body'=>['sometimes','string']])); return response()->json(['data'=>$communication->refresh()->toArray()]); }
    public function destroy(Request $request, Communication $communication): \Illuminate\Http\Response { abort_unless((string) $request->user()?->current_team_id === (string) $communication->team_id, 404); $communication->delete(); return response()->noContent(); }
}
