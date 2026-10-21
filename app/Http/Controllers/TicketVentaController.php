<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\PageWeb;
use App\Models\Venta;
use Barryvdh\DomPDF\Facade\Pdf;

use Illuminate\Http\Request;
use setasign\Fpdi\Fpdi;
use Luecano\NumeroALetras\NumeroALetras;

// Importaciones para Endroid QR Code v6.x
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\FacadesLog;
use Illuminate\Support\Facades\Storage;

class TicketVentaController extends Controller
{
    public function abrirVentanaPDFTicket($identificador_web)
    {
        // Obtener la venta por identificador_web
        $venta = Venta::where('identificador_web', $identificador_web)->firstOrFail();

        // Crear una instancia de Fpdi
        $pdf = new Fpdi('P', 'mm', [80, 300]);
        $pdf->AddPage();
        $pdf->SetMargins(5, 5, 5);
        $pdf->SetFont('Arial', 'B', 9);
        //imagen de estado
        if ($venta->comprobante->tipo_comprobante == 'BOLETA' || $venta->comprobante->tipo_comprobante == 'FACTURA') {
            if ($venta->estado == 1 && (!isset($venta->emisionComprobante))) {
                $pdf->Image('images/failed-logo.png', 0, 40, 82, 56); // Imagen en diagonal
            } elseif ($venta->estado == 0) {
                $pdf->Image('images/canceled-logo.png', 0, 50, 82, 56); // Imagen en diagonal
            }
        } elseif ($venta->comprobante->tipo_comprobante == 'NOTA VENTA') {
            if ($venta->estado == 0) {
                $pdf->Image('images/canceled-logo.png', 0, 50, 82, 56); // Imagen en diagonal
            }
        }
        // Cargar datos de la cuenta (ejemplo)
        $accountTicketData = $this->getAccountTicketData();

        // Agregar imagen
        $this->addImageToPDF($pdf, $accountTicketData);

        // Título y dirección de la empresa
        $this->addCompanyDetailsToPDF($pdf, $accountTicketData);

        // Detalles de la venta
        $cliente = $venta->cliente->persona; // Obtener el cliente
        $this->addVentaDetailsToPDF($pdf, $venta, $cliente);

        // Detalles de los productos y obtener el subtotal normal
        list($subTotalNormal, $subTotalLibre, $totalDescuentos) = $this->addProductDetailsToPDF($pdf, $venta);

        // Calcular y agregar totales
        $impuesto = $venta->impuesto;

        $this->addTotalsToPDF($pdf, $subTotalNormal, $subTotalLibre, $impuesto, $totalDescuentos, $venta, $accountTicketData);

        // Salida del PDF
        $fileName = $venta->serie . $venta->correlativo . $venta->id . '.pdf';

        return response($pdf->Output('I', $fileName), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $fileName . '"')
            ->header('Cache-Control', 'private, max-age=0, must-revalidate')
            ->header('Expires', '0')
            ->header('Pragma', 'public');
    }

