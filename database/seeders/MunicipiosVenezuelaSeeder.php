<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MunicipiosVenezuelaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        // Obtener todos los estados con sus IDs
        $estados = DB::table('estados')->get()->keyBy('nombre');
        
        $municipios = [
            // Amazonas
            'Amazonas' => [
                'Alto Orinoco',
                'Atabapo',
                'Atures',
                'Autana',
                'Manapiare',
                'Maroa',
                'Río Negro'
            ],

            // Anzoátegui
            'Anzoátegui' => [
                'Anaco',
                'Aragua',
                'Bolívar',
                'Bruzual',
                'Cajigal',
                'Carvajal',
                'Diego Bautista Urbaneja',
                'Freites',
                'Guanipa',
                'Guanta',
                'Independencia',
                'Libertad',
                'McGregor',
                'Miranda',
                'Monagas',
                'Peñalver',
                'Píritu',
                'San Juan de Capistrano',
                'Santa Ana',
                'Simón Rodríguez',
                'Sotillo'
            ],

            // Apure
            'Apure' => [
                'Achaguas',
                'Biruaca',
                'Muñoz',
                'Páez',
                'Pedro Camejo',
                'Rómulo Gallegos',
                'San Fernando'
            ],

            // Aragua
            'Aragua' => [
                'Bolívar',
                'Camatagua',
                'Francisco Linares Alcántara',
                'Girardot',
                'José Ángel Lamas',
                'José Félix Ribas',
                'José Rafael Revenga',
                'Libertador',
                'Mario Briceño Iragorry',
                'Ocumare de la Costa de Oro',
                'San Casimiro',
                'San Sebastián',
                'Santiago Mariño',
                'Santos Michelena',
                'Sucre',
                'Tovar',
                'Urdaneta',
                'Zamora'
            ],

            // Barinas
            'Barinas' => [
                'Alberto Arvelo Torrealba',
                'Andrés Eloy Blanco',
                'Antonio José de Sucre',
                'Arismendi',
                'Barinas',
                'Bolívar',
                'Cruz Paredes',
                'Ezequiel Zamora',
                'Obispos',
                'Pedraza',
                'Rojas',
                'Sosa'
            ],

            // Bolívar
            'Bolívar' => [
                'Caroní',
                'Cedeño',
                'El Callao',
                'Gran Sabana',
                'Heres',
                'Piar',
                'Angostura (Raúl Leoni)',
                'Roscio',
                'Sifontes',
                'Sucre',
                'Padre Pedro Chien'
            ],

            // Carabobo
            'Carabobo' => [
                'Bejuma',
                'Carlos Arvelo',
                'Diego Ibarra',
                'Guacara',
                'Juan José Mora',
                'Libertador',
                'Los Guayos',
                'Miranda',
                'Montalbán',
                'Naguanagua',
                'Puerto Cabello',
                'San Diego',
                'San Joaquín',
                'Valencia'
            ],

            // Cojedes
            'Cojedes' => [
                'Anzoátegui',
                'Pao de San Juan Bautista',
                'Tinaquillo',
                'Girardot',
                'Lima Blanco',
                'Ricaurte',
                'Rómulo Gallegos',
                'Ezequiel Zamora',
                'Tinaco'
            ],

            // Delta Amacuro
            'Delta Amacuro' => [
                'Antonio Díaz',
                'Casacoima',
                'Pedernales',
                'Tucupita'
            ],

            // Falcón
            'Falcón' => [
                'Acosta',
                'Bolívar',
                'Buchivacoa',
                'Cacique Manaure',
                'Carirubana',
                'Colina',
                'Dabajuro',
                'Democracia',
                'Falcón',
                'Federación',
                'Jacura',
                'Los Taques',
                'Mauroa',
                'Miranda',
                'Monseñor Iturriza',
                'Palmasola',
                'Petit',
                'Píritu',
                'San Francisco',
                'Silva',
                'Sucre',
                'Tocópero',
                'Unión',
                'Urumaco',
                'Zamora'
            ],

            // Guárico
            'Guárico' => [
                'Camaguán',
                'Chaguaramas',
                'El Socorro',
                'Francisco de Miranda',
                'José Félix Ribas',
                'José Tadeo Monagas',
                'Juan Germán Roscio',
                'Julián Mellado',
                'Las Mercedes',
                'Leonardo Infante',
                'Pedro Zaraza',
                'Ortiz',
                'San Gerónimo de Guayabal',
                'San José de Guaribe',
                'Santa María de Ipire'
            ],

            // Lara
            'Lara' => [
                'Andrés Eloy Blanco',
                'Crespo',
                'Iribarren',
                'Jiménez',
                'Morán',
                'Palavecino',
                'Simón Planas',
                'Torres',
                'Urdaneta'
            ],

            // Mérida
            'Mérida' => [
                'Alberto Adriani',
                'Andrés Bello',
                'Antonio Pinto Salinas',
                'Aricagua',
                'Arzobispo Chacón',
                'Campo Elías',
                'Caracciolo Parra Olmedo',
                'Cardenal Quintero',
                'Guaraque',
                'Julio César Salas',
                'Justo Briceño',
                'Libertador',
                'Miranda',
                'Obispo Ramos de Lora',
                'Padre Noguera',
                'Pueblo Llano',
                'Rangel',
                'Rivas Dávila',
                'Santos Marquina',
                'Sucre',
                'Tovar',
                'Tulio Febres Cordero',
                'Zea'
            ],

            // Miranda
            'Miranda' => [
                'Acevedo',
                'Andrés Bello',
                'Baruta',
                'Brión',
                'Buroz',
                'Carrizal',
                'Chacao',
                'Cristóbal Rojas',
                'El Hatillo',
                'Guaicaipuro',
                'Independencia',
                'Lander',
                'Los Salias',
                'Páez',
                'Paz Castillo',
                'Pedro Gual',
                'Plaza',
                'Simón Bolívar',
                'Sucre',
                'Urdaneta',
                'Zamora'
            ],

            // Monagas
            'Monagas' => [
                'Acosta',
                'Aguasay',
                'Bolívar',
                'Caripe',
                'Cedeño',
                'Ezequiel Zamora',
                'Libertador',
                'Maturín',
                'Piar',
                'Punceres',
                'Santa Bárbara',
                'Sotillo',
                'Uracoa'
            ],

            // Nueva Esparta
            'Nueva Esparta' => [
                'Antolín del Campo',
                'Arismendi',
                'Díaz',
                'García',
                'Gómez',
                'Maneiro',
                'Marcano',
                'Mariño',
                'Península de Macanao',
                'Tubores',
                'Villalba'
            ],

            // Portuguesa
            'Portuguesa' => [
                'Araure',
                'Agua Blanca',
                'Esteller',
                'Guanare',
                'Guanarito',
                'Monseñor José Vicente de Unda',
                'Ospino',
                'Páez',
                'Papelón',
                'San Genaro de Boconoíto',
                'San Rafael de Onoto',
                'Santa Rosalía',
                'Sucre',
                'Turén'
            ],

            // Sucre
            'Sucre' => [
                'Andrés Eloy Blanco',
                'Andrés Mata',
                'Arismendi',
                'Benítez',
                'Bermúdez',
                'Bolívar',
                'Cajigal',
                'Cruz Salmerón Acosta',
                'Libertador',
                'Mariño',
                'Mejía',
                'Montes',
                'Ribero',
                'Sucre',
                'Valdez'
            ],

            // Táchira
            'Táchira' => [
                'Andrés Bello',
                'Antonio Rómulo Costa',
                'Ayacucho',
                'Bolívar',
                'Cárdenas',
                'Córdoba',
                'Fernández Feo',
                'Francisco de Miranda',
                'García de Hevia',
                'Guásimos',
                'Independencia',
                'Jáuregui',
                'José María Vargas',
                'Junín',
                'Libertad',
                'Libertador',
                'Lobatera',
                'Michelena',
                'Panamericano',
                'Pedro María Ureña',
                'Rafael Urdaneta',
                'Samuel Darío Maldonado',
                'San Cristóbal',
                'San Judas Tadeo',
                'Seboruco',
                'Simón Rodríguez',
                'Sucre',
                'Torbes',
                'Uribante'
            ],

            // Trujillo
            'Trujillo' => [
                'Andrés Bello',
                'Boconó',
                'Bolívar',
                'Candelaria',
                'Carache',
                'Escuque',
                'José Felipe Márquez Cañizalez',
                'Juan Vicente Campo Elías',
                'La Ceiba',
                'Miranda',
                'Monte Carmelo',
                'Motatán',
                'Pampán',
                'Pampanito',
                'Rafael Rangel',
                'San Rafael de Carvajal',
                'Sucre',
                'Trujillo',
                'Urdaneta',
                'Valera'
            ],

            // La Guaira
            'La Guaira' => [
                'Vargas'
            ],

            // Yaracuy
            'Yaracuy' => [
                'Arístides Bastidas',
                'Bolívar',
                'Bruzual',
                'Cocorote',
                'Independencia',
                'José Antonio Páez',
                'La Trinidad',
                'Manuel Monge',
                'Nirgua',
                'Peña',
                'San Felipe',
                'Sucre',
                'Urachiche',
                'Veroes'
            ],

            // Zulia
            'Zulia' => [
                'Almirante Padilla',
                'Baralt',
                'Cabimas',
                'Catatumbo',
                'Colón',
                'Francisco Javier Pulgar',
                'Jesús Enrique Lossada',
                'Jesús María Semprún',
                'La Cañada de Urdaneta',
                'Lagunillas',
                'Machiques de Perijá',
                'Mara',
                'Maracaibo',
                'Miranda',
                'Guajira',
                'Rosario de Perijá',
                'San Francisco',
                'Santa Rita',
                'Simón Bolívar',
                'Sucre',
                'Valmore Rodríguez'
            ],

            // Distrito Capital
            'Distrito Capital' => [
                'Libertador'
            ]
        ];

        $data = [];
        foreach ($municipios as $estadoNombre => $municipiosEstado) {
            if (isset($estados[$estadoNombre])) {
                $estadoId = $estados[$estadoNombre]->id;
                
                foreach ($municipiosEstado as $municipio) {
                    $data[] = [
                        'nombre' => $municipio,
                        'estado_id' => $estadoId,
                        'created_at' => $now,
                        'updated_at' => $now
                    ];
                }
            }
        }

        // Insertar en lotes para mejor performance
        $chunks = array_chunk($data, 100);
        foreach ($chunks as $chunk) {
            DB::table('municipios')->insert($chunk);
        }
    }
}