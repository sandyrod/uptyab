<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ParroquiasCompletasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        // Obtener todos los municipios con sus IDs
        $municipios = DB::table('municipios')
            ->join('estados', 'municipios.estado_id', '=', 'estados.id')
            ->select('municipios.id', 'municipios.nombre as municipio', 'estados.nombre as estado')
            ->get()
            ->groupBy('estado');

        $parroquiasCompletas = [
            // Amazonas
            'Amazonas' => [
                'Alto Orinoco' => ['Alto Orinoco', 'Huachamacare', 'Marawaka', 'Mavaka', 'Sierra Parima'],
                'Atabapo' => ['Atabapo', 'Ucata', 'Yapacana', 'Caname'],
                'Atures' => ['Fernando Girón Tovar', 'Luis Alberto Gómez', 'Parhueña', 'Platanillal'],
                'Autana' => ['Samariapo', 'Sipapo', 'Munduapo', 'Guayapo'],
                'Manapiare' => ['Alto Ventuari', 'Medio Ventuari', 'Bajo Ventuari', 'Manapiare'],
                'Maroa' => ['Maroa', 'Victorino'],
                'Río Negro' => ['Casiquire', 'Cocuy', 'San Carlos de Río Negro', 'Solano'],
            ],

            // Anzoátegui
            'Anzoátegui' => [
                'Anaco' => ['Anaco', 'San Joaquín'],
                'Aragua' => ['Aragua de Barcelona', 'Cachipo'],
                'Bolívar' => ['Bergantín'],
                'Bruzual' => ['Caigua', 'El Carmen', 'El Pilar', 'Naricual', 'San Cristóbal'],
                'Cajigal' => ['Clarines', 'Guanape', 'Sabana de Uchire'],
                'Carvajal' => ['Valle de Guanape', 'Santa Bárbara'],
                'Diego Bautista Urbaneja' => ['Lecherías', 'El Morro'],
                'Freites' => ['Cantaura', 'Libertador', 'Santa Rosa', 'Urica'],
                'Guanipa' => ['San José de Guanipa'],
                'Guanta' => ['Guanta', 'Chorrerón'],
                'Independencia' => ['Mamo', 'Soledad'],
                'Libertad' => ['San Mateo', 'El Carito'],
                'McGregor' => ['San Simón'],
                'Miranda' => ['Pariaguán', 'Atapirire'],
                'Monagas' => ['Mapire', 'Piar'],
                'Peñalver' => ['Puerto Píritu', 'San Miguel'],
                'Píritu' => ['Boca de Uchire', 'Boca de Chávez'],
                'San Juan de Capistrano' => ['Santa Inés', 'Pueblo Nuevo'],
                'Santa Ana' => ['Santa Ana'],
                'Simón Rodríguez' => ['El Chaparro', 'Tomás Alfaro'],
                'Sotillo' => ['Puerto La Cruz', 'Pozuelos'],
            ],

            // Apure
            'Apure' => [
                'Achaguas' => ['Achaguas', 'Apurito', 'El Yagual', 'Guachara', 'Mucuritas', 'Queseras del Medio'],
                'Biruaca' => ['Biruaca'],
                'Muñoz' => ['Bruzual', 'Mantecal', 'Quintero', 'Rincón Hondo', 'San Vicente'],
                'Páez' => ['Guasdualito', 'Aramendi', 'El Amparo', 'San Camilo', 'Urdaneta'],
                'Pedro Camejo' => ['San Juan de Payara', 'Codazzi', 'Cunaviche'],
                'Rómulo Gallegos' => ['Elorza', 'La Trinidad'],
                'San Fernando' => ['San Fernando', 'El Recreo', 'Peñalver', 'San Rafael de Atamaica'],
            ],

            // Aragua
            'Aragua' => [
                'Bolívar' => ['San Mateo', 'Camatagua'],
                'Camatagua' => ['Camatagua', 'Carmen de Cura'],
                'Francisco Linares Alcántara' => ['Santa Rita', 'Francisco de Miranda', 'Moseñor Feliciano González'],
                'Girardot' => ['Choroní', 'Las Delicias', 'Madre María de San José', 'Pedro José Ovalles', 'Joaquín Crespo', 'José Casanova Godoy', 'Andrés Eloy Blanco', 'Los Tacariguas'],
                'José Ángel Lamas' => ['Santa Cruz'],
                'José Félix Ribas' => ['La Victoria', 'Castor Nieves Ríos', 'Guayabita', 'Pao de Zárate', 'Zuata'],
                'José Rafael Revenga' => ['El Consejo', 'Pueblo de Revenga'],
                'Libertador' => ['Palo Negro', 'San Martín de Porres'],
                'Mario Briceño Iragorry' => ['El Limón', 'Caña de Azúcar'],
                'Ocumare de la Costa de Oro' => ['Ocumare de la Costa'],
                'San Casimiro' => ['San Casimiro', 'Güiripa', 'Ollas de Caramacate', 'Valle Morín'],
                'San Sebastián' => ['San Sebastián', 'Turmero'],
                'Santiago Mariño' => ['Turmero', 'Arevalo Aponte', 'Chuao', 'Samán de Güere', 'Alfredo Pacheco Miranda'],
                'Santos Michelena' => ['Las Tejerías', 'Tiara'],
                'Sucre' => ['Cagua', 'Bella Vista'],
                'Tovar' => ['Colonia Tovar'],
                'Urdaneta' => ['Barbacoas', 'San Francisco de Cara', 'Taguay'],
                'Zamora' => ['Villa de Cura', 'Magdaleno', 'San Francisco de Asís', 'Valles de Tucutunemo', 'Augusto Mijares'],
            ],

            // Barinas
            'Barinas' => [
                'Alberto Arvelo Torrealba' => ['Sabaneta', 'Rodríguez Domínguez'],
                'Andrés Eloy Blanco' => ['El Cantón', 'Santa Cruz de Guacas', 'Puerto Vivas'],
                'Antonio José de Sucre' => ['Socopó', 'Bum Bum', 'Campo Elías'],
                'Arismendi' => ['Arismendi', 'Guadarrama', 'La Unión', 'San Antonio'],
                'Barinas' => ['Barinas', 'Alberto Arvelo Larriva', 'San Silvestre', 'Santa Inés', 'Santa Lucía', 'Torunos', 'El Carmen', 'Rómulo Betancourt', 'Corazón de Jesús', 'Ramón Ignacio Méndez'],
                'Bolívar' => ['Barinitas', 'Altamira de Cáceres', 'Calderas'],
                'Cruz Paredes' => ['Barrancas', 'El Socorro', 'Masparrito'],
                'Ezequiel Zamora' => ['Santa Bárbara', 'Pedro Briceño Méndez', 'Ramón Ignacio Méndez', 'José Ignacio del Pumar'],
                'Obispos' => ['Obispos', 'El Real', 'La Luz', 'Los Guasimitos'],
                'Pedraza' => ['Ciudad Bolivia', 'Ignacio Briceño', 'José Félix Ribas', 'Páez'],
                'Rojas' => ['Libertad', 'Dolores', 'Palacios Fajardo', 'Santa Rosa', 'Simón Rodríguez'],
                'Sosa' => ['Ciudad de Nutrias', 'El Regalo', 'Puerto de Nutrias', 'Santa Catalina', 'Simón Bolívar'],
            ],

            // Bolívar
            'Bolívar' => [
                'Caroní' => ['Cachamay', 'Chirica', 'Dalla Costa', 'Once de Abril', 'Simón Bolívar', 'Unare', 'Universidad', 'Vista al Sol', 'Pozo Verde', 'Yocoima'],
                'Cedeño' => ['Caicara del Orinoco', 'Ascensión Farreras', 'Guaniamo', 'La Urbana', 'Pijiguaos'],
                'El Callao' => ['El Callao'],
                'Gran Sabana' => ['Santa Elena de Uairén', 'Ikabarú'],
                'Heres' => ['Agua Salada', 'Catedral', 'José Antonio Páez', 'La Sabanita', 'Marhuanta', 'Vista Hermosa', 'Orinoco', 'Panapana', 'Zea'],
                'Piar' => ['Upata', 'Andrés Eloy Blanco', 'Pedro Cova'],
                'Angostura (Raúl Leoni)' => ['Ciudad Piar', 'San Félix', 'Vista Hermosa'],
                'Roscio' => ['Guasipati', 'Salom'],
                'Sifontes' => ['Tumeremo', 'Dalla Costa', 'San Isidro'],
                'Sucre' => ['Maripa', 'Aripao', 'Guarataro', 'Las Majadas', 'Moitaco'],
                'Padre Pedro Chien' => ['El Palmar', 'Bejuma'],
            ],

            // Carabobo
            'Carabobo' => [
                'Bejuma' => ['Bejuma', 'Canoabo', 'Simón Bolívar'],
                'Carlos Arvelo' => ['Güigüe', 'Belén', 'Tacarigua'],
                'Diego Ibarra' => ['Mariara', 'Aguas Calientes'],
                'Guacara' => ['Guacara', 'Ciudad Alianza', 'Yagua'],
                'Juan José Mora' => ['Morón', 'Urama'],
                'Libertador' => ['Tocuyito', 'Independencia'],
                'Los Guayos' => ['Los Guayos'],
                'Miranda' => ['Miranda', 'Agua Linda'],
                'Montalbán' => ['Montalbán'],
                'Naguanagua' => ['Naguanagua', 'Bárbula', 'La Entrada', 'Pueblo Nuevo', 'San Esteban'],
                'Puerto Cabello' => ['Bartolomé Salom', 'Democracia', 'Fraternidad', 'Goaigoaza', 'Juan José Flores', 'Unión', 'Borburata', 'Patanemo'],
                'San Diego' => ['San Diego'],
                'San Joaquín' => ['San Joaquín'],
                'Valencia' => ['Candelaria', 'Catedral', 'El Socorro', 'Miguel Peña', 'Rafael Urdaneta', 'San Blas', 'San José', 'Santa Rosa'],
            ],

            // Cojedes
            'Cojedes' => [
                'Anzoátegui' => ['Cojedes', 'Juan de Mata Suárez'],
                'Pao de San Juan Bautista' => ['El Pao', 'Rómulo Gallegos'],
                'Tinaquillo' => ['Tinaquillo'],
                'Girardot' => ['El Baúl', 'Sucre'],
                'Lima Blanco' => ['La Aguadita', 'Macapo'],
                'Ricaurte' => ['Libertad', 'El Amparo'],
                'Rómulo Gallegos' => ['Rómulo Gallegos'],
                'Ezequiel Zamora' => ['San Carlos de Austria', 'Juan Ángel Bravo', 'Manuel Manrique'],
                'Tinaco' => ['Tinaco', 'General en Jefe José Laurencio Silva'],
            ],

            // Delta Amacuro
            'Delta Amacuro' => [
                'Antonio Díaz' => ['Curiapo', 'Almirante Luis Brión', 'Francisco Aniceto Lugo', 'Manuel Renaud', 'Padre Barral', 'Santos de Abelgas'],
                'Casacoima' => ['Imataca', 'Juan Bautista Arismendi', 'Manuel Piar', 'Rómulo Gallegos'],
                'Pedernales' => ['Pedernales', 'Luis Beltrán Prieto Figueroa'],
                'Tucupita' => ['Tucupita', 'José Vidal Marcano', 'Juan Millán', 'Leonardo Ruíz Pineda', 'Mariscal Antonio José de Sucre', 'Monseñor Argimiro García', 'San Rafael', 'Virgen del Valle'],
            ],

            // Falcón
            'Falcón' => [
                'Acosta' => ['San Juan de los Cayos', 'Capadare', 'La Pastora', 'Libertador'],
                'Bolívar' => ['San Luis', 'Aracua', 'La Peña'],
                'Buchivacoa' => ['Capatárida', 'Bariro', 'Borojó', 'Guajiro', 'Seque', 'Zazárida'],
                'Cacique Manaure' => ['Yaracal', 'Churuguara'],
                'Carirubana' => ['Punto Fijo', 'Carirubana', 'Punta Cardón', 'Santa Ana'],
                'Colina' => ['La Vela de Coro', 'Acurigua', 'Guaibacoa', 'Las Calderas', 'Macoruca'],
                'Dabajuro' => ['Dabajuro'],
                'Democracia' => ['Pedregal', 'Agua Clara', 'Avaria', 'Bijagual', 'El Charito', 'Guanarito', 'La Sierrita'],
                'Falcón' => ['Pueblo Nuevo', 'Adícora', 'Baraived', 'Buena Vista', 'Jadacaquiva', 'Moruy', 'Adaure', 'El Hato', 'El Vínculo'],
                'Federación' => ['Churuguara', 'Agua Larga', 'El Paují', 'Independencia', 'Mapararí'],
                'Jacura' => ['Jacura', 'Agua Linda', 'Araurima'],
                'Los Taques' => ['Los Taques', 'Judibana'],
                'Mauroa' => ['Mene de Mauroa', 'Casigua', 'San Félix'],
                'Miranda' => ['Santa Cruz de Los Taques', 'Chichiriviche'],
                'Monseñor Iturriza' => ['Chichiriviche', 'Boca de Tocuyo', 'Tocuyo de la Costa'],
                'Palmasola' => ['Palmasola'],
                'Petit' => ['Cabure', 'Colina', 'Curimagua'],
                'Píritu' => ['Píritu', 'San José de la Costa'],
                'San Francisco' => ['Mirimire', 'Tucacas'],
                'Silva' => ['Tucacas', 'Boca de Aroa'],
                'Sucre' => ['La Cruz de Taratara', 'Sucre'],
                'Tocópero' => ['Tocópero'],
                'Unión' => ['Santa María de Caparo'],
                'Urumaco' => ['Urumaco', 'Bruzual'],
                'Zamora' => ['Puerto Cumarebo', 'La Ciénaga', 'Palmarejo', 'Pueblo Cumarebo'],
            ],

            // Guárico
            'Guárico' => [
                'Camaguán' => ['Camaguán', 'Puerto Miranda', 'Uverito'],
                'Chaguaramas' => ['Chaguaramas'],
                'El Socorro' => ['El Socorro'],
                'Francisco de Miranda' => ['Calabozo', 'El Calvario', 'El Rastro', 'Guardatinajas'],
                'José Félix Ribas' => ['Tucupido', 'San Rafael de Laya'],
                'José Tadeo Monagas' => ['Altagracia de Orituco', 'San Rafael de Orituco', 'Soublette', 'San Francisco de Macaira', 'Lezama', 'Paso Real de Macaira'],
                'Juan Germán Roscio' => ['San Juan de Los Morros', 'Cantagallo', 'Parapara'],
                'Julián Mellado' => ['El Sombrero', 'Sosa'],
                'Las Mercedes' => ['Las Mercedes', 'Cabruta', 'Santa Rita de Manapire'],
                'Leonardo Infante' => ['Valle de la Pascua', 'Espino'],
                'Pedro Zaraza' => ['Zaraza', 'San José de Unare'],
                'Ortiz' => ['Ortiz', 'San Francisco de Tiznados', 'San José de Tiznados', 'San Lorenzo de Tiznados'],
                'San Gerónimo de Guayabal' => ['Guayabal', 'Cazorla'],
                'San José de Guaribe' => ['San José de Guaribe'],
                'Santa María de Ipire' => ['Santa María de Ipire', 'Altamira'],
            ],

            // Lara
            'Lara' => [
                'Andrés Eloy Blanco' => ['Sanare', 'Río Blanco', 'Tamayo'],
                'Crespo' => ['Duaca', 'Aguedo Felipe Alvarado', 'Juárez'],
                'Iribarren' => ['Concepción', 'El Cují', 'Juan de Villegas', 'Santa Rosa', 'Tamaca', 'Unión', 'Aguedo Felipe Alvarado', 'Buena Vista', 'Juárez'],
                'Jiménez' => ['Quíbor', 'Juan Bautista Rodríguez', 'Blanca Nieve de Páez', 'Yacambú'],
                'Morán' => ['El Tocuyo', 'Anzoátegui', 'Humocaro Alto', 'Humocaro Bajo', 'La Candelaria', 'Morán'],
                'Palavecino' => ['Cabudare', 'José Gregorio Bastidas'],
                'Simón Planas' => ['Sarare', 'Buría', 'Gustavo Vega'],
                'Torres' => ['Carora', 'Antonio Díaz', 'Camacaro', 'Castellanos', 'Cecilio Zubillaga', 'Heriberto Arrollo', 'Manuel Morillo', 'Trinidad Samuel'],
                'Urdaneta' => ['Siquisique', 'San Miguel', 'Moroturo', 'Xaguas'],
            ],

            // Mérida
            'Mérida' => [
                'Alberto Adriani' => ['El Vigía', 'La Azulita', 'Santa María de Caparo'],
                'Andrés Bello' => ['La Luz', 'San José de Bolívar'],
                'Antonio Pinto Salinas' => ['Santa Cruz de Mora', 'Mesa Bolívar', 'Mesa de Las Palmas'],
                'Aricagua' => ['Aricagua', 'San Antonio'],
                'Arzobispo Chacón' => ['Canagua', 'Capurí', 'Chacantá', 'El Molino', 'Guaimaral', 'Mucutuy', 'Mucuchachí'],
                'Campo Elías' => ['Ejido', 'Ignacio Fernández Peña', 'Montañita', 'Los Nevados'],
                'Caracciolo Parra Olmedo' => ['Tucaní', 'Florencio Ramírez'],
                'Cardenal Quintero' => ['Santo Domingo', 'Las Piedras'],
                'Guaraque' => ['Guaraque', 'Mesa de Quintero', 'Río Negro'],
                'Julio César Salas' => ['Arapuey', 'Palmira'],
                'Justo Briceño' => ['Torondoy', 'San Antonio de Torondoy', 'Santo Domingo'],
                'Libertador' => ['Arias', 'Caracciolo Parra Olmedo', 'Domingo Peña', 'El Llano', 'Gonzalo Picón Febres', 'Jacinto Plaza', 'Juan Rodríguez Suárez', 'Lasso de la Vega', 'Mariano Picón Salas', 'Milla', 'Osuna Rodríguez', 'Sagrario', 'El Morro', 'Los Nevados'],
                'Miranda' => ['Timotes', 'Andrés Eloy Blanco', 'La Venta', 'Piñango'],
                'Obispo Ramos de Lora' => ['Santa Elena de Arenales', 'Eloy Paredes', 'San Rafael de Alcázar'],
                'Padre Noguera' => ['Santa María de Caparo', 'Pueblo Llano'],
                'Pueblo Llano' => ['Pueblo Llano'],
                'Rangel' => ['Mucuchíes', 'Cacute', 'La Toma', 'Mucurubá', 'San Rafael'],
                'Rivas Dávila' => ['Bailadores', 'Gerónimo Maldonado'],
                'Santos Marquina' => ['Tabay'],
                'Sucre' => ['Lagunillas', 'Chiguará', 'Estánquez', 'La Trampa', 'Pueblo Nuevo del Sur', 'San Juan'],
                'Tovar' => ['Tovar', 'El Amparo', 'San Francisco', 'El Llano'],
                'Tulio Febres Cordero' => ['Nueva Bolivia', 'Independencia', 'Santa Apolonia', 'María de la Concepción Palacios Blanco'],
                'Zea' => ['Zea', 'Caño El Tigre'],
            ],

            // Miranda
            'Miranda' => [
                'Acevedo' => ['Caucagua', 'Aragüita', 'Arévalo González', 'Capaya', 'El Café', 'Marizapa', 'Panaquire', 'Ribas'],
                'Andrés Bello' => ['San José de Barlovento', 'Cumbo'],
                'Baruta' => ['Baruta', 'El Cafetal', 'Las Minas de Baruta'],
                'Brión' => ['Higuerote', 'Curiepe', 'Tacarigua de Brión'],
                'Buroz' => ['Mamporal'],
                'Carrizal' => ['Carrizal'],
                'Chacao' => ['Chacao'],
                'Cristóbal Rojas' => ['Charallave', 'Las Brisas'],
                'El Hatillo' => ['El Hatillo'],
                'Guaicaipuro' => ['Los Teques', 'Altagracia de la Montaña', 'Cecilio Acosta', 'El Jarillo', 'Paracotos', 'San Pedro', 'Tácata'],
                'Independencia' => ['Santa Teresa del Tuy', 'El Cartanal', 'Ocumare del Tuy'],
                'Lander' => ['Ocumare del Tuy', 'La Democracia', 'Santa Bárbara'],
                'Los Salias' => ['San Antonio de los Altos'],
                'Páez' => ['Río Chico', 'El Guapo', 'Tacarigua de la Laguna', 'Paparo', 'San Fernando del Guapo'],
                'Paz Castillo' => ['Santa Lucía del Tuy'],
                'Pedro Gual' => ['Cúpira', 'Machurucuto'],
                'Plaza' => ['Guarenas'],
                'Simón Bolívar' => ['San Francisco de Yare', 'San Antonio de Yare'],
                'Sucre' => ['Petare', 'Caucagüita', 'Fila de Mariche', 'La Dolorita', 'Leoncio Martínez'],
                'Urdaneta' => ['Cúa', 'Nueva Cúa'],
                'Zamora' => ['Guatire', 'Bolívar'],
            ],

            // Monagas
            'Monagas' => [
                'Acosta' => ['San Antonio de Capayacuar', 'El Guácharo', 'La Guanota', 'Sabana de Piedra', 'San Francisco'],
                'Aguasay' => ['Aguasay'],
                'Bolívar' => ['Caripito'],
                'Caripe' => ['Caripe', 'El Guácharo', 'La Guanota', 'Sabana de Piedra', 'San Agustín', 'Teresén'],
                'Cedeño' => ['Caicara', 'Areo', 'San Félix', 'Viento Fresco'],
                'Ezequiel Zamora' => ['Punta de Mata', 'El Tejero'],
                'Libertador' => ['Temblador', 'Chaguaramas', 'El Furrial', 'El Corozo', 'Jusepín', 'La Pica', 'San Vicente'],
                'Maturín' => ['Maturín', 'Alto de los Godos', 'Boquerón', 'Las Cocuizas', 'San Simón', 'Santa Cruz', 'El Corozo', 'El Furrial', 'Jusepín', 'La Pica', 'San Vicente'],
                'Piar' => ['Aragua', 'Chaguaramas', 'El Pinto', 'Guanaguana', 'La Toscana', 'Taguaya'],
                'Punceres' => ['Quiriquire', 'Cachipo'],
                'Santa Bárbara' => ['Santa Bárbara', 'Morón'],
                'Sotillo' => ['Barrancas', 'Los Barrancos de Fajardo'],
                'Uracoa' => ['Uracoa'],
            ],

            // Nueva Esparta
            'Nueva Esparta' => [
                'Antolín del Campo' => ['La Plaza de Paraguachí'],
                'Arismendi' => ['San Juan Bautista', 'Zabala'],
                'Díaz' => ['Santa Ana', 'Guevara', 'Matasiete', 'Sucre'],
                'García' => ['El Valle', 'Francisco Fajardo'],
                'Gómez' => ['Santa Ana', 'Bolívar', 'Guevara'],
                'Maneiro' => ['Pampatar', 'Aguirre'],
                'Marcano' => ['Juan Griego', 'Adrián'],
                'Mariño' => ['Porlamar'],
                'Península de Macanao' => ['San Francisco de Macanao', 'Boca de Río'],
                'Tubores' => ['Punta de Piedras', 'Los Barales'],
                'Villalba' => ['San Pedro de Coche', 'Vicente Fuentes'],
            ],

            // Portuguesa
            'Portuguesa' => [
                'Araure' => ['Araure', 'Río Acarigua'],
                'Agua Blanca' => ['Agua Blanca'],
                'Esteller' => ['Píritu', 'Uveral'],
                'Guanare' => ['Guanare', 'Córdoba', 'San José de la Montaña', 'San Juan de Guanaguanare', 'Virgen de la Coromoto'],
                'Guanarito' => ['Guanarito', 'Trinidad de la Capilla', 'Divina Pastora'],
                'Monseñor José Vicente de Unda' => ['Paraíso de Chabasquén', 'Peñalver'],
                'Ospino' => ['Ospino', 'Aparición', 'La Estación'],
                'Páez' => ['Acarigua', 'Payara', 'Pimpinela', 'Ramón Peraza'],
                'Papelón' => ['Papelón', 'Caño Delgadito'],
                'San Genaro de Boconoíto' => ['Boconoíto', 'Antolín Tovar'],
                'San Rafael de Onoto' => ['San Rafael de Onoto', 'Santa Fe', 'Thermo Morales'],
                'Santa Rosalía' => ['El Playón', 'Florida'],
                'Sucre' => ['Biscucuy', 'Concepción', 'San José de Saguaz', 'San Rafael de Palo Alzado', 'Uvencio Antonio Velásquez', 'Villa Rosa'],
                'Turén' => ['Villa Bruzual', 'Canelones', 'Santa Cruz', 'San Isidro Labrador'],
            ],

            // Sucre
            'Sucre' => [
                'Andrés Eloy Blanco' => ['Casanay', 'Tunapuy'],
                'Andrés Mata' => ['San José de Aerocuar', 'Tavera Acosta'],
                'Arismendi' => ['Río Caribe', 'Antonio José de Sucre', 'El Morro de Puerto Santo', 'Puerto Santo', 'San Juan de las Galdonas'],
                'Benítez' => ['El Pilar', 'El Rincón', 'General Francisco Antonio Vázquez', 'Guaraúnos', 'Tunapuicito', 'Unión'],
                'Bermúdez' => ['Carúpano', 'Santa Catalina', 'Santa Rosa', 'Santa Teresa', 'Bolívar', 'Marigüitar'],
                'Bolívar' => ['Yaguaraparo', 'El Paujil', 'Libertad'],
                'Cajigal' => ['Irapa', 'Campo Claro', 'Marabal', 'San Antonio de Irapa', 'Soro'],
                'Cruz Salmerón Acosta' => ['Araya', 'Chacopata', 'Manicuare'],
                'Libertador' => ['Tunapuy', 'Campo Elías'],
                'Mariño' => ['Cumanacoa', 'Arenas', 'Aricagua', 'Cocollar', 'San Fernando', 'San Lorenzo'],
                'Mejía' => ['San Antonio del Golfo'],
                'Montes' => ['Cariaco', 'Catuaro', 'Rendón', 'Santa Cruz', 'Santa María'],
                'Ribero' => ['Cumaná', 'Ayacucho', 'Puerto Sucre', 'Santa Fe', 'Valentín Valiente'],
                'Sucre' => ['Yaguaraparo', 'Altos de Sucre'],
                'Valdez' => ['Güiria', 'Cristóbal Colón', 'Punta de Piedras'],
            ],

            // Táchira
            'Táchira' => [
                'Andrés Bello' => ['Cordero'],
                'Antonio Rómulo Costa' => ['Las Mesas'],
                'Ayacucho' => ['San Juan de Colón', 'San Pedro del Río'],
                'Bolívar' => ['San Antonio del Táchira', 'Palotal', 'Vegas de Santa Bárbara'],
                'Cárdenas' => ['Táriba', 'Amenodoro Rangel Lamus', 'La Florida'],
                'Córdoba' => ['Santa Ana del Táchira'],
                'Fernández Feo' => ['San Rafael del Piñal', 'Alberto Adriani', 'Santo Domingo'],
                'Francisco de Miranda' => ['San José de Bolívar'],
                'García de Hevia' => ['La Fría', 'Boca de Grita', 'José Antonio Páez'],
                'Guásimos' => ['Palmira'],
                'Independencia' => ['Capacho Nuevo', 'Capacho Viejo', 'San Pedro del Río', 'La Grita'],
                'Jáuregui' => ['La Grita', 'Emeterio Ochoa', 'Palmira'],
                'José María Vargas' => ['El Cobre'],
                'Junín' => ['Rubio', 'Bramón', 'La Petrólea', 'Quinimarí'],
                'Libertad' => ['Capacho', 'Cipriano Castro', 'Manuel Felipe Rugeles'],
                'Libertador' => ['Abejales', 'San Carlos de Río Negro', 'San Joaquín de Navay'],
                'Lobatera' => ['Lobatera', 'Constitución'],
                'Michelena' => ['Michelena'],
                'Panamericano' => ['Coloncito', 'La Palmita'],
                'Pedro María Ureña' => ['Ureña', 'Nueva Arcadia'],
                'Rafael Urdaneta' => ['Delicias', 'San José de Bolívar'],
                'Samuel Darío Maldonado' => ['San José de Bolívar', 'Boconoíto'],
                'San Cristóbal' => ['San Cristóbal', 'Juan Germán Roscio', 'Francisco Romero Lobo', 'El Recreo', 'Pedro María Morantes', 'Presidente Páez', 'Isaías Medina Angarita'],
                'San Judas Tadeo' => ['Umuquena'],
                'Seboruco' => ['Seboruco'],
                'Simón Rodríguez' => ['San Simón'],
                'Sucre' => ['Queniquea', 'San Pablo', 'Eleazar López Contreras'],
                'Torbes' => ['San Josecito'],
                'Uribante' => ['Pregonero', 'Cardenas', 'Potosi', 'Juan Pablo Peñaloza'],
            ],

            // Trujillo
            'Trujillo' => [
                'Andrés Bello' => ['Santa Isabel', 'Araguaney'],
                'Boconó' => ['Boconó', 'El Carmen', 'Mosquey', 'Ayacucho', 'Buena Vista', 'General Ribas', 'Guaramacal', 'Vega de Guaramacal', 'Monseñor Jáuregui'],
                'Bolívar' => ['Sabana Grande', 'Cheregüé', 'Granados'],
                'Candelaria' => ['Chejendé', 'Arnoldo Gabaldón', 'Bolivia'],
                'Carache' => ['Carache', 'Cuicas', 'Panamericana', 'Santa Cruz'],
                'Escuque' => ['Escuque', 'La Unión', 'Sabana Libre', 'Santa Rita'],
                'José Felipe Márquez Cañizalez' => ['El Socorro', 'Los Caprichos', 'Antonio José de Sucre'],
                'Juan Vicente Campo Elías' => ['Campo Elías', 'Arnoldo Gabaldón'],
                'La Ceiba' => ['Santa Apolonia', 'El Progreso', 'La Ceiba', 'Triunfo'],
                'Miranda' => ['El Dividive', 'Agua Santa', 'Agua Caliente', 'El Cenizo', 'Valerita'],
                'Monte Carmelo' => ['Monte Carmelo', 'Buena Vista', 'Santa María del Horcón'],
                'Motatán' => ['Motatán', 'El Baño', 'Jalisco'],
                'Pampán' => ['Pampán', 'Flor de Patria', 'La Paz'],
                'Pampanito' => ['Pampanito', 'La Concepción', 'Pampanito II'],
                'Rafael Rangel' => ['Betijoque', 'José Gregorio Hernández', 'La Pueblita', 'Los Cedros'],
                'San Rafael de Carvajal' => ['Carvajal', 'Antonio Nicolás Briceño', 'Campo Alegre', 'José Leonardo Suárez'],
                'Sucre' => ['Sabana de Mendoza', 'El Paraíso', 'Junín', 'Valmore Rodríguez'],
                'Trujillo' => ['Trujillo', 'Andrés Linares', 'Chiquinquirá', 'Cristóbal Mendoza', 'Cruz Carrillo', 'Matriz', 'Monseñor Carrillo', 'José Vicente Campo Elías'],
                'Urdaneta' => ['La Quebrada', 'Cabimbú', 'Jajó', 'La Mesa', 'Santiago'],
                'Valera' => ['Valera', 'Juan Ignacio Montilla', 'La Beatriz', 'La Puerta', 'Mendoza del Valle de Momboy', 'Mercedes Díaz', 'San Luis'],
            ],

            // La Guaira
            'La Guaira' => [
                'Vargas' => [
                    'Caraballeda', 'Carayaca', 'Carlos Soublette', 'Caruao', 'Catia La Mar', 
                    'El Junko', 'La Guaira', 'Macuto', 'Maiquetía', 'Naiguatá', 
                    'Urimare', 'Camurí Grande'
                ]
            ],

            // Yaracuy
            'Yaracuy' => [
                'Arístides Bastidas' => ['San Pablo'],
                'Bolívar' => ['Aroa'],
                'Bruzual' => ['Chivacoa', 'Campo Elías'],
                'Cocorote' => ['Cocorote'],
                'Independencia' => ['Independencia'],
                'José Antonio Páez' => ['Sabana de Parra'],
                'La Trinidad' => ['Boraure'],
                'Manuel Monge' => ['Yaritagua'],
                'Nirgua' => ['Nirgua', 'Salom', 'Temerla'],
                'Peña' => ['San Felipe', 'Albarico', 'San Javier'],
                'San Felipe' => ['San Felipe'],
                'Sucre' => ['Guama', 'Sucre'],
                'Urachiche' => ['Urachiche'],
                'Veroes' => ['Farriar', 'El Guayabo', 'Isla de Toas'],
            ],

            // Zulia
            'Zulia' => [
                'Almirante Padilla' => ['El Toro', 'San José', 'San Rafael', 'Santa Rosa'],
                'Baralt' => ['San Timoteo', 'Mene Grande', 'Bartolomé de las Casas'],
                'Cabimas' => ['Ambrosio', 'Carmen Herrera', 'Germán Ríos Linares', 'Jorge Hernández', 'La Rosa', 'Arístides Calvani', 'Rómulo Betancourt', 'San Benito', 'Punta Gorda'],
                'Catatumbo' => ['Encontrados', 'Udón Pérez'],
                'Colón' => ['San Carlos del Zulia', 'Moralito', 'Santa Bárbara', 'Santa Cruz del Zulia', 'Urribarrí'],
                'Francisco Javier Pulgar' => ['El Batey', 'Bobures', 'El Carmelo', 'Monseñor Arturo Celestino Álvarez'],
                'Jesús Enrique Lossada' => ['La Concepción', 'José Ramón Yépez', 'Mariano Parra León', 'San José'],
                'Jesús María Semprún' => ['Boscán', 'El Guayabo', 'Isla de Toas', 'Monte Carmelo'],
                'La Cañada de Urdaneta' => ['Concepción', 'Andrés Bello', 'Chiquinquirá', 'El Carmelo', 'Potreritos'],
                'Lagunillas' => ['Ciudad Ojeda', 'Alonso de Ojeda', 'Venezuela', 'Eleazar López Contreras', 'Campo Lara'],
                'Machiques de Perijá' => ['Machiques', 'Colón', 'Francisco Eugenio Bustamante', 'José María Semprún', 'Río Negro'],
                'Mara' => ['San Rafael del Moján', 'Altagracia', 'Chiquinquirá', 'Elías Sánchez Rubio', 'Guana', 'Isla de Toas', 'Las Piedras', 'Libertad', 'Padre José de Shea', 'Ricaurte', 'Tamare', 'Urribarrí'],
                'Maracaibo' => ['Antonio Borjas Romero', 'Bolívar', 'Cacique Mara', 'Carracciolo Parra Pérez', 'Cecilio Acosta', 'Cristo de Aranza', 'Coquivacoa', 'Chiquinquirá', 'Francisco Eugenio Bustamante', 'Idelfonzo Vásquez', 'Juana de Ávila', 'Luis Hurtado Higuera', 'Manuel Dagnino', 'Olegario Villalobos', 'Raúl Leoni', 'Santa Lucía', 'Venancio Pulgar', 'San Isidro'],
                'Miranda' => ['Los Puertos de Altagracia', 'Páez', 'Ricaurte', 'San José', 'San Miguel', 'Urdaneta'],
                'Guajira' => ['Sinamaica', 'Alta Guajira', 'Elías Sánchez Rubio', 'Guajira'],
                'Rosario de Perijá' => ['La Concepción', 'San José', 'Mariano Parra León', 'José Ramón Yépez'],
                'San Francisco' => ['San Francisco', 'El Bajo', 'Domitila Flores', 'Francisco Ochoa', 'Los Cortijos', 'Mariano Parra León', 'Monseñor Sergio Godoy', 'Rafael Urdaneta', 'Robert Serra', 'San Isidro'],
                'Santa Rita' => ['Santa Rita', 'El Mene', 'José Cenobio Urribarrí', 'Pedro Lucas Urribarrí'],
                'Simón Bolívar' => ['Simón Bolívar', 'Carlos Quevedo', 'Francisco Javier Pulgar'],
                'Sucre' => ['Bobures', 'El Batey', 'Monseñor Arturo Celestino Álvarez'],
                'Valmore Rodríguez' => ['Bachaquero', 'Libertad', 'Mene Grande'],
            ],

            // Distrito Capital
            'Distrito Capital' => [
                'Libertador' => [
                    'Altagracia', 'Antímano', 'Candelaria', 'Caricuao', 'Catedral', 'Coche', 
                    'El Junquito', 'El Paraíso', 'El Recreo', 'El Valle', 'Catedral', 
                    'La Pastora', 'La Vega', 'Macarao', 'San Agustín', 'San Bernardino', 
                    'San José', 'San Juan', 'San Pedro', 'Santa Rosalía', 'Santa Teresa', 
                    'Sucre (Catia)'
                ]
            ],
        ];

        $data = [];
        $contador = 0;

        foreach ($parroquiasCompletas as $estadoNombre => $municipiosEstado) {
            if (isset($municipios[$estadoNombre])) {
                foreach ($municipiosEstado as $municipioNombre => $parroquiasMunicipio) {
                    // Buscar el municipio correspondiente
                    $municipio = $municipios[$estadoNombre]->firstWhere('municipio', $municipioNombre);
                    
                    if ($municipio) {
                        foreach ($parroquiasMunicipio as $parroquia) {
                            $data[] = [
                                'nombre' => $parroquia,
                                'municipio_id' => $municipio->id,
                                'created_at' => $now,
                                'updated_at' => $now
                            ];
                            $contador++;
                            
                            // Insertar en lotes de 100 para mejor performance
                            if (count($data) >= 100) {
                                DB::table('parroquias')->insert($data);
                                $data = [];
                            }
                        }
                    } else {
                        $this->command->warn("Municipio no encontrado: {$municipioNombre} en {$estadoNombre}");
                    }
                }
            } else {
                $this->command->warn("Estado no encontrado: {$estadoNombre}");
            }
        }

        // Insertar los registros restantes
        if (!empty($data)) {
            DB::table('parroquias')->insert($data);
        }

        $this->command->info("Se insertaron {$contador} parroquias.");
    }
}