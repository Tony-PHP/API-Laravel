<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class m_coordiApp extends Model
{
    public function getOrdenesCompletadas($FK_Tecnico_apps)
    {
        return DB::table('View_Detalle_Coordiapp_Completadas')
            ->where('FK_Tecnico_apps', $FK_Tecnico_apps)
            ->get();
    }

    public function getOrdenesIncompletadas($FK_Tecnico_apps)
    {
        return DB::table('View_Detalle_Coordiapp_Incompletas')
            ->where('FK_Tecnico_apps', $FK_Tecnico_apps)
            ->get();
    }

    public function obtenerOpciones($step, $bindings)
    {
        $queries = [
            1 => "SELECT d.idDivision AS idDivision, d.Division AS nameDivision, a.idAreas AS idAreas, 
                a.area AS nameArea, c.id AS idCope, c.COPE AS COPE 
                FROM divisiones d 
                JOIN areas a ON a.FK_Division = d.idDivision 
                JOIN copes c ON c.FK_Area = a.idAreas",
            '5e' => "SELECT id_estado as idEstado, estado as nameEstado FROM t_estado",
            '5m' => "SELECT id as idMunicipio, nombre as nameMunicipio, estado as estadoMunicipio 
                    FROM municipios WHERE estado = :idEstado",
            '5c' => "SELECT nombre as nameColonia, municipio as idMunicipio, codigo_postal as CodigoPostal 
                    FROM colonias WHERE municipio = :idMunicipio",
            6 => "SELECT idSalidas, Num_Serie_Salida_Det 
                FROM salidas_contratistas 
                WHERE FK_Tecnico_Salida_Det = :idTecnico AND Ont_Ubicacion = 'TECNICO'"
        ];

        if (!isset($queries[$step])) {
            throw new \InvalidArgumentException('Parámetro step no válido');
        }

        return DB::select($queries[$step], $bindings);
    }

    public function getOrden($Folio_Pisa)
    {
        return DB::table('tecnico_instalaciones_coordiapp')
            ->where('Folio_Pisa', $Folio_Pisa)
            ->get();
    }
    
    public function actualizarRegistro($id, $params, $allowedFields, $imageFields, $folioPisa)
    {
        DB::beginTransaction();

        try {
            $setClauses = [];
            $values = [];

            // Procesar campos permitidos
            foreach ($allowedFields as $key => $dbField) {
                if (isset($params[$key]) && $params[$key] !== '') {
                    $setClauses[] = "$dbField = ?";
                    $values[] = $params[$key];
                }
            }

            // Procesar campos de imágenes
            foreach ($imageFields as $formField => $folderName) {
                if (isset($params[$formField]) && !empty($params[$formField])) {
                    $base64String = $params[$formField];
                    $fileParts = explode(';base64,', $base64String);

                    if (count($fileParts) === 2) {
                        $fileType = explode(':', $fileParts[0])[1];
                        $fileData = base64_decode($fileParts[1]);

                        $fileExtension = str_replace('image/', '', $fileType);
                        $uniqueFileName = $folioPisa . '.jpg';

                        $uploadDir = public_path('imagesCordiapp/' . $folderName . '/');
                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }

                        $filePath = $uploadDir . $uniqueFileName;

                        if (file_put_contents($filePath, $fileData)) {
                            $relativePath = 'imagesCordiapp/' . $folderName . '/' . $uniqueFileName;
                            $values[] = $relativePath;
                            $setClauses[] = "$formField = ?";
                        } else {
                            throw new \Exception('Error al guardar el archivo: ' . $formField);
                        }
                    } else {
                        throw new \Exception('Error en el formato de la imagen: ' . $formField);
                    }
                }
            }

            if (empty($setClauses)) {
                throw new \Exception('No hay campos válidos para actualizar.');
            }

            $values[] = $id;

            $sql = "UPDATE tecnico_instalaciones_coordiapp SET " . implode(', ', $setClauses) . " WHERE idtecnico_instalaciones_coordiapp = ?";
            DB::update($sql, $values);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function obtenerFolioPisa($id)
    {
        $sql = "SELECT Folio_Pisa FROM tecnico_instalaciones_coordiapp WHERE idtecnico_instalaciones_coordiapp = ?";
        $result = DB::select($sql, [$id]);

        return $result ? $result[0]->Folio_Pisa : null;
    }
}