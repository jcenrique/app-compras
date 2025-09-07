<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupMarket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:setup-market';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea las semillas para poblar la base de datos del mercado';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🌱 Iniciando la siembra del mercado...');

        // Aquí puedes agregar la lógica para sembrar datos en la base de datos
        // Por ejemplo, puedes usar modelos Eloquent para crear registros
        // puedo utilizar seeders existentes
        $this->call('db:seed', ['--class' => 'CategoriesSeeder']);

        $this->info('✅ Siembra completada con éxito.');
        return Command::SUCCESS;
    }
}
