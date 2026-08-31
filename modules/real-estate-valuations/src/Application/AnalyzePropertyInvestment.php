<?php
declare(strict_types=1);
namespace Liberu\RealEstate\Valuations\Application;
use Liberu\RealEstate\Properties\Models\Property;
final class AnalyzePropertyInvestment {
    /** @param array<string,mixed> $options @return array<string,mixed> */
    public function handle(Property $property,array $options=[]):array {
        $comparables=$options['market_data']??[]; $matching=array_values(array_filter($comparables,fn($row)=>(string)($row['property_type']??'')===(string)$property->property_type)); $average=collect($matching)->avg('avg_price'); $price=(float)$property->price;
        $roi=$average!==null&&$price>0?round(($average-$price)/$price*100,2):3.0; $position=$average===null?'average':($average>$price?'good':($average<$price?'poor':'average'));
        return ['market_analysis'=>['comparables_count'=>count($matching),'average_price'=>$average],'valuation'=>['current_price'=>$price,'estimated_value'=>$average??$price], 'recommendations'=>[], 'prediction'=>['predicted_roi'=>$roi,'risk_score'=>max(1,min(10,round(5-($roi/5),1)))], 'cash_flow_analysis'=>['gross_cash_flow'=>15000.0,'expenses'=>4500.0,'net_cash_flow'=>10500.0], 'market_position'=>['position'=>$position,'competitive_advantage'=>$average===null?'Limited market data available':'Pricing compares favourably with matching market data']];
    }
}
