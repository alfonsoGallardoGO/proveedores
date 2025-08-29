<!DOCTYPE html>
<html>

<head>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            background-color: white;
            color: black;
            text-align: center;
            font-size: 30px;
            vertical-align: top;
        }

        .td-1 {
            width: 10%;
        }

        .td-2-3 {
            width: 50%;
            padding: 0 10px;
        }

        .texto-empresa p {
            text-transform: uppercase;
            text-align: left;
            line-height: 1.2;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            font-weight: normal;
            margin: 0;
        }

        .texto-orden {
            text-align: right;
            vertical-align: top;
            padding-right: 20px;
        }

        .texto-orden h1 {
            font-size: 40px;
            margin: 0;
        }

        .texto-orden .nOrden {
            font-size: 25px;
        }

        .texto-orden p {
            font-size: 15px;
            margin: 0;
            line-height: 1.5;
        }

        .td-logo {
            width: auto;
            vertical-align: middle;
            text-align: left;
            padding: 10px;
        }

        .td-logo img {
            width: 80px;
            height: auto;
            display: block;
            margin: 0 auto;
            border-radius: 50%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .td-1 img {
            width: 150px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .td-total {
            background-color: #f1f1f1;
            padding: 15px;
            vertical-align: top;
            position: relative;
            text-align: right;
        }

        .td-proveedor {
            text-align: left;
        }

        .etiqueta {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .proveedor-detalle {
            font-size: 12px;
            margin: 0;
            line-height: 1.2;
        }

        .etiqueta-total {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: left;
            position: absolute;
            top: 5px;
            left: 5px;
            margin: 0;
        }

        .monto {
            font-size: 40px;
            font-weight: normal;
            margin: 0;
            margin-top: 20px;
        }

        .fecha-hora {
            font-size: 12px;
            margin: 0;
            margin-top: 10px;
        }

        .tabla-items th,
        .tabla-items td {
            font-family: Arial, sans-serif;
            font-size: 10px;
            padding: 8px 10px;
            text-align: left;
            vertical-align: middle;
        }

        .tabla-items th {
            background-color: #e0e0e0;
            font-weight: bold;
        }

        .tabla-items .fila-gris {
            background-color: #f2f2f2;
        }

        .tabla-items .total-label {
            text-align: right;
            font-weight: bold;
        }

        .tabla-items .total-value {
            text-align: right;
        }

        .td-ubicacion {
            width: 65%;
        }

        .td-ubicacion p,
        .td-status p {
            text-align: left;
            font-size: 12px;
        }

        .td-status {
            width: 35%;
        }

        .anexo-a-container {
            border: 1px solid #000;
            margin-top: 20px;
            font-family: Arial, Helvetica, sans-serif;
            text-align: left;
        }

        .anexo-a-container h2 {
            font-size: 10px;
            margin-top: 0;
            margin-bottom: 10px;
            text-align: left;
        }

        .anexo-a-item p {
            margin: 0 0 5px 0;
            line-height: 1.2;
            font-size: 9px;
        }

        .anexo-a-item p strong {
            font-weight: bold;
        }

        #footer {
            position: fixed;
            bottom: 0px;
            left: 0;
            right: 0;
            height: 60px;
            text-align: right;
            padding-right: 20px;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #000;
        }

        #footer .barcode-container {
            position: absolute;
            left: 20px;
            top: 5px;
            text-align: center;
        }

        #footer .barcode-container img {
            display: block;
            margin-bottom: 5px;
        }

        #page-number-container {
            position: fixed;
            bottom: 10px;
            right: 20px;
            font-size: 12px;
            color: #000;
            font-family: Arial, Helvetica, sans-serif;
        }

        .final-barcode-section {
            display: none;
        }

        body {
            margin-bottom: 70px;
        }
    </style>
</head>

