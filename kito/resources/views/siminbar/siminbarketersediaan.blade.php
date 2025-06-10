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
    <title>Document</title>
</head>
<body class="h-full">
    <!-- component -->
    <div x-data="{ sidebarOpen: false }" class="flex h-screen">
        <x-sidebar></x-sidebar>
        <div class="flex flex-col flex-1 overflow-hidden">
            <x-navbar></x-navbar>
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200">
                <div class="container px-6 py-8 mx-auto">
                    <h3 class="text-3xl font-medium text-gray-700">Ketersediaan Barang</h3>
                    <div class="mt-8">
                        <div class=" mb-3">
                            <div class="w-full md:w-1/2 flex flex-col md:flex-row">
                                <div class="flex flex-grow mb-2 md:mb-0">
                                    <input type="text" id="searchInput" class="w-full border border-gray-300 rounded p-2" placeholder="Cari barang">
                                </div>
                            </div>
                            <!-- Add a placeholder for the search result message -->
                            <p id="searchResultMessage" class="mt-2 text-gray-600"></p>
                            <div class="w-full md:w-1/2 flex items-center space-x-2 mt-3">
                                <!-- Tombol Submit -->
                                <button id="submitButton" class=" px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                                    Submit
                                </button>

                                <!-- Tombol Reset -->
                                <a id="resetButton" href="/hamuktisuratmasuk" class="ml-2 px-4 py-2 bg-gray-300 text-black rounded-lg hover:bg-gray-400">
                                    Reset
                                </a>
                            </div>

                        </div>
                    </div>

                    <div class="flex flex-col mt-8">
                        <div class="py-2 -my-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
                            <div
                                class="inline-block min-w-full overflow-hidden align-middle border-b border-gray-200 shadow sm:rounded-lg">
                                <table class="min-w-full">
                                    <thead>
                                        <tr>
                                            <th
                                                class="px-6 py-5 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                                No</th>
                                            <th
                                                class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                                Nama Barang</th>
                                                <th
                                                class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                                                Stok Tersedia</th>
                                        </tr>
                                    </thead>

                                    <tbody class="bg-white">
                                        @foreach($barangs as $barang)
                                        <tr id="{{ $barang['id'] }}">
                                            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                                                <div class="text-sm font-medium leading-5 text-gray-900 whitespace-nowrap">{{ $loop->iteration }}
                                                </div>

                                            </td>

                                            <td class="relative mx-auto max-w-6xl [--arrow-size:5px] [--tooltip-color:white] border-b border-gray-200">
                                                <div id="grid" class=" gap-2  p-4 ">
                                                    <div id="gridItem" class="relative cursor-pointer min-w-40 line-clamp-2 text-xs hover:z-50" data-tooltip="LOOK HERE">{{ $barang['namabarang'] }}</div>
                                                </div>
                                                <div class="bg-opacity-100 absolute shadow-md left-[calc(theme(padding.8)+theme(padding.4)+(theme(width.3)/2))] top-[calc(theme(padding.8)+theme(padding.4)-.25rem)] w-60 max-w-xs origin-bottom -translate-x-1/2 translate-y-[calc(-100%-var(--arrow-size))] rounded-[.3rem] bg-[--tooltip-color] p-2 m-2 text-center text-xs transition-transform scale-0 [#grid:has(#gridItem:nth-child(1):hover)~&]:scale-100 z-50 overflow-hidden break-words">

                                                    {{ $barang['namabarang'] }}
                                                </div>
                                            </td>
                                            <td class="relative mx-auto max-w-6xl [--arrow-size:5px] [--tooltip-color:white] border-b border-gray-200">
                                                <div id="grid" class=" gap-2  p-4 ">
                                                    <div id="gridItem" class="relative cursor-pointer min-w-40 line-clamp-2 text-xs hover:z-50" data-tooltip="LOOK HERE">{{ $barang['stoktersedia'] }}</div>
                                                </div>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                    <div class="flex justify-center mt-4">

                        {{ $barangs->links() }}
                    </div>
                </div>
            </main>
        </div>
    </div>
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script type="text/javascript">
// modal button
// Trigger search when clicking the search button
document.getElementById('submitButton').addEventListener('click', function() {
    performSearch();

});

// Trigger search when pressing 'Enter' in the search input field
document.getElementById('searchInput').addEventListener('keydown', function(event) {
    if (event.key === 'Enter') {
        event.preventDefault(); // Prevent form submission (if inside a form)
        performSearch();
    }
});

// Trigger search when pressing 'Enter' in the start date input field
document.getElementById('datepicker-range-start').addEventListener('keydown', function(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        performSearch();
    }
});

// Trigger search when pressing 'Enter' in the end date input field
document.getElementById('datepicker-range-end').addEventListener('keydown', function(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        performSearch();
    }
});

// Update search result message when the page loads
document.addEventListener('DOMContentLoaded', function() {
    updateSearchResultMessage(); // Automatically update message on page load
});


function performSearch() {
    const searchValue = document.getElementById('searchInput').value;
    const startDate = document.getElementById('datepicker-range-start').value;
    const endDate = document.getElementById('datepicker-range-end').value;
    

    const url = new URL(window.location.href);

    // Set search parameter if it exists
    if (searchValue) {
        url.searchParams.set('search', searchValue);
    } else {
        url.searchParams.delete('search');
    }

    // Set start date parameter if it exists
    if (startDate) {
        url.searchParams.set('start_date', startDate);
    } else {
        url.searchParams.delete('start_date');
    }

    // Set end date parameter if it exists
    if (endDate) {
        url.searchParams.set('end_date', endDate);
    } else {
        url.searchParams.delete('end_date');
    }

    // Redirect with updated query parameters
    window.location.href = url.toString();


}


// Function to update the search result message
function updateSearchResultMessage() {
    const urlParams = new URLSearchParams(window.location.search);
    const searchQuery = urlParams.get('search');
    const startDate = urlParams.get('start_date');
    const endDate = urlParams.get('end_date');

    let message = '';

    // If there is a search query, add it to the message
    if (searchQuery) {
        message += `Hasil pencarian untuk: "${searchQuery}"`;
    }

    // If there is a start date and end date, add them to the message
    if (startDate && endDate) {
        if (message) {
            message += ' dan '; // Connect search query with date range
        }
        message += `rentang tanggal: ${startDate} hingga ${endDate}`;
    }

    // Update the message in the DOM
    if (message) {
        document.getElementById('searchResultMessage').textContent = message;
    } else {
        document.getElementById('searchResultMessage').textContent = '';
    }
}
</script>