<?php

// app/Console/Commands/MakeFilamentSchema.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeFilamentSchema extends Command
{
    protected $signature = 'make:filament-schema {resource} {name=Form}';
    protected $description = 'Crea un archivo Schema dentro del recurso Filament';

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

        $path = base_path("app/Filament/{$panel}/Resources/{$resource}/Schemas");
    $file = "{$path}/{$resource}{$name}.php";

        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        if (File::exists($file)) {
            $this->error("El archivo ya existe: {$file}");
            return;
        }

        File::put($file, $this->getStub($resource, $name));
        $this->info("Schema creado: {$file}");
    }

    protected function getStub($resource, $name): string
    {
        return <<<PHP
<?php

namespace App\Filament\Resources\\{$resource}\\Schemas;

use Filament\\Schemas\\Schema;
use Filament\\Forms\\Components\\TextInput;

class {$resource}{$name}
{
    public static function configure(Schema \$schema): Schema
    {
        return \$schema->components([
            TextInput::make('name')->required(),
        ]);
    }
}
PHP;
    }
}

