<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Illuminate\Validation\Rule;

class BlogsRelationManager extends RelationManager
{
    protected static string $relationship = 'blogs';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Create Post')
                    ->description('create a post over here')
                    // ->collapsible()//not work with aside
                    ->schema([
                        TextInput::make('title')->required(), //numeric method is for number input
                        TextInput::make('slug')->required()->unique('blogs', 'slug', null, true),
                        ColorPicker::make('color')->required(),
                        TagsInput::make('tags')->required(),
                        Select::make('category_id')
                            ->label('Category')
                            // ->options(Category::all()->pluck('name', 'id'))
                            ->relationship('category', 'name')
                            ->rules(Rule::exists('categories', 'id')),
                        // ->searchable(),
                        Checkbox::make('published')->required()
                    ])->columns(2),
                Section::make('Image')
                    ->description('Upload image here')
                    ->schema([
                        FileUpload::make('thumbnail')->disk('public')->directory('thumbmanails'),
                    ]),
                Section::make('Blog Body')
                    ->description('Upload image here')
                    ->schema([
                        MarkdownEditor::make('content')->required(),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('id')->sortable()->searchable()->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('thumbnail'),
                TextColumn::make('title')->sortable()->searchable()->toggleable(),
                TextColumn::make('category.name')->sortable()->searchable()->toggleable(), //using relationship name
                TextColumn::make('slug')->sortable()->searchable()->toggleable(),
                ColorColumn::make('color')->toggleable(),
                TextColumn::make('tags')->sortable()->searchable()->toggleable(),
                CheckboxColumn::make('published')->sortable()->searchable()->toggleable()
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
