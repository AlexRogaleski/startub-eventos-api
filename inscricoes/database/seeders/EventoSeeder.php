<?php

namespace Database\Seeders;

use App\Models\Evento;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Evento::create([
            'titulo' => 'Workshop de APIs com Laravel',
            'descricao' => 'APIs REST, integração e webhooks na prática.',
            'local' => 'Laboratório 3',
            'data' => '2026-12-05 19:00',
            'vagas' => 30,
            'valor' => 0,
        ]);
        
        Evento::create([
            'titulo' => 'Minicurso de Git e GitHub',
            'descricao' => 'Versionamento de código do zero ao pull request.',
            'local' => 'Auditório',
            'data' => '2026-12-12 14:00',
            'vagas' => 2,
            'valor' => 25.00,
        ]);
    }
}