    private function getAccountTicketData()
    {
        // Obtener la primera empresa de la base de datos
        $empresa = Empresa::first();
        //obtener la imagen del sistema  principal (aqui en pagina web)
        $page_web = PageWeb::first();

        // Verifica si se encontró la empresa
        if ($empresa) {
            return [
                'ruc_dni' => $empresa->ruc_dni,
                'razon_social_comprobante' => $empresa->razon_social_comprobante,
                'direccion_comprobante' => $empresa->direccion_comprobante,
                'telefono_comprobante' => $empresa->telefono_comprobante,
                'img_account' => $page_web->url_sistem . '/storage/img_account_empresa/' . $empresa->img_account, // Ruta absoluta del (SISTEMA PRINCIPAL)
                'subtitulo_principal' => $empresa->subtitulo_principal_comprobante,
                'descripcion_aviso' => $empresa->descripcion_aviso_comprobante,
                'despedida' => $empresa->despedida_comprobante,
            ];
        }

        // Si no hay empresas, devuelve valores por defecto o lanza una excepción
        return [
            'nombre_empresa' => 'Nombre de la Empresa',
            'direccion' => 'Dirección de la Empresa',
            'telefono' => '999999999',
            'img_account' => 'ruta/a/imagen.png',
            'subtitulo_principal' => 'VENTAS AL POR MAYOR Y MENOR',
            'descripcion_aviso' => 'Aviso para el cliente',
            'despedida' => 'Gracias por su compra!',
        ];
    }
    private function addImageToPDF($pdf, $accountTicketData)
    {
        $img_account = $accountTicketData['img_account'];

        // Log: Mostrar la URL de la imagen
        // Log::info("URL de la imagen: " . $img_account);

        // Verificar si la URL de la imagen es válida
        $useFallbackImage = true; // Asumimos que usaremos la imagen de respaldo hasta comprobar lo contrario

        if (!empty($img_account)) {
            // Log::info("Verificando si la imagen externa existe...");
            if ($this->checkExternalImageExists($img_account)) {
                // Log::info("La imagen externa existe.");
                $useFallbackImage = false; // Cambiamos a falso si la imagen existe

                // Si la imagen es .webp, intentamos convertirla a .png
                if (pathinfo($img_account, PATHINFO_EXTENSION) === 'webp') {
                    // Log::info("La imagen es WebP. Intentando convertir a PNG...");
                    $tempPngPath = storage_path('app/public/img_account_empresa/temp_logo.png');
                    try {
                        $img_account = $this->convertWebpToPng($img_account, $tempPngPath);
                        // Log::info("Imagen convertida a PNG: " . $img_account);
                    } catch (Exception $e) {
                        // Log::error("Error al convertir la imagen WebP a PNG: " . $e->getMessage());
                        $useFallbackImage = true; // Error en la conversión, usar imagen de respaldo
                    }
                }
            } else {
                // Log::warning("La imagen externa no existe o no se pudo acceder.");
            }
        } else {
            // Log::warning("La URL de la imagen está vacía.");
        }

        // Agregar la imagen al PDF
        try {
            if (!$useFallbackImage) {
                // Log::info("Agregando imagen principal al PDF...");
                $pdf->Image($img_account, 19, 3, 40, 20); // Agrega imagen principal si existe
            } else {
                // Log::info("Usando imagen de respaldo en el PDF...");
                $pdf->Image('images/banner2.png', 17, 1, 40, 26); // Agrega imagen de respaldo
            }
        } catch (Exception $e) {
            // Log::error("Error al agregar la imagen al PDF: " . $e->getMessage());
            // Si ocurre cualquier error al agregar, forzamos la imagen de respaldo
            $pdf->Image('images/banner2.png', 17, 1, 40, 26);
        }

        // Eliminar la imagen temporal después de usarla, si existe
        if (isset($tempPngPath) && file_exists($tempPngPath)) {
            // Log::info("Eliminando imagen temporal: " . $tempPngPath);
            unlink($tempPngPath);
        }
    }

    /**
     * Verifica si una imagen externa existe haciendo una solicitud HTTP.
     *
     * @param string $url La URL de la imagen externa.
     * @return bool True si la imagen existe, False en caso contrario.
     */
    private function checkExternalImageExists($url)
    {
        // Log::info("Verificando existencia de la imagen externa: " . $url);
        $headers = @get_headers($url); // Usamos @ para suprimir advertencias si la URL no es válida

        if ($headers && strpos($headers[0], '200')) {
            // Log::info("La imagen externa existe.");
            return true; // La imagen existe
        }

        // Log::warning("La imagen externa no existe o no se pudo acceder.");
        return false; // La imagen no existe o no se pudo acceder
    }

