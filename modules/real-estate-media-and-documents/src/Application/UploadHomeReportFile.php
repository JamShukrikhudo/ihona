<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MediaAndDocuments\Application;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Liberu\RealEstate\MediaAndDocuments\Models\HomeReport;

final class UploadHomeReportFile
{
    public function handle(HomeReport $report, int|string $teamId, UploadedFile $file): HomeReport
    {
        abort_unless((string) $report->team_id === (string) $teamId, 404);
        if ($file->getMimeType() !== 'application/pdf') {
            throw new \InvalidArgumentException('A PDF file is required.');
        }if ($report->file_path) {
            Storage::disk('public')->delete($report->file_path);
        }$path = $file->store('home-reports/'.$teamId, 'public');
        $report->update(['file_path' => $path, 'file_url' => Storage::disk('public')->url($path)]);

        return $report->refresh();
    }
}
