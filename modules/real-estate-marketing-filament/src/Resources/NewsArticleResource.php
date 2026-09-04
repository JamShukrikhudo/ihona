<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MarketingFilament\Resources;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Liberu\RealEstate\Marketing\Models\NewsArticle;

final class NewsArticleResource extends Resource
{
    protected static ?string $model = NewsArticle::class;

    public static function getModelLabel(): string
    {
        return __('filament.resources.news_article.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.resources.news_article.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->label(__('filament.news_article.fields.title'))->required(),
            TextInput::make('slug')->label(__('filament.news_article.fields.slug'))->required(),
            Textarea::make('content')->label(__('filament.news_article.fields.content'))->required(),
            DateTimePicker::make('published_at')->label(__('filament.news_article.fields.published_at')),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([TextColumn::make('title')->label(__('filament.news_article.fields.title'))->searchable(), TextColumn::make('published_at')->label(__('filament.news_article.fields.published_at'))->dateTime(), TextColumn::make('is_featured')->label(__('filament.news_article.fields.is_featured'))->boolean()]);
    }

    public static function getEloquentQuery(): Builder
    {
        $teamId = auth()->user()?->current_team_id;

        return parent::getEloquentQuery()->visibleToTeam($teamId)->whereNotNull('published_at');
    }
}
