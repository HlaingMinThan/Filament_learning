<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogResource\Pages;
use App\Filament\Resources\BlogResource\RelationManagers;
use App\Filament\Resources\BlogResource\RelationManagers\AuthorsRelationManager;
use App\Filament\Resources\BlogResource\RelationManagers\CommentsRelationManager;
use App\Forms\Components\HelloWorld;
use App\Forms\Components\YoutubePreview;
use App\Models\Blog;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
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
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class BlogResource extends Resource
{
    protected static ?string $model = Blog::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Blogs management';
    protected static ?int $navigationSort = 2;
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
                        YoutubePreview::make('youtube_url')->label('Paste Youtube link')->required(),
                        TextInput::make('title')->required()
                            ->live(debounce: 300)
                            //params order doesn't matter
                            ->afterStateUpdated(function (Blog $blog, $operation, $set, $state, $get) {
                                if ($operation === 'edit') {
                                    return null;
                                }
                                $set('slug', Str::slug($state));
                                // dd($get('content')); //need to use live on content input to get latest state
                            }), //numeric method is for number input
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
                    ->aside()
                    ->schema([
                        FileUpload::make('thumbnail')->disk('public')->directory('thumbmanails'),
                    ]),
                Section::make('Blog Body')
                    ->description('Upload image here')
                    ->aside()
                    ->schema([
                        MarkdownEditor::make('content')
                            ->live()
                            ->required(),
                    ]),
                Section::make('Authors')
                    ->description('select authors')
                    ->aside()
                    ->schema([
                        Select::make('authors')->relationship('authors', 'name')->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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
                //option - 1 add published filter
                // Filter::make('Published')->query(function ($query) {
                //     $query->where('published', true);
                // }),
                //optin 2 - yes or no
                TernaryFilter::make('published'),
                //Select Filter
                SelectFilter::make('category_id')
                    ->label('Category')
                    // ->options(Category::all()->pluck('name', 'id'))
                    //rs doesn't load all category by default, we need to add preload
                    ->relationship('category', 'name')
                    ->preload()
                    ->searchable()
                    ->multiple()
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

    public static function getRelations(): array
    {
        return [
            AuthorsRelationManager::class,
            CommentsRelationManager::class
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
