<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaleResource\Pages;
use App\Models\Product;
use App\Models\Sale;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput\CreateOptionForm;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected $fillable = ['nafaka','kiwango_cha_unit','kilo_kwa_unit','bei_ya_tanesco','bei_ya_mashineni','kiwango_cha_kapi'];


    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('product_id')
                ->label('Chagua Nafaka')
                ->relationship('product', 'nafaka')
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set) {
                    $product = Product::find($state);
                    if ($product) {
                        $set('nafaka', $product->nafaka);
                    }
                }),
    
            Hidden::make('nafaka')->dehydrated(true),
    
            Toggle::make('anataka_kulipa')
            ->label('Je, Mteja Anataka Kapi? (NDIO /Hapana)')
            ->default(false) // YES by default
            ->onColor('success')
            ->offColor('danger')
            ->onIcon('heroicon-m-check-circle')
            ->offIcon('heroicon-m-x-circle')
            ->inline(false)
            ->reactive()
            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                $product = Product::find($get('product_id'));
                $kapi_zilizo_patikana = $get('kapi_zilizo_patikana') ?? 0.00;
                $unit_zilizotumika = $get('unit_zilizotumika') ?? 0.00;
                $malipo_ya_mteja = $get('kilo_za_mteja') * ($product->bei_ya_mashineni ?? 0);
        
                if ($state === true) {
                    // Hataki kulipa
                    $set('kapi_alizoacha_mteja', $kapi_zilizo_patikana);
                    $set('malipo_ya_mteja', 0.00);
                    $set('faida', 0.00);
        
                    Notification::make()
                        ->title('✅ Mteja halipi, kapi zimebaki dukani!')
                        ->danger()
                        ->send();
                } else {
                    // Anataka kulipa
                    $set('kapi_alizoacha_mteja', 0.00);
                    $set('malipo_ya_mteja', round($malipo_ya_mteja, 0));
        
                    $gharama_ya_uzalishaji = $unit_zilizotumika * ($product->bei_ya_tanesco ?? 0);
                    $faida = $malipo_ya_mteja - $gharama_ya_uzalishaji;
                    $set('faida', round($faida, 0));

                    Notification::make()
                        ->title('⚠️ Mteja analipa, kapi anaondokanazo!')
                        ->danger()
                        ->send();
                }
            }),
        
            TextInput::make('kapi_alizoacha_mteja')
            ->label('Kapi Alizoacha Mteja')
            ->numeric()
            ->default(0.00) // ← Prevent null errors
            ->required()
            ->disabled()
            ->dehydrated(),
        
        TextInput::make('malipo_ya_mteja')
            ->label('Malipo ya Mteja (TSH)')
            ->numeric()
            ->default(0.00) // ← Prevent null errors
            ->required()
            ->disabled()
            ->dehydrated(),
        
   
            Section::make()
                ->schema([
                    TextInput::make('kilo_za_mteja')
                        ->label('CUSTOMER ORDER: (Kg au Debe)')
                        ->numeric()
                        ->reactive()
                        ->required()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            $product = Product::find($get('product_id'));
    
                            if ($product && $state > 0) {
                                $kilo_za_mteja = $state;
    
                                $kilo_kwa_unit = $product->kilo_kwa_unit;
                                $kiwango_cha_kapi = $product->kiwango_cha_kapi;
                                $bei_ya_tanesco = $product->bei_ya_tanesco;
                                $bei_ya_mashineni = $product->bei_ya_mashineni;
    
                                $unit_zilizotumika = $kilo_za_mteja / $kilo_kwa_unit;
                                $malipo_ya_mteja = $kilo_za_mteja * $bei_ya_mashineni;
                                $kapi_zilizo_patikana = $unit_zilizotumika * $kiwango_cha_kapi;
                                $gharama_ya_uzalishaji = $unit_zilizotumika * $bei_ya_tanesco;
                                $faida = $malipo_ya_mteja - $gharama_ya_uzalishaji;
    
                                $set('unit_zilizotumika', round($unit_zilizotumika, 2));
                                $set('malipo_ya_mteja', round($malipo_ya_mteja, 0));
                                $set('kapi_zilizo_patikana', round($kapi_zilizo_patikana, 2));
                                $set('faida', round($faida, 0));
                            }
                        }),
    
                    TextInput::make('unit_zilizotumika')
                        ->label('Unit Zilizotumika')
                        ->numeric()
                        ->required()
                        ->disabled()
                        ->dehydrated(),
    
                    TextInput::make('kapi_zilizo_patikana')
                        ->label('Kapi Zilizo Patikana')
                        ->numeric()
                        ->required()
                        ->disabled()
                        ->dehydrated(),
    
                    TextInput::make('kapi_alizoacha_mteja')
                        ->label('Kapi Alizoacha Mteja')
                        ->numeric()
                        ->required()
                        ->disabled()
                        ->dehydrated(),
    
                    TextInput::make('malipo_ya_mteja')
                        ->label('Malipo ya Mteja (TSH)')
                        ->numeric()
                        ->required()
                        ->disabled()
                        ->dehydrated(),
    
                    TextInput::make('faida')
                        ->label('Faida')
                        ->numeric()
                        ->required()
                        ->disabled()
                        ->dehydrated(),
    
                    Placeholder::make('Bili_ya_mteja')
                        ->content(function (callable $get) {
                            $unit = number_format($get('unit_zilizotumika') ?? 0, 2);
                            $kapi = number_format($get('kapi_zilizo_patikana') ?? 0, 2);
                            $malipo = number_format($get('malipo_ya_mteja') ?? 0, 0);
                            $faida = number_format($get('faida') ?? 0, 0);
    
                            return new HtmlString(<<<HTML
                                <div style="font-family: monospace; padding: 1rem; background: #f9f9f9;">
                                    <p>✅ <strong>Unit:</strong> {$unit} Unit</p>
                                    <p>📦 <strong>Kapi:</strong> {$kapi} KG</p>
                                    <p>💰 <strong>Analipa:</strong> {$malipo} TSH</p>
                                    <p>📈 <strong>Faida:</strong> {$faida} TSH</p>
                                </div>
                            HTML);
                        })
                        ->visible(fn (callable $get) => filled($get('kilo_za_mteja')))
                        ->columnSpan('full'),
    
                    Placeholder::make('pos_invoice_preview')
                        ->label('🧾 eMashineni POS Invoice')
                        ->content(function (callable $get) {
                            if (!filled($get('product_id'))) {
                                return new HtmlString('<em style="color: gray;">Tafadhali chagua nafaka ili kuona Bili preview...</em>');
                            }
    
                            $electricUnit = number_format($get('kiwango_cha_unit') ?? 0, 2);
                            $kiloPerUnit = number_format($get('kilo_kwa_unit') ?? 0, 2);
                            $kapiPerUnit = number_format($get('kiwango_cha_kapi') ?? 0, 2);
                            $tanescoCost = number_format($get('bei_ya_tanesco') ?? 0, 0);
                            $mashineniCost = number_format($get('bei_ya_mashineni') ?? 0, 0);
    
                            return new HtmlString(<<<HTML
                            <div style="font-family: monospace; border: 1px dashed #aaa; padding: 1rem; background: #FFFFFF;">
                                <h3 style="text-align: center; margin-bottom: 1rem;">🧾 POS Invoice Preview</h3>
                                <p>🔌 <strong>Electric Unit:</strong> {$electricUnit}</p>
                                <p>⚖️ <strong>Kilo per Unit:</strong> {$kiloPerUnit}</p>
                                <p>📦 <strong>Kapi per Unit:</strong> {$kapiPerUnit}</p>
                                <p>💸 <strong>Gharama ya Tanesco:</strong> {$tanescoCost} TSH / Unit</p>
                                <p>🛠️ <strong>Gharama ya Mashineni:</strong> {$mashineniCost} TSH / Kilo</p>
                              
                            </div>
                        HTML);
                        })
                        ->columnSpan('full'),
                ])
                ->visible(fn (callable $get) => filled($get('product_id'))),
        ]);
    }
    
    


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('nafaka')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kilo_za_mteja')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->summarize(Tables\Columns\Summarizers\Sum::make()
                    ->label('Kapi (KG)')),

                Tables\Columns\TextColumn::make('unit_zilizotumika')
                    ->sortable()
                    ->summarize(Tables\Columns\Summarizers\Sum::make()
                    ->label('Electric Unit')
                    -> numeric()),
                Tables\Columns\TextColumn::make('kapi_zilizo_patikana')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->summarize(Tables\Columns\Summarizers\Sum::make()
                    ->label('Kapi (KG)')),
                
                Tables\Columns\TextColumn::make('kapi_alizoacha_mteja')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->summarize(Tables\Columns\Summarizers\Sum::make()
                    ->label('Kapi (KG)')),
                
                Tables\Columns\TextColumn::make('malipo_ya_mteja')
                    ->sortable()
                    ->summarize(Tables\Columns\Summarizers\Sum::make()
                    ->label('Jumla Mapato')
                    -> money('TZS')), 
                
                Tables\Columns\TextColumn::make('faida')
                    ->sortable()
                    ->summarize(Tables\Columns\Summarizers\Sum::make()
                    ->label('Jumla Faida')
                    -> money('TZS'))
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
            'index' => Pages\ListSales::route('/'),
            'pos' => Pages\CreateSale::route('/create'),
            'edit' => Pages\EditSale::route('/{record}/edit'),
        ];
    }
}
