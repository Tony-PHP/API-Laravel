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
    
    function comparativa($f3)
    {
        $body = json_decode($f3->get('BODY'), true);

        $anio = isset($body['anio']) ? $body['anio'] : null;
        $mes = isset($body['mes']) ? $body['mes'] : null;
        $idTecnico = isset($body['idTecnico']) ? $body['idTecnico'] : null;
        $opcion = isset($body['opcion']) ? $body['opcion'] : null;

        if ($anio === null || $mes === null || $idTecnico === null || $opcion === null) {
            echo json_encode(['error' => 'Faltan parámetros requeridos: anio, mes, idTecnico u opcion']);
            return;
        }

        if ($opcion == 1) {
            $sql = "WITH meses AS (
                SELECT :anio AS Anio, :mes AS Mes
            )
            SELECT 
                meses.Anio,
                meses.Mes,
                COALESCE(Telmex.TotalProduccion, 0) AS Registros_Telmex,
                COALESCE(ED.TotalProduccion, 0) AS Registros_ED
            FROM meses
            LEFT JOIN (
                SELECT YEAR(fecha_liq) AS Anio, MONTH(fecha_liq) AS Mes, COUNT(id_detalle_produccion) AS TotalProduccion
                FROM `detalle_produccion`
                WHERE tecnico = (SELECT CONCAT(Nombre_T, ' ', Apellidos_T) FROM tecnicos WHERE idTecnico = :idTecnico)
                GROUP BY Anio, Mes
            ) AS Telmex
            ON meses.Anio = Telmex.Anio AND meses.Mes = Telmex.Mes
            LEFT JOIN (
                SELECT YEAR(Fecha_Coordiapp) AS Anio, MONTH(Fecha_Coordiapp) AS Mes, COUNT(idtecnico_instalaciones_coordiapp) AS TotalProduccion
                FROM `tecnico_instalaciones_coordiapp`
                WHERE FK_Tecnico_apps = :idTecnico
                GROUP BY Anio, Mes
            ) AS ED
            ON meses.Anio = ED.Anio AND meses.Mes = ED.Mes
            ORDER BY meses.Anio DESC, meses.Mes DESC;";
        } else if ($opcion == 2) {
            $sql = "SELECT d.folio_pisa AS 'Folios_Telmex', d.telefono AS 'TELEFONOS_TELMEX'
                    FROM detalle_produccion d 
                    WHERE d.tecnico = (SELECT CONCAT(t.Nombre_T, ' ', t.Apellidos_T) 
                                    FROM tecnicos t 
                                    WHERE t.idTecnico = :idTecnico) 
                    AND MONTH(d.fecha_liq) = :mes
                    AND YEAR(d.fecha_liq) = :anio
                    AND (d.folio_pisa, d.telefono) NOT IN (
                        SELECT i.Folio_Pisa, i.Telefono 
                        FROM tecnico_instalaciones_coordiapp i 
                        WHERE i.FK_Tecnico_apps = :idTecnico
                        AND MONTH(i.Fecha_Coordiapp) = :mes
                        AND YEAR(i.Fecha_Coordiapp) = :anio
                    )
                    ORDER BY d.folio_pisa DESC;";
        }

        $db = $f3->get('DB');
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':anio', $anio, PDO::PARAM_INT);
        $stmt->bindParam(':mes', $mes, PDO::PARAM_INT);
        $stmt->bindParam(':idTecnico', $idTecnico, PDO::PARAM_INT);
        $stmt->bindParam(':opcion', $opcion, PDO::PARAM_INT);

        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($result) {
            echo json_encode($result);
        } else {
            echo json_encode([]);
        }
    }
}