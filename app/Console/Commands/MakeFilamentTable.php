<?php

// app/Console/Commands/MakeFilamentTable.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeFilamentTable extends Command
{
    protected $signature = 'make:filament-table {resource} {name=Table}';
    protected $description = 'Crea un archivo Table dentro del recurso Filament';

    public function handle()
    {
        $resource = $this->argument('resource');
        $name = $this->argument('name');

           // Preguntar por el panel
    $panel = $this->choice(
        '¿Para qué panel quieres generar el schema?',
        ['Admin', 'App'], // Puedes cargar dinámicamente desde config/filament.php si lo prefieres
        0
    );


        $path = base_path("app/Filament/{$panel}/Resources/{$resource}/Tables");
        $file = "{$path}/{$resource}{$name}.php";

        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        if (File::exists($file)) {
            $this->error("El archivo ya existe: {$file}");
            return;
        }

        File::put($file, $this->getStub($resource, $name));
        $this->info("Table creado: {$file}");
    }

    protected function getStub($resource, $name): string
    {
        return <<<PHP
<?php

namespace App\Filament\Resources\\{$resource}\\Tables;

use Filament\\Tables\\Table;
use Filament\\Tables\\Columns\\TextColumn;

class {$resource}{$name}
{
    public static function configure(Table \$table): Table
    {
        return \$table->columns([
            TextColumn::make('name')->searchable()->sortable(),
        ]);
    }
}
PHP;
    }
}