    /**
     * Convierte una imagen WebP a PNG.
     *
     * @param string $webpUrl La URL de la imagen WebP.
     * @param string $tempPngPath La ruta temporal donde se guardará la imagen PNG.
     * @return string La ruta de la imagen PNG temporal.
     */
    private function convertWebpToPng($webpUrl, $tempPngPath)
    {
        // Log::info("Descargando imagen WebP desde: " . $webpUrl);
        $webpContent = file_get_contents($webpUrl);
        if ($webpContent === false) {
            // Log::error("No se pudo descargar la imagen WebP.");
            throw new Exception("No se pudo descargar la imagen WebP.");
        }

        // Log::info("Creando imagen desde WebP...");
        $img = imagecreatefromwebp($webpUrl);
        if ($img === false) {
            // Log::error("No se pudo crear la imagen desde WebP.");
            throw new Exception("No se pudo crear la imagen desde WebP.");
        }

        // Crear el directorio si no existe
        $directory = dirname($tempPngPath);
        if (!is_dir($directory)) {
            // Log::info("Creando directorio: " . $directory);
            mkdir($directory, 0755, true); // Crea el directorio con permisos 0755
        }

        // Log::info("Guardando imagen convertida a PNG en: " . $tempPngPath);
        if (!imagepng($img, $tempPngPath)) {
            // Log::error("No se pudo guardar la imagen PNG.");
            throw new Exception("No se pudo guardar la imagen PNG.");
        }
        imagedestroy($img);

        return $tempPngPath; // Retorna la ruta de la imagen PNG temporal
    }

