<?php
declare(strict_types=1);
namespace Liberu\RealEstate\PortalsReportingApi\Http\Controllers;
use Illuminate\Http\Request; use Illuminate\Http\JsonResponse; use Liberu\RealEstate\PortalsReporting\Models\DashboardLayout;
final class DashboardLayoutController { public function store(Request $r):JsonResponse{$u=$r->user();abort_unless($u?->current_team_id!==null,403);$d=$r->validate(['name'=>'required|string|max:255','widgets'=>'nullable|array']);return response()->json(['data'=>DashboardLayout::create([...$d,'team_id'=>$u->current_team_id,'user_id'=>$u->getAuthIdentifier()])],201);} }
