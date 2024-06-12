<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TMH</title>

    <style>
        ::root {
            --title-color: rgb(0, 0, 0);
            --icon-color: rgb(60, 182, 232);
            --text-color: rgb(142, 142, 142);

        }

        body::-webkit-scrollbar {
            width: 8px;
            /* width of the entire scrollbar */
        }


        body::-webkit-scrollbar-thumb {
            background-color: rgb(182, 182, 182);
            /* color of the scroll thumb */
            border: 2px solid #e1e1e1;
            /* creates padding around scroll thumb */
        }

        .text-color {
            color: rgb(142, 142, 142) !important;
        }

        body {
            background-color: #fff;
            /* background-image: url('static/img/fondo.png');*/
            background-repeat: no-repeat;
            background-position: center;
            height: 100vh;
            margin: 0;
            padding: 0;
            box-sizing: none;
            font-size: 20px;
            max-width: 840px;
            margin: auto;
            font-family: Arial, Helvetica, sans-serif;
        }

        .main-title {
            font-size: 1em;
            color: var(--title-color);
        }

        .justify-evenly {
            justify-content: space-evenly;
        }

        .p-1 {
            padding: 0.25em;
        }

        .pt-1 {
            padding-top: 0.25em;
        }

        .flex-grow {
            flex-grow: 1;
        }

        .pt-2 {
            padding-top: 0.75em;
        }

        .pt-3 {
            padding-top: 1.25em;
        }

        .pt-4 {
            padding-top: 1.75em;
        }

        .mt-1 {
            margin-top: 0.25em;
        }

        .mt-2 {
            margin-top: 0.75em;
        }

        .mt-3 {
            margin-top: 1.25em;
        }

        .mt-4 {
            margin-top: 1.75em;
        }

        .p-2 {
            padding: 0.75em;
        }

        .p-3 {
            padding: 1.25em;
        }

        .gap-1 {
            gap: 0.5em;
        }

        .gap-2 {
            gap: 1em;
        }

        .gap-3 {
            gap: 1.5em;
        }

        .title {
            color: #3a3a3a;
            font-size: 0.7em;
            font-weight: bold;
        }

        .flex {
            display: flex;
        }

        .flex-row {
            flex-direction: row;
        }

        .flex-col {
            flex-direction: column;
        }

        .font-bold {
            font-weight: 700;
        }

        .w-full {
            width: 100%;
        }

        .justify-between {
            justify-content: space-between;
        }

        .items-center {
            align-items: center;
        }

        .items-end {
            align-items: end;
        }

        .justify-center {
            justify-content: center;
        }


        ::root {
            --title-color: rgb(0, 0, 0);
            --icon-color: rgb(60, 182, 232);
            --text-color: rgb(142, 142, 142);

        }

        .input-wrapper {
            position: relative;
            width: 100%;
        }

        .icon-color {
            color: rgb(60, 182, 232);
        }

        .table-item {
            color: rgb(142, 142, 142);
        }

        .table-text {
            font-size: 0.7em;
        }

        .table-bg {
            background: rgb(229, 229, 229);
        }

        article .table-text>div {
            padding: 6px 0;
        }

        .cant-col>div {
            padding-left: 10px !important;
        }

        .input {
            box-sizing: border-box;
            padding: 0.6em 15px;
            font-size: 0.7em;
            width: 100%;
            border: 1px solid rgb(142, 142, 142) !important;
            outline: none;
            max-width: 420px;
            color: rgb(142, 142, 142);
            border-radius: 6px;
        }

        .input::placeholder {
            color: rgb(142, 142, 142);
        }

        .input-icon {
            color: #191919;
            position: absolute;
            width: 20px;
            height: 20px;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
        }

        .logo {
            height: 33px;
            width: 100px;
        }

        #consume>.title {
            font-size: 0.8em;
        }

        .input-date,
        .input-time {
            display: flex;
            flex-direction: row-reverse;
            padding: 0.5em 4px;
            text-align: end;
        }

        .input-date::-webkit-calendar-picker-indicator,
        .input-date::-webkit-calendar-picker-indicator {
            background-image: url('static/img/calendar_icon.png');

        }

        .input-time::-webkit-calendar-picker-indicator,
        .input-time::-webkit-calendar-picker-indicator {
            background-image: url('static/img/time_icon.png');
            background-position-x: -6px;
            ;
        }

        .firma-input {
            border: 0;
            border-bottom: 1px solid #000;
            outline: none;
        }

        .logofondo {
            width: 400px;
            position: absolute;
            top: 40%;
            opacity: .8;
            margin: auto;
            z-index: -1;
        }

        .span-input {
            font-size: 0.7em;
        }
    </style>