    private function addCompanyDetailsToPDF($pdf, $accountTicketData)
    {
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetY(23);
        $pdf->MultiCell(70, 4, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $accountTicketData['razon_social_comprobante']), 0, 'C');
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->MultiCell(70, 3, 'RUC: ' . $accountTicketData['ruc_dni'], 0, 'C');
        $pdf->SetFont('Arial', '', 9);
        $pdf->MultiCell(70, 4, $accountTicketData['direccion_comprobante'], 0, 'C');
        $pdf->MultiCell(70, 3, 'Cel: ' . $accountTicketData['telefono_comprobante'], 0, 'C');
        $pdf->Ln(1);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->MultiCell(70, 4, $accountTicketData['subtitulo_principal'], 0, 'C');
    }

    private function addVentaDetailsToPDF($pdf, $venta, $cliente)
    {
        // Ajustar fuente y saltos para líneas específicas en la sección de detalles
        $pdf->SetFont('Arial', 'B', 8);

        // Tipo de comprobante y serie con correlativo
        $pdf->Ln(1);
        $pdf->MultiCell(70, 3, $venta->comprobante->tipo_comprobante . ' ELECTRONICA', 0, 'C');
        $pdf->MultiCell(70, 3, $venta->serie . ' - 000' . $venta->correlativo, 0, 'C');

        // Información del cliente
        $pdf->Ln(1);
        $pdf->Cell(13, 3, 'Cliente:', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->MultiCell(66, 3, ' ' . $cliente->razon_social, 0, 'L');

        // Otros detalles del cliente
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Ln(1);
        $pdf->Cell(13, 1, 'Otros:', 0, 0, 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->MultiCell(43, 1, ' ' . $cliente->numero_documento, 0, 'L');

        // Dirección del cliente
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Ln(1);
        $pdf->Cell(16, 3, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Dirección:'), 0, 0, 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->MultiCell(43, 3, ' ' . $cliente->direccion, 0, 'L');

        // Fecha y hora de emisión
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Ln(1);
        $pdf->Cell(18, 2, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'F/H Emisión:'), 0, 0, 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->MultiCell(43, 2, ' ' . $venta->fecha_hora, 0, 'L');
    }

    private function addProductDetailsToPDF($pdf, $venta)
    {
        // Línea divisoria inicial
        $pdf->Ln(2);
        $pdf->Cell(70, 2, str_repeat('-', 83), 0, 1, 'L');
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(9, 4, 'Cant', 0, 0, 'L');
        $pdf->Cell(10, 4, 'U.M.', 0, 0, 'L');
        $descripcionHeader = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Descripción');
        $descripcionHeader = $descripcionHeader === false ? 'Descripción' : $descripcionHeader;
        $pdf->Cell(30, 4, $descripcionHeader, 0, 0, 'L');
        $pdf->Cell(12, 4, 'Unit', 0, 0, 'C');
        $pdf->Cell(10, 4, 'Total', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(70, 2, str_repeat('-', 83), 0, 1, 'L');

        $subTotalNormal = 0.0; // Inicializar el subtotal para productos normales
        $totalDescuentos = 0.0; // Inicializar el total de descuentos
        $subTotalLibre = 0.0; // Inicializar el subtotal para productos libres

        // Función para generar un identificador único basado en la descripción y cantidad
        $generarIdentificadorUnico = function ($producto) {
            return md5($producto->nombre . $producto->pivot->cantidad);
        };

        $ultimoIdentificador = null; // Variable para almacenar el último identificador

        // Iterar sobre los productos de la venta
        foreach ($venta->productos as $producto) {
            // Generar el identificador único para el producto actual
            $identificadorActual = $generarIdentificadorUnico($producto);

            // Guardar la posición X e Y inicial antes de imprimir celdas horizontales
            $x = $pdf->GetX(); // Coordenada X actual
            $y = $pdf->GetY(); // Coordenada Y actual

            // Imprimir Cantidad
            $pdf->Cell(8, 4, $producto->pivot->cantidad, 0, 0, 'L');

            // Acceder a la presentación del producto
            $presentacion = $producto->presentacion; // Acceder al modelo Presentacion

            // Imprimir Unidad de Medida de la Presentación (característica si aplica)
            if ($presentacion && $presentacion->caracteristica) {
                $pdf->Cell(8, 3, $presentacion->caracteristica->nombre, 0, 0, 'L'); // Acceder al nombre de la característica
            } else {
                $pdf->Cell(8, 3, 'UND', 0, 0, 'L'); // Valor predeterminado si no hay presentación
            }

            // Guardar la posición X e Y antes de imprimir la descripción
            $pdf->SetXY($x + 18, $y); // Fija la posición para el MultiCell

            // Descripción utilizando MultiCell
            $descripcionCompleta = mb_convert_encoding($producto->nombre, 'ISO-8859-1', 'UTF-8');
            $pdf->SetFont('Arial', '', 8);
            $pdf->MultiCell(30, 3, $descripcionCompleta, 0, 'L');
            $pdf->SetFont('Arial', '', 8);

            // Obtener la altura después de la MultiCell
            $alto_producto = $pdf->GetY() - $y; // Obtener la altura de la descripción

            // Imprimir el precio unitario y subtotal (en las mismas coordenadas de X e Y que el ejemplo)
            $precioUnitario = $producto->pivot->precio_ventas;
            $total = $precioUnitario * $producto->pivot->cantidad;
            $totalFormateado = number_format($total, 2, '.', ',');

            // Posicionar para el precio unitario
            $pdf->SetXY($x + 54, $y); // Regresa la posición para las siguientes columnas
            $pdf->Cell(3, 3, number_format($precioUnitario, 2, '.', ','), 0, 0, 'C');

            // Posicionar para el subtotal
            $pdf->SetXY($x + 64, $y); // Posición para subtotal
            $pdf->Cell(6, 3, $totalFormateado, 0, 0, 'C');

            // Añadir un salto de línea según la altura de la descripción
            $pdf->Ln(max(3, $alto_producto)); // El salto es mínimo de 2, pero ajustable según la altura de la descripción

            // Acumular al subtotal
            $subTotalNormal += $total;

            // Acumular el descuento
            $totalDescuentos += $producto->pivot->descuento;

            // // Actualizar el último identificador
            // $ultimoIdentificador = $identificadorActual;
            $pdf->Ln(1);
        }

        // **Iteración sobre productos libres con lógica similar**
        foreach ($venta->productosLibres as $productoLibre) {
            // Generar el identificador único para el producto libre actual
            $identificadorActualLibre = md5($productoLibre->nombre . $productoLibre->cantidad);

            // Guardar la posición X e Y inicial antes de imprimir celdas horizontales
            $x = $pdf->GetX(); // Coordenada X actual
            $y = $pdf->GetY(); // Coordenada Y actual

            // Imprimir Cantidad
            $pdf->Cell(8, 3, $productoLibre->cantidad, 0, 0, 'L');

            // Imprimir Unidad de Medida (por defecto 'UND' para productos libres)
            $pdf->Cell(8, 3, 'UND', 0, 0, 'L');

            // Guardar la posición X e Y antes de imprimir la descripción
            $pdf->SetXY($x + 18, $y); // Fija la posición para el MultiCell

            // Descripción utilizando MultiCell
            $descripcionCompletaLibre = mb_convert_encoding($productoLibre->nombre, 'ISO-8859-1', 'UTF-8');
            $pdf->SetFont('Arial', '', 8);
            $pdf->MultiCell(30, 3, $descripcionCompletaLibre, 0, 'L');
            $pdf->SetFont('Arial', '', 8);

            // Obtener la altura después de la MultiCell
            $alto_productoLibre = $pdf->GetY() - $y; // Obtener la altura de la descripción

            // Imprimir el precio unitario y subtotal (en las mismas coordenadas de X e Y que el ejemplo)
            $precioUnitarioLibre = $productoLibre->precio_venta;
            $totalLibre = $precioUnitarioLibre * $productoLibre->cantidad;
            $totalFormateadoLibre = number_format($totalLibre, 2, '.', ',');

            // Posicionar para el precio unitario
            $pdf->SetXY($x + 54, $y); // Regresa la posición para las siguientes columnas
            $pdf->Cell(3, 3, number_format($precioUnitarioLibre, 2, '.', ','), 0, 0, 'C');

            // Posicionar para el subtotal
            $pdf->SetXY($x + 64, $y); // Posición para subtotal
            $pdf->Cell(6, 3, $totalFormateadoLibre, 0, 0, 'C');

            // Añadir un salto de línea según la altura de la descripción
            $pdf->Ln(max(3, $alto_productoLibre)); // El salto es mínimo de 2, pero ajustable según la altura de la descripción

            // Acumular al subtotal de productos libres
            $subTotalLibre += $totalLibre;

            // Acumular el descuento
            $totalDescuentos += $productoLibre->descuento;

            // Actualizar el último identificador
            // $ultimoIdentificador = $identificadorActualLibre;
            $pdf->Ln(1);
        }


        // Línea divisoria final
        $pdf->Cell(70, 0, str_repeat('-', 83), 0, 1, 'L');
        $pdf->Ln(2);

        // Retornar el subtotal calculado y el total de descuentos
        return [$subTotalNormal, $subTotalLibre, $totalDescuentos];
    }

    private function addTotalsToPDF($pdf, $subTotalNormal, $subTotalLibre, $impuesto, $totalDescuentos, $venta, $accountTicketData)
    {
        // Calcular el importe total
        $importeTotalNormMasLib = $subTotalNormal + $subTotalLibre;
        $importeTotalNormMasLibMasDescu = $importeTotalNormMasLib - $totalDescuentos;
        $importeTotalNormMasLibMasDescuMasImpue = $importeTotalNormMasLibMasDescu + $impuesto;
        $importeTotalFormateado = number_format($importeTotalNormMasLibMasDescuMasImpue, 2, '.', '');

        // Establecer la fuente y mostrar los distintos subtotales y el total
        $pdf->SetFont('Arial', 'B', 9);

        // OP. Descuento
        $pdf->Cell(48, 5, 'DESCUENTO: S/', 0, 0, 'R');
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(22, 5, number_format($totalDescuentos, 2, '.', ','), 0, 1, 'R');

        // OP. SUBTOTAL
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(48, 5, 'OP. SUBTOTAL: S/', 0, 0, 'R');
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(22, 5, number_format($importeTotalNormMasLibMasDescu, 2, '.', ','), 0, 1, 'R');

        // IGV
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(48, 5, 'IGV 0%: S/', 0, 0, 'R');
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(22, 5, number_format($impuesto, 2, '.', ','), 0, 1, 'R');

        // OP. IMPORTE TOTAL
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(48, 5, 'OP. IMPORTE TOTAL: S/', 0, 0, 'R');
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(22, 5, number_format($importeTotalFormateado, 2, '.', ','), 0, 1, 'R');

        // Dividir la parte entera y la parte decimal
        $partes = explode('.', $importeTotalFormateado);
        $parteEntera = intval($partes[0]);
        $parteDecimal = isset($partes[1]) ? intval($partes[1]) : 0;

        // Instanciar NumeroALetras
        $numeroALetras = new NumeroALetras();

        // Convertir la parte entera y añadir "SOLES"
        $importeTotalEnLetrasEntera = $numeroALetras->toWords($parteEntera) . ' SOLES';

        // Convertir la parte decimal (si es mayor que cero) y ajustar singular/plural
        if ($parteDecimal > 0) {
            $importeTotalEnLetrasDecimal = $numeroALetras->toWords($parteDecimal) . ' ' . ($parteDecimal == 1 ? 'CÉNTIMO' : 'CÉNTIMOS');
        } else {
            $importeTotalEnLetrasDecimal = '';
        }

        // Concatenar el resultado final
        if ($parteDecimal > 0) {
            $importeTotalEnLetras = $importeTotalEnLetrasEntera . ' CON ' . $importeTotalEnLetrasDecimal;
        } else {
            $importeTotalEnLetras = $importeTotalEnLetrasEntera;
        }

        // Mostrar el resultado en el PDF
        $pdf->SetFont('Arial', 'B', 7); // Cambiar a negrita
        $pdf->Write(4, iconv('UTF-8', 'ISO-8859-1', 'SON: '));

        $pdf->SetFont('Arial', '', 7); // Cambiar a fuente normal
        $pdf->Write(4, iconv('UTF-8', 'ISO-8859-1', strtoupper($importeTotalEnLetras)));

        // Mostrar Usuario de atención
        $pdf->Ln(4);
        $pdf->SetFont('Arial', 'B', 8); // Cambiar a negrita
        $pdf->Write(4, iconv('UTF-8', 'ISO-8859-1', 'N Caja: '));
        $pdf->Write(4, iconv('UTF-8', 'ISO-8859-1', strtoupper($venta->user_id)));

        $pdf->Ln(4);
        $pdf->Write(4, iconv('UTF-8', 'ISO-8859-1', 'Cajero: '));
        $pdf->SetFont('Arial', '', 7); // Cambiar a fuente normal
        $user = $venta->user; // Acceder al modelo usuario
        $pdf->Write(4, iconv('UTF-8', 'ISO-8859-1', strtoupper($user->name)));

        $pdf->Ln(4);
        $pdf->SetFont('Arial', 'B', 8); // Cambiar a negrita
        $pdf->Write(4, iconv('UTF-8', 'ISO-8859-1', 'Forma de Pago: '));
        $pdf->SetFont('Arial', '', 7); // Cambiar a fuente normal
        $pdf->Write(4, iconv('UTF-8', 'ISO-8859-1', strtoupper('contado')));

        $pdf->Ln(4);

        // Mover 16mm a la derecha y generar el QR code
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetX(6);

        $fpago = $venta->metodoPago; // Acceder al modelo método de pago
        if ($fpago->nombre == 'Efectivo') {
            $vuelto = $venta->monto_recibido - $importeTotalFormateado;
            $pdf->Write(4, iconv('UTF-8', 'ISO-8859-1', strtoupper($fpago->nombre . ':  S/.' . number_format($venta->monto_recibido, 2, '.', ',') .
                '  VUELTO:  S/.' . number_format($vuelto, 2, '.', ','))));
        } else {
            $pdf->Write(4, iconv('UTF-8', 'ISO-8859-1', strtoupper($fpago->nombre . ':  Num Ope. ' . $venta->numero_operacion)));
        }

        // Línea divisoria final
        $pdf->Ln(4);

        if (in_array($venta->comprobante->tipo_comprobante, ['BOLETA', 'FACTURA'])) {
            $pdf->Cell(70, 0, str_repeat('-', 83), 0, 1, 'L');
            // Obtener la URL específica del ticket usando el helper `url()` en Laravel
            $currentUrl = url('/venta/' . $venta->identificador_web . '/ticket');

            // Generar el QR con la URL obtenida usando Endroid QR Code v6.x (Uso correcto de enums)
            $builder = new Builder();

            $result = $builder->build(
                writer: new PngWriter(),
                data: $currentUrl,
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: ErrorCorrectionLevel::Low, // Uso correcto de la enum
                size: 150, // Tamaño del QR en píxeles
                margin: 1, // Margen en píxeles
                roundBlockSizeMode: RoundBlockSizeMode::Margin // Uso correcto de la enum
            );

            // Guardar el QR en un archivo temporal
            $tempQRPath = storage_path('app/public/qr_temp.png');
            $result->saveToFile($tempQRPath);

            // Agregar el QR al PDF
            $pdf->Image($tempQRPath, 23, $pdf->GetY() + 1, 35, 35); // Ajusta posición (x, y) y tamaño
            unlink($tempQRPath); // Elimina el archivo temporal después de insertarlo

            $pdf->Ln(34);

            // Cambiar a un tamaño de fuente válido
            $pdf->SetFont('Arial', '', 8); // Cambiar a un tamaño adecuado, como 8
            $pdf->Cell(56, 5, iconv('UTF-8', 'ISO-8859-1', 'Representación impresa de la'), 0, 0, 'R');
            $pdf->Ln(4);
            $pdf->MultiCell(68, 3, $venta->comprobante->tipo_comprobante . ' ELECTRONICA', 0, 'C');

            // Hash del comprobante
            if (isset($venta->emisionComprobante) && isset($venta->emisionComprobante->hash_cpe)) {
                $hashv = $venta->emisionComprobante->hash_cpe;
                $pdf->MultiCell(70, 4, iconv('UTF-8', 'ISO-8859-1', $hashv), 0, 'C');
            } else {
                $pdf->MultiCell(70, 4, 'Hash no disponible', 0, 'C');
            }
        } else {
        }

        // Agregar contenido de aviso
        $descripcion_aviso = $accountTicketData['descripcion_aviso'];
        // Reemplazar cada punto seguido de un espacio con un punto, un salto de línea y mantener el espacio
        $descripcion_aviso = str_replace('. ', ".\n", $descripcion_aviso);
        // Ahora usamos MultiCell para generar las líneas automáticamente
        $pdf->MultiCell(70, 3, iconv('UTF-8', 'ISO-8859-1', $descripcion_aviso), 0, 'C');

        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'B', 8); // Cambiar a un tamaño adecuado, como 8
        // Despedida
        $despedida = $accountTicketData['despedida'];
        // Reemplazar cada punto seguido de un espacio con un punto, un salto de línea y mantener el espacio
        $despedida = str_replace('. ', ".\n", $despedida);
        // Ahora usamos MultiCell para generar las líneas automáticamente
        $pdf->MultiCell(70, 4, iconv('UTF-8', 'ISO-8859-1', $despedida), 0, 'C');
    }
}
