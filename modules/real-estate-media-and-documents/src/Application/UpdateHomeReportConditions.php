<?php
declare(strict_types=1);
namespace Liberu\RealEstate\MediaAndDocuments\Application;
use Liberu\RealEstate\MediaAndDocuments\Models\HomeReport;
final class UpdateHomeReportConditions { public function handle(HomeReport $report,int|string $teamId,array $conditions):HomeReport { abort_unless((string)$report->team_id===(string)$teamId,404);foreach($conditions as $key=>$value){if(!is_int($value)||$value<1||$value>4)throw new \InvalidArgumentException('Condition ratings must be between 1 and 4.');}$report->update(['condition_categories'=>[...($report->condition_categories??[]),...$conditions]]);return $report->refresh();} }
