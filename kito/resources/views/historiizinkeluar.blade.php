<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('viteall')
    <link rel="icon" href="/Logo BPS.png" type="image/png">
    <title>Histori izin keluar</title>
</head>

<body class="h-full">
   
    <!-- component -->
    <div x-data="{ sidebarOpen: false }" class="flex h-screen">
        <x-sidebar></x-sidebar>
        <div class="flex flex-col flex-1 overflow-hidden">
            <x-navbar></x-navbar>
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200">
                <div class="container px-6 py-8 mx-auto">
                    <h3 class="text-3xl font-medium text-gray-700">Histori Izin Keluar</h3>
               


                    <div class="flex flex-col mt-8">
                        <div class="py-2 -my-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
                            <div
                                class="inline-block min-w-full overflow-hidden align-middle border-b border-gray-200 shadow sm:rounded-lg">
                                <table class="min-w-full">
                                    <thead>
                                        <tr>
                                            <th
                                                class="px-6 py-5 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                                Tanggal</th>
                                            <th
                                                class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                                Nama</th>
                                            <th
                                                class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                                Waktu</th>
                                            <th
                                                class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                                Keperluan</th>

                                 
                                        </tr>
                                    </thead>

                                    <tbody class="bg-white">
                                        @foreach ($izinkeluars as $izinkeluar)
                                            <tr id="{{ $izinkeluar['id'] }}">
                                                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                                    <div
                                                        class="text-sm font-medium leading-5 text-gray-900 whitespace-nowrap">
                                                        {{ $izinkeluar['tanggalizin'] }}
                                                    </div>

                                                </td>

                                                <td
                                                    class="relative mx-auto max-w-6xl [--arrow-size:5px] [--tooltip-color:white] border-b border-gray-200">
                                                    <div id="grid" class=" gap-2  p-4 ">
                                                        <div id="gridItem"
                                                            class="relative cursor-pointer min-w-40 line-clamp-2 text-xs hover:z-50"
                                                            data-tooltip="LOOK HERE">{{  $izinkeluar['name'] }}</div>
                                                    </div>
                                                </td>
                                                <td
                                                    class="relative mx-auto max-w-6xl [--arrow-size:5px] [--tooltip-color:white] border-b border-gray-200">
                                                    <div id="grid" class=" gap-2  p-4 ">
                                                        <div id="gridItem"
                                                            class="relative cursor-pointer min-w-40 line-clamp-2 text-xs hover:z-50"
                                                            data-tooltip="LOOK HERE">{{  $izinkeluar['created_at'] }} - {{  $izinkeluar['jamizin'] }}</div>
                                                    </div>

                                                </td>
                                                
                                                  <td
                                                    class="relative mx-auto max-w-6xl [--arrow-size:5px] [--tooltip-color:white] border-b border-gray-200">
                                                    <div id="grid" class=" gap-2  p-4 ">
                                                        <div id="gridItem"
                                                            class="relative cursor-pointer min-w-40 line-clamp-2 text-xs hover:z-50"
                                                            data-tooltip="LOOK HERE">{{  $izinkeluar['keperluan'] }}</div>
                                                    </div>
                                                    <div
                                                        class="bg-opacity-100 absolute shadow-md left-[calc(theme(padding.8)+theme(padding.4)+(theme(width.3)/2))] top-[calc(theme(padding.8)+theme(padding.4)-.25rem)] w-60 max-w-xs origin-bottom -translate-x-1/2 translate-y-[calc(-100%-var(--arrow-size))] rounded-[.3rem] bg-[--tooltip-color] p-2 m-2 text-center text-xs transition-transform scale-0 [#grid:has(#gridItem:nth-child(1):hover)~&]:scale-100 z-50 overflow-hidden break-words">

                                                        {{  $izinkeluar['keperluan'] }}
                                                    </div>
                                                </td>

                                               
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                    <div class="flex justify-center mt-4">

                        {{ $pagination->links() }}
                    </div>
                </div>
            </main>
        </div>
    </div>
    </div>

</body>



</html>