<body>
    <script type="text/php">
        if (isset($pdf)) {
            // Obtiene las dimensiones de la página y los márgenes
            $w = $pdf->get_width();
            $h = $pdf->get_height();
            $font = $fontMetrics->get_font("Arial");
            $text = "Página {PAGE_NUM} de {PAGE_COUNT}";
            $text_width = $fontMetrics->getTextWidth($text, $font, 12);

            // Coordenadas para el pie de página
            $x = ($w - $text_width) / 2; // Centrado horizontal
            $y = $h - 20; // 20px desde la parte inferior

            // Agrega el número de página
            $pdf->text($x, $y, $text, $font, 12);
        }
    </script>
    <div class="main-content">
        <table>
            <tr>
                <td class="td-logo">
                    <img src="data:image/png;base64,{{ $logoBase64 }}" alt="Logo">
                </td>
                <td class="td-direccion">
                    <div class="texto-empresa">
                        <p>SERVICIOS CORPORATIVOS</p>
                        <p>QUATUM DE OCCIDENTE SC</p>
                        <p>BELISARIO DOMINGUEZ 30</p>
                        <p>MORELIA CENTRO, MORELIA</p>
                        <p>58000 MORELIA, MICH</p>
                        <p>México</p>
                    </div>
                </td>
                <td class="td-orden">
                    <div class="texto-orden">
                        <h1>Orden de compra</h1>
                        <p class="nOrden">#{{ $orders->purchase_order }}</p>
                        <p>{{ $orders->created_at?->format('d/m/Y') }}</p>
                    </div>
                </td>
            </tr>
        </table>
        <br>
        <table>
            <tr>
                <td class="td-proveedor">
                    <p class="etiqueta">Proveedor</p>
                    <p class="proveedor-detalle">RFC: {{ $supplier->tax }}</p>
                    <p class="proveedor-detalle">{{ $supplier->company_name }}</p>
                    <p class="proveedor-detalle">{{ $supplier->address }}</p>
                    <p class="proveedor-detalle">{{ $supplier->zip_code }}</p>
                </td>
                <td class="td-total">
                    <p class="etiqueta-total">TOTAL</p>
                    <p class="monto">${{ number_format($totalItems, 2) }}</p>
                    <p class="fecha-hora">{{ $orders->created_at?->format('d/m/Y H:i:s') }}</p>
                </td>
            </tr>
        </table>
        <table class="tabla-items">
            <thead>
                <tr>
                    <th class="fila-gris">Fecha límite de recepción</th>
                    <th class="fila-gris">N° de proveedor</th>
                    <th class="fila-gris">Moneda</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $orders->reception_deadline?->format('d/m/Y') }}</td>
                    <td>{{ $supplier->external_id }}</td>
                    <td>{{ $supplier->currency }}</td>
                </tr>
                <tr>
                    <td colspan="3" style="padding: 0; border: none;"></td>
                </tr>
            </tbody>
            <thead>
                <tr>
                    <th class="fila-gris">Descripción</th>
                    <th class="fila-gris">Unidad</th>
                    <th class="fila-gris" style="text-align: right;">Importe</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->unit }}</td>
                        <td class="total-value">${{ number_format($item->amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="total-label">Total</td>
                    <td class="total-value">${{ number_format($totalItems, 2) }}</td>
                </tr>
            </tfoot>
        </table>
        <br>
        <table>
            <tr>
                <td class="td-ubicacion">
                    <p>Ubicación:</p>
                    <p>{{ $items->first()->location }}</p>
                </td>
                <td class="td-status">
                    <p>Estatus</p>
                    <p>{{ $orders->status }}</p>
                </td>
            </tr>
        </table>
        <br>
        <table class="anexo-a-container">
            <tr>
                <td style="padding: 5px; text-align: left; vertical-align: top; font-size: 8px;">
                    <h2 style="font-size: 10px; margin-top: 0; margin-bottom: 8px; text-align: left;">ANEXO A</h2>
                    <div class="anexo-a-item">
                        <p><strong>Aceptación de la Orden de Compra</strong></p>
                        <p>• "El proveedor acepta esta orden de compra al confirmar su recepción o al iniciar la entrega
                            de los bienes/servicios solicitados."</p>
                    </div>
                    <div class="anexo-a-item">
                        <p><strong>Condiciones de Pago</strong></p>
                        <p>• "El pago se realizará conforme a los días de crédito o negociación pactada entre las
                            partes."</p>
                    </div>
                    <div class="anexo-a-item">
                        <p><strong>Cumplimiento de Especificaciones</strong></p>
                        <p>• "Los bienes/servicios deben cumplir estrictamente con las especificaciones detalladas en
                            esta orden. La no conformidad puede resultar en rechazo total o parcial de la entrega."</p>
                    </div>
                    <div class="anexo-a-item">
                        <p><strong>Garantías y Devoluciones</strong></p>
                        <p>• "El proveedor garantiza que los productos están libres de defectos de fabricación. En caso
                            de defectos, el proveedor se compromete a reparar, reemplazar o reembolsar los productos
                            defectuosos sin costo adicional."</p>
                    </div>
                    <div class="anexo-a-item">
                        <p><strong>Condiciones de Cancelación</strong></p>
                        <p>• "El comprador se reserva el derecho de cancelar esta orden de compra sin penalización en
                            caso de incumplimiento por parte del proveedor."</p>
                    </div>
                    <div class="anexo-a-item">
                        <p><strong>Propiedad Intelectual y Confidencialidad</strong></p>
                        <p>• "El proveedor acuerda mantener la confidencialidad de toda la información proporcionada en
                            relación con esta orden de compra y no utilizarla para fines no autorizados."</p>
                    </div>
                    <div class="anexo-a-item">
                        <p><strong>Fuerza Mayor</strong></p>
                        <p>• "Ambas partes estarán exentas de responsabilidad por el incumplimiento de esta orden debido
                            a causas de fuerza mayor, como desastres naturales, conflictos laborales o cualquier evento
                            fuera de su control razonable."</p>
                    </div>
                    <div class="anexo-a-item">
                        <p><strong>Almacén de Recepción o Lugar de Entrega</strong></p>
                        <p>• "Los bienes deberán ser entregados en la dirección proporcionada por el comprador en los
                            horarios siguientes de 9:00 am a 1:30 pm y de 3:30 pm a 4:30 pm de lunes a viernes y los
                            sábados de 9:00 am a 1:00 pm</p>
                        <p>• Documentación entregar con la mercancía</p>
                        <p>Orden de compra, Factura de los artículos que se están entregando, certificados de calidad
                            (en caso de que aplique). En caso de no entregar con esta documentación, no se realizará el
                            pago correspondiente.</p>
                    </div>
                    <div class="anexo-a-item">
                        <p><strong>Condiciones de Cancelación</strong></p>
                        <p>• "El comprador se reserva el derecho de cancelar esta orden de compra sin penalización en
                            caso de incumplimiento de algunos de los puntos anteriores por parte del proveedor."</p>
                    </div>
                </td>
            </tr>
        </table>
        <div class="final-barcode-section">
            {!! $barcode !!}
            <div class="barcode-text">
                {{ $orders->purchase_order }}
            </div>
        </div>
    </div>

    <div id="footer">
        <div class="barcode-container">
            {!! $barcode !!}
            <div class="barcode-text">
                {{ $orders->purchase_order }}
            </div>
        </div>
    </div>
</body>

</html>

{{-- <!DOCTYPE html>
<html>

<head>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            background-color: white;
            color: black;
            text-align: center;
            font-size: 30px;
            vertical-align: top;
        }

        .td-1 {
            width: 10%;
        }

        .td-2-3 {
            width: 50%;
            padding: 0 10px;
        }

        .texto-empresa p {
            text-transform: uppercase;
            text-align: left;
            line-height: 1.2;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            font-weight: normal;
            margin: 0;
        }

        .texto-orden {
            text-align: right;
            vertical-align: top;
            padding-right: 20px;
        }

        .texto-orden h1 {
            font-size: 40px;
            margin: 0;
        }

        .texto-orden .nOrden {
            font-size: 25px;
        }

        .texto-orden p {
            font-size: 15px;
            margin: 0;
            line-height: 1.5;
        }

        .logo {
            width: 150px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .td-logo {
            width: auto;
            vertical-align: middle;
            text-align: left;
            padding: 10px;
        }

        .td-logo img {
            width: 80px;
            height: auto;
            display: block;
            margin: 0 auto;
            border-radius: 50%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .td-1 img {
            width: 150px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .td-total {
            background-color: #f1f1f1;
            padding: 15px;
            vertical-align: top;
            position: relative;
            text-align: right;
        }

        .td-proveedor {
            text-align: left;
        }

        .etiqueta {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .proveedor-detalle {
            font-size: 12px;
            margin: 0;
            line-height: 1.2;
        }

        .etiqueta-total {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: left;
            position: absolute;
            top: 5px;
            left: 5px;
            margin: 0;
        }

        .monto {
            font-size: 40px;
            font-weight: normal;
            margin: 0;
            margin-top: 20px;
        }

        .fecha-hora {
            font-size: 12px;
            margin: 0;
            margin-top: 10px;
        }

        .tabla-items th,
        .tabla-items td {
            font-family: Arial, sans-serif;
            font-size: 10px;
            padding: 8px 10px;
            text-align: left;
            vertical-align: middle;
            /* border-bottom: 1px solid #ddd; */
        }

        .tabla-items th {
            background-color: #e0e0e0;
            font-weight: bold;
        }

        .tabla-items .fila-gris {
            background-color: #f2f2f2;
        }

        .tabla-items .descripcion {
            font-weight: bold;
        }

        .tabla-items .total-label {
            text-align: right;
            font-weight: bold;
        }

        .tabla-items .total-value {
            text-align: right;
        }

        .td-ubicacion {
            width: 65%;
        }

        .td-ubicacion p {
            text-align: left;
            font-size: 12px;
        }

        .td-status {
            width: 35%;
        }

        .td-status p {
            text-align: left;
            font-size: 12px;
        }

        .anexo-a-container {
            border: 1px solid #000;
            margin-top: 20px;
            font-family: Arial, Helvetica, sans-serif;
            text-align: left;
        }

        .anexo-a-container h2 {
            font-size: 10px;
            margin-top: 0;
            margin-bottom: 10px;
            text-align: left;
        }

        .anexo-a-item p {
            margin: 0 0 5px 0;
            line-height: 1.2;
            font-size: 9px;
        }

        .anexo-a-item p strong {
            font-weight: bold;
        }

        .anexo-a-item ul {
            margin-top: 5px;
            padding-left: 20px;
        }

        .anexo-a-item ul li {
            margin-bottom: 1px;
        }

        .footer {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            margin-top: auto;
        }

        .barcode-container {
            width: 250px;
            text-align: center;
        }

        .barcode-text {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            margin-top: 5px;
            color: #000;
            text-align: center;
        }

        @page {
            margin-bottom: 70px;

            @bottom-center {
                content: element(footer);
            }
        }

        #page-footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            padding: 10px 20px;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
        }

        .page-footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-number {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            text-align: right;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }

        .main-content {
            flex: 1;
        }
    </style>
</head>

<body>
    <div class="main-content">
        <table>
            <tr>
                <td class="td-logo">
                    <img src="data:image/png;base64,{{ $logoBase64 }}" alt="Logo">
                </td>
                <td class="td-direccion">
                    <div class="texto-empresa">
                        <p>SERVICIOS CORPORATIVOS</p>
                        <p>QUATUM DE OCCIDENTE SC</p>
                        <p>BELISARIO DOMINGUEZ 30</p>
                        <p>MORELIA CENTRO, MORELIA</p>
                        <p>58000 MORELIA, MICH</p>
                        <p>México</p>
                    </div>
                </td>
                <td class="td-orden">
                    <div class="texto-orden">
                        <h1>Orden de compra</h1>
                        <p class="nOrden">#{{ $orders->purchase_order }}</p>
                        <p>{{ $orders->created_at?->format('d/m/Y') }}</p>
                    </div>
                </td>
            </tr>
        </table>

        <br>
        <table>
            <tr>
                <td class="td-proveedor">
                    <p class="etiqueta">Proveedor</p>
                    <p class="proveedor-detalle">RFC: {{ $supplier->tax }}</p>
                    <p class="proveedor-detalle">{{ $supplier->company_name }}</p>
                    <p class="proveedor-detalle">{{ $supplier->address }}</p>
                    <p class="proveedor-detalle">{{ $supplier->zip_code }}
                </td>
                <td class="td-total">
                    <p class="etiqueta-total">TOTAL</p>
                    <p class="monto">${{ number_format($totalItems, 2) }}</p>
                    <p class="fecha-hora">{{ $orders->created_at?->format('d/m/Y H:i:s') }}</p>
                </td>
            </tr>
        </table>

        <table class="tabla-items">
            <thead>
                <tr>
                    <th class="fila-gris">Fecha límite de recepción</th>
                    <th class="fila-gris">N° de proveedor</th>
                    <th class="fila-gris">Moneda</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $orders->reception_deadline?->format('d/m/Y') }}</td>
                    <td>{{ $supplier->external_id }}</td>
                    <td>{{ $supplier->currency }}</td>
                </tr>
                <tr>
                    <td colspan="3" style="padding: 0; border: none;"></td>
                </tr>
            </tbody>
            <thead>
                <tr>
                    <th class="fila-gris">Descripción</th>
                    <th class="fila-gris">Unidad</th>
                    <th class="fila-gris" style="text-align: right;">Importe</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td>{{ $item->unit }}</td>
                        <td class="total-value">${{ number_format($item->amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="total-label">Total</td>
                    <td class="total-value">${{ number_format($orders->totalItems, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <br>
        <table>
            <tr>
                <td class="td-ubicacion">
                    <p>Ubicación:</p>
                    <p>{{ $items->first()->location }}</p>
                </td>
                <td class="td-status">
                    <p>Estatus</p>
                    <p>{{ $orders->status }}</p>
                </td>
            </tr>
        </table>
        <br>
        <table class="anexo-a-container">
            <tr>
                <td style="padding: 5px; text-align: left; vertical-align: top; font-size: 8px;">
                    <h2 style="font-size: 10px; margin-top: 0; margin-bottom: 8px; text-align: left;">ANEXO A</h2>
                    <div class="anexo-a-item">
                        <p><strong>Aceptación de la Orden de Compra</strong></p>
                        <p>• "El proveedor acepta esta orden de compra al confirmar su recepción o al iniciar la entrega
                            de
                            los bienes/servicios solicitados."</p>
                    </div>
                    <div class="anexo-a-item">
                        <p><strong>Condiciones de Pago</strong></p>
                        <p>• "El pago se realizará conforme a los días de crédito o negociación pactada entre las
                            partes."
                        </p>
                    </div>
                    <div class="anexo-a-item">
                        <p><strong>Cumplimiento de Especificaciones</strong></p>
                        <p>• "Los bienes/servicios deben cumplir estrictamente con las especificaciones detalladas en
                            esta
                            orden. La no conformidad puede resultar en rechazo total o parcial de la entrega."</p>
                    </div>
                    <div class="anexo-a-item">
                        <p><strong>Garantías y Devoluciones</strong></p>
                        <p>• "El proveedor garantiza que los productos están libres de defectos de fabricación. En caso
                            de
                            defectos, el proveedor se compromete a reparar, reemplazar o reembolsar los productos
                            defectuosos sin costo adicional."</p>
                    </div>
                    <div class="anexo-a-item">
                        <p><strong>Condiciones de Cancelación</strong></p>
                        <p>• "El comprador se reserva el derecho de cancelar esta orden de compra sin penalización en
                            caso
                            de incumplimiento por parte del proveedor."</p>
                    </div>
                    <div class="anexo-a-item">
                        <p><strong>Propiedad Intelectual y Confidencialidad</strong></p>
                        <p>• "El proveedor acuerda mantener la confidencialidad de toda la información proporcionada en
                            relación con esta orden de compra y no utilizarla para fines no autorizados."</p>
                    </div>
                    <div class="anexo-a-item">
                        <p><strong>Fuerza Mayor</strong></p>
                        <p>• "Ambas partes estarán exentas de responsabilidad por el incumplimiento de esta orden debido
                            a
                            causas de fuerza mayor, como desastres naturales, conflictos laborales o cualquier evento
                            fuera
                            de su control razonable."</p>
                    </div>
                    <div class="anexo-a-item">
                        <p><strong>Almacén de Recepción o Lugar de Entrega</strong></p>
                        <p>• "Los bienes deberán ser entregados en la dirección proporcionada por el comprador en los
                            horarios siguientes de 9:00 am a 1:30 pm y de 3:30 pm a 4:30 pm de lunes a viernes y los
                            sábados
                            de 9:00 am a 1:00 pm</p>
                        <p>• Documentación entregar con la mercancía</p>
                        <p>Orden de compra, Factura de los artículos que se están entregando, certificados de calidad
                            (en
                            caso de que aplique). En caso de no entregar con esta documentación, no se realizará el pago
                            correspondiente.</p>
                    </div>
                    <div class="anexo-a-item">
                        <p><strong>Condiciones de Cancelación</strong></p>
                        <p>• "El comprador se reserva el derecho de cancelar esta orden de compra sin penalización en
                            caso
                            de incumplimiento de algunos de los puntos anteriores por parte del proveedor."</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div id="page-footer">
        <div class="page-footer-content">
            <div class="barcode-container">
                {!! $barcode !!}
                <div class="barcode-text">
                    {{ $orders->purchase_order }}
                </div>
            </div>
            <div class="page-number">
            </div>
        </div>
    </div>
</body>

</html> --}}


{{-- <!DOCTYPE html>
<html>

<head>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            background-color: white;
            color: black;
            text-align: center;
            font-size: 30px;
            vertical-align: top;
        }

        .td-1 {
            width: 10%;
        }

        .td-2-3 {
            width: 50%;
            padding: 0 10px;
        }

        .texto-empresa p {
            text-transform: uppercase;
            text-align: left;
            line-height: 1.2;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            font-weight: normal;
            margin: 0;
        }

        .texto-orden {
            text-align: right;
            vertical-align: top;
            padding-right: 20px;
        }

        .texto-orden h1 {
            font-size: 40px;
            margin: 0;
        }

        .texto-orden .nOrden {
            font-size: 25px;
        }

        .texto-orden p {
            font-size: 15px;
            margin: 0;
            line-height: 1.5;
        }

        .logo {
            width: 150px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .td-logo {
            width: auto;
            vertical-align: middle;
            text-align: left;
            padding: 10px;
        }

        .td-logo img {
            width: 80px;
            height: auto;
            display: block;
            margin: 0 auto;
            border-radius: 50%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .td-1 img {
            width: 150px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .td-total {
            background-color: #f1f1f1;
            padding: 15px;
            vertical-align: top;
            position: relative;
            text-align: right;
        }

        .td-proveedor {
            text-align: left;
        }

        .etiqueta {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .proveedor-detalle {
            font-size: 12px;
            margin: 0;
            line-height: 1.2;
        }

        .etiqueta-total {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: left;
            position: absolute;
            top: 5px;
            left: 5px;
            margin: 0;
        }

        .monto {
            font-size: 40px;
            font-weight: normal;
            margin: 0;
            margin-top: 20px;
        }

        .fecha-hora {
            font-size: 12px;
            margin: 0;
            margin-top: 10px;
        }

        .tabla-items th,
        .tabla-items td {
            font-family: Arial, sans-serif;
            font-size: 10px;
            padding: 8px 10px;
            text-align: left;
            vertical-align: middle;
            /* border-bottom: 1px solid #ddd; */
        }

        .tabla-items th {
            background-color: #e0e0e0;
            font-weight: bold;
        }

        .tabla-items .fila-gris {
            background-color: #f2f2f2;
        }

        .tabla-items .descripcion {
            font-weight: bold;
        }

        .tabla-items .total-label {
            text-align: right;
            font-weight: bold;
        }

        .tabla-items .total-value {
            text-align: right;
        }

        .td-ubicacion {
            width: 65%;
        }

        .td-ubicacion p {
            text-align: left;
            font-size: 12px;
        }

        .td-status {
            width: 35%;
        }

        .td-status p {
            text-align: left;
            font-size: 12px;
        }

        .anexo-a-container {
            border: 1px solid #000;
            margin-top: 20px;
            font-family: Arial, Helvetica, sans-serif;
            text-align: left;
        }

        .anexo-a-container h2 {
            font-size: 10px;
            margin-top: 0;
            margin-bottom: 10px;
            text-align: left;
        }

        .anexo-a-item p {
            margin: 0 0 5px 0;
            line-height: 1.2;
            font-size: 9px;
        }

        .anexo-a-item p strong {
            font-weight: bold;
        }

        .anexo-a-item ul {
            margin-top: 5px;
            padding-left: 20px;
        }

        .anexo-a-item ul li {
            margin-bottom: 1px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding: 10px 20px;
        }

        .barcode-container {
            width: 250px;
            text-align: center;
        }

        .barcode-text {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            margin-top: 5px;
            color: #000;
            text-align: center;
        }

        .page-number {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            text-align: right;
        }
    </style>
</head>

<body>

    <table>
        <tr>
            <td class="td-logo">
                <img src="data:image/png;base64,{{ $logoBase64 }}" alt="Logo">
            </td>
            <td class="td-direccion">
                <div class="texto-empresa">
                    <p>SERVICIOS CORPORATIVOS</p>
                    <p>QUATUM DE OCCIDENTE SC</p>
                    <p>BELISARIO DOMINGUEZ 30</p>
                    <p>MORELIA CENTRO, MORELIA</p>
                    <p>58000 MORELIA, MICH</p>
                    <p>México</p>
                </div>
            </td>
            <td class="td-orden">
                <div class="texto-orden">
                    <h1>Orden de compra</h1>
                    <p class="nOrden">#OCSCQ0212</p>
                    <p>21/08/2025</p>
                </div>
            </td>
        </tr>
    </table>

    <br>
    <table>
        <tr>
            <td class="td-proveedor">
                <p class="etiqueta">Proveedor</p>
                <p class="proveedor-detalle">RFC: FIRA860812RX3</p>
                <p class="proveedor-detalle">ABRAHAM FIGUEROA REYES</p>
                <p class="proveedor-detalle">NA 0</p>
                <p class="proveedor-detalle">VENUSTIANO CARRANZA</p>
                <p class="proveedor-detalle">15000 , CDMX</p>
                <p class="proveedor-detalle">México</p>
            </td>
            <td class="td-total">
                <p class="etiqueta-total">TOTAL</p>
                <p class="monto">$1,432.60</p>
                <p class="fecha-hora">21/08/2025 10:53:00</p>
            </td>
        </tr>
    </table>

    <table class="tabla-items">
        <thead>
            <tr>
                <th class="fila-gris">Fecha límite de recepción</th>
                <th class="fila-gris">N° de proveedor</th>
                <th class="fila-gris">Moneda</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td></td>
                <td>2110-0001</td>
                <td>Mexican Peso</td>
            </tr>
            <tr>
                <td colspan="3" style="padding: 0; border: none;"></td>
            </tr>
        </tbody>
        <thead>
            <tr>
                <th class="fila-gris">Categoría</th>
                <th class="fila-gris">Descripción</th>
                <th class="fila-gris" style="text-align: right;">Importe</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>ARTICULOS PARA TALLER-G</td>
                <td class="descripcion">AMORTIGUADORES PA LA TIGUAN</td>
                <td class="total-value">$1,235.00</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="total-label">Total</td>
                <td class="total-value">$1,432.60</td>
            </tr>
        </tfoot>
    </table>

    <br>
    <table>
        <tr>
            <td class="td-ubicacion">
                <p>Ubicación:</p>
                <p>CORPORATIVO</p>
            </td>
            <td class="td-status">
                <p>Estatus</p>
                <p>Aprobación pendiente</p>
            </td>
        </tr>
    </table>
    <br>
    <table class="anexo-a-container">
        <tr>
            <td style="padding: 5px; text-align: left; vertical-align: top; font-size: 8px;">
                <h2 style="font-size: 10px; margin-top: 0; margin-bottom: 8px; text-align: left;">ANEXO A</h2>
                <div class="anexo-a-item">
                    <p><strong>Aceptación de la Orden de Compra</strong></p>
                    <p>• "El proveedor acepta esta orden de compra al confirmar su recepción o al iniciar la entrega de
                        los bienes/servicios solicitados."</p>
                </div>
                <div class="anexo-a-item">
                    <p><strong>Condiciones de Pago</strong></p>
                    <p>• "El pago se realizará conforme a los días de crédito o negociación pactada entre las partes."
                    </p>
                </div>
                <div class="anexo-a-item">
                    <p><strong>Cumplimiento de Especificaciones</strong></p>
                    <p>• "Los bienes/servicios deben cumplir estrictamente con las especificaciones detalladas en esta
                        orden. La no conformidad puede resultar en rechazo total o parcial de la entrega."</p>
                </div>
                <div class="anexo-a-item">
                    <p><strong>Garantías y Devoluciones</strong></p>
                    <p>• "El proveedor garantiza que los productos están libres de defectos de fabricación. En caso de
                        defectos, el proveedor se compromete a reparar, reemplazar o reembolsar los productos
                        defectuosos sin costo adicional."</p>
                </div>
                <div class="anexo-a-item">
                    <p><strong>Condiciones de Cancelación</strong></p>
                    <p>• "El comprador se reserva el derecho de cancelar esta orden de compra sin penalización en caso
                        de incumplimiento por parte del proveedor."</p>
                </div>
                <div class="anexo-a-item">
                    <p><strong>Propiedad Intelectual y Confidencialidad</strong></p>
                    <p>• "El proveedor acuerda mantener la confidencialidad de toda la información proporcionada en
                        relación con esta orden de compra y no utilizarla para fines no autorizados."</p>
                </div>
                <div class="anexo-a-item">
                    <p><strong>Fuerza Mayor</strong></p>
                    <p>• "Ambas partes estarán exentas de responsabilidad por el incumplimiento de esta orden debido a
                        causas de fuerza mayor, como desastres naturales, conflictos laborales o cualquier evento fuera
                        de su control razonable."</p>
                </div>
                <div class="anexo-a-item">
                    <p><strong>Almacén de Recepción o Lugar de Entrega</strong></p>
                    <p>• "Los bienes deberán ser entregados en la dirección proporcionada por el comprador en los
                        horarios siguientes de 9:00 am a 1:30 pm y de 3:30 pm a 4:30 pm de lunes a viernes y los sábados
                        de 9:00 am a 1:00 pm</p>
                    <p>• Documentación entregar con la mercancía</p>
                    <p>Orden de compra, Factura de los artículos que se están entregando, certificados de calidad (en
                        caso de que aplique). En caso de no entregar con esta documentación, no se realizará el pago
                        correspondiente.</p>
                </div>
                <div class="anexo-a-item">
                    <p><strong>Condiciones de Cancelación</strong></p>
                    <p>• "El comprador se reserva el derecho de cancelar esta orden de compra sin penalización en caso
                        de incumplimiento de algunos de los puntos anteriores por parte del proveedor."</p>
                </div>
            </td>
        </tr>
    </table>
    <div class="footer">
        <div class="barcode-container">
            {!! $barcode !!}
            <div class="barcode-text">
                {{ $orderNumber }}
            </div>
        </div>
        <div class="page-number">
            1 of 1
        </div>
    </div>
</body>

</html> --}}
