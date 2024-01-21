<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogResource\Pages;
use App\Filament\Resources\BlogResource\RelationManagers;
use App\Models\Blog;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rule;

class BlogResource extends Resource
{
    protected static ?string $model = Blog::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $modelLabel = 'Post'; //override navigation label

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Create Post')
                    ->description('create a post over here')
                    ->aside()
                    // ->collapsible()//not work with aside
                    ->schema([
                        TextInput::make('title')->required(), //numeric method is for number input
                        TextInput::make('slug')->required()->unique('blogs', 'slug', null, true),
                        ColorPicker::make('color')->required(),
                        TagsInput::make('tags')->required(),
                        Select::make('category_id')
                            ->label('Category')
                            ->options(Category::all()->pluck('name', 'id'))
                            ->rules(Rule::exists('categories', 'id')),
                        // ->searchable(),
                        Checkbox::make('published')->required()
                    ])->columns(2),
                Section::make('Image')
                    ->description('Upload image here')
                    ->aside()
                    ->schema([
                        FileUpload::make('thumbnail')->disk('public')->directory('thumbmanails'),
                    ]),
                Section::make('Blog Body')
                    ->description('Upload image here')
                    ->aside()
                    ->schema([
                        MarkdownEditor::make('content')->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('thumbnail'),
                TextColumn::make('title'),
                TextColumn::make('category.name'), //using relationship name
                TextColumn::make('slug'),
                ColorColumn::make('color'),
                TextColumn::make('tags'),
                CheckboxColumn::make('published')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogs::route('/'),
            'create' => Pages\CreateBlog::route('/create'),
            'edit' => Pages\EditBlog::route('/{record}/edit'),
        ];
    }
}