</head>

<body>
    <p style="text-align:right;">
        <span class="text-color span-input" style="text-align: right;">
            Folio: {{$data->folio}}
        </span>
    </p>
    <header>
        <img class="pt-2 logo" src="static/img/logo.png" alt="logo" />
    </header>
    <main class="w-full flex justify-center flex-col items-center" style="position: relative;">
        <img class="logofondo" src="static/img/fondo.png" style="margin-left: 150px; margin-top: -50px" />
        <h1 class="main-title font-bold" style="display: block; text-align: center; margin-bottom: 40px">
            Salida de transporte
        </h1>

        <section class="pt-1 w-full">
            <article class="flex flex-row justify-evenly gap-3 ">
                <div class="flex flex-col gap-1" style="width: 49%; display: inline-block; text-align: center; margin-top: 10px">
                    <label class="title">
                        Fecha y hora de carga*
                    </label>
                    <div class="flex items-center justify-between" style="width: 80%;margin: auto;margin-top: 0;">
                        <table style="width: 100%;">
                            <tr>
                                <td style="text-align: right; width: 50%;">
                                    <img class="" src="static/img/calendar_icon.png" alt="calendar_icon" height="20" width="20" style="margin-top: 20px;" />
                                </td>
                                <td style="text-align: center; width: 50%;">
                                    <span class="text-color span-input" style="text-align: right; margin-top: -20px">
                                        {{$data->upload_date}}
                                    </span>
                                </td>
                            </tr>
                        </table>
                        
                        
                    </div>
                    <div class="flex items-center justify-between" style="width: 80%;margin: auto;margin-top: 0;">
                        <table style="width: 100%;">
                            <tr>
                                <td style="text-align: right; width: 50%;">
                                    <img class="" src="static/img/time_icon.png" alt="calendar_icon" height="20" width="20" style="margin-top: 10px;" />
                                </td>
                                <td style="text-align: center; width: 30%;">
                                    <span class="text-color span-input" style="text-align: center;">
                                        {{$data->charging_hour}}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="flex flex-col gap-1" style="width: 50%; display: inline-block; text-align: center; margin-top: 10px">
                    <label class="title">
                        Fecha y hora de descarga*
                    </label>
                    <div class="flex items-center justify-between" style="width: 80%;margin: auto;margin-top: 0;">
                        <table style="width: 100%;">
                            <tr>
                                <td style="text-align: right; width: 50%;">
                                    <img class="" src="static/img/calendar_icon.png" alt="calendar_icon" height="20" width="20" style="margin-top: 20px;" />
                                </td>
                                <td style="text-align: center; width: 50%;">
                                    <span class="text-color span-input" style="text-align: center; margin-top: -20px">
                                        {{$data->download_date}}
                                    </span>
                                </td>
                            </tr>
                        </table>
                        
                    </div>
                    <div class="flex items-center justify-between" style="width: 80%;margin: auto;margin-top: 0;">
                        <table style="width: 100%;">
                            <tr>
                                <td style="text-align: right; width: 50%;">
                                    <img class="" src="static/img/time_icon.png" alt="calendar_icon" height="20" width="20" style="margin-top: 10px;" />
                                </td>
                                <td style="text-align: center; width: 30%;">
                                    <span class="text-color span-input" style="text-align: center;">
                                        {{$data->download_time}}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </article>
            <table class="w-full pt-2">
                <tbody>
                    <tr>
                        <td>
                            <label class="title">
                                Cliente
                            </label> </br>
                            <span class="span-input text-color">{{$data->client}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label class="title">
                                Origen
                            </label> </br>
                            <span class="span-input text-color">{{$data->origin_address}}</span>
                        </td>
                        <td>
                            <label class="title">
                                Destino
                            </label> </br>
                            <span class="span-input text-color">{{$data->destination_address}}</span>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <label class="title">
                                Unidad
                            </label> </br>
                            <span class="span-input text-color">{{$data->unit}}</span>
                        </td>
                        <td>
                            <label class="title">
                                Placa
                            </label> </br>
                            <span class="span-input text-color">{{$data->plates}}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label class="title">
                                Operador
                            </label> </br>
                            <span class="span-input text-color">{{$data->driver}}</span>
                        </td>
                        <td>
                            <label class="title">
                                Auxiliar(s)
                            </label> </br>
                            <span class="span-input text-color">{{$data->assistant}}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section class="pt-2 w-full flex items-center flex-col" id="consume">
            <h2 class="title" style="display: block; text-align: center; margin-bottom: 15px; width: 100%">
                Registro de insumos
                <br>
            </h2>
            <article class="flex flex-row justify-between w-full" style="display: block; width: 100%">
                <div class="flex-grow" style="width: 49.1%; display: inline-block;">
                    <h4 class="title icon-color table-text">
                        Cantidad
                    </h4>
                    <article class="table-text cant-col">
                        <div class="">
                            {{$data->bands}}
                        </div>
                        <div class="table-bg">
                            {{$data->roller_skates}}
                        </div>
                        <div class="">
                            {{$data->beach}}
                        </div>
                        <div class="table-bg">
                            {{$data->devils}}
                        </div>
                        <div class="">
                            {{$data->mats}}
                        </div>
                        <div class="table-bg">
                            {{$data->cartons}}
                        </div>
                    </article>
                </div>
                <div class="flex-grow" style="width: 50%; display: inline-block; margin-top: 80px; margin-left: -5px;">
                    <h4 class="title icon-color table-text">
                        Insumos
                    </h4>
                    <article class="table-text table-item">
                        <div class="">
                            Bandas
                        </div>
                        <div class="table-bg">
                            Patines
                        </div>
                        <div class="">
                            Playos
                        </div>
                        <div class="table-bg">
                            Diablos
                        </div>
                        <div class="">
                            Colchonetas
                        </div>
                        <div class="table-bg">
                            Cartones
                        </div>
                    </article>
                </div>
            </article>
        </section>

        <footer class="flex flex-row w-full justify-between pt-2" id="firmas" style="padding-bottom: 0.75em;">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 33.33%; text-align:center">
                        <span class="firma-input text-color span-input" style="width: 140%; max-width: 120px; text-align: center;">_____________________</span>
                        <br>
                        <label class="title">Direcci&oacute;n</label>
                    </td>
                    <td style="width: 33.33%;text-align:center">
                        <span class="firma-input text-color span-input" style="width: 140%; max-width: 120px; text-align: center;">_____________________</span>
                        <br>
                        <label class="title">Coordinaci&oacute;n</label>
                    </td>
                    <td style="width: 33.33%;text-align:center">
                        <span class="firma-input text-color span-input" style="width: 140%; max-width: 120px; text-align: center;">_____________________</span>
                        <br>
                        <label class="title">Operador</label>
                    </td>
                </tr>
            </table>
            <!-- <div class="flex flex-col items-center">
                
            </div>
            <div class="flex flex-col items-center">
                
            </div>
            <div class="flex flex-col items-center">
                
            </div> -->
        </footer>

    </main>
</body>

</html>