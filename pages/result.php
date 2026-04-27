<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once __DIR__ . '/../config.php';

?>
<!DOCTYPE html>

<html class="light" lang="en">
    <head>
        <meta charset="utf-8"/>
        <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
        <link href="../styles/output.css" rel="stylesheet"/>
    </head>
    <body class="bg-surface text-on-surface font-body">
        <nav class="fixed top-0 w-full z-50 bg-surface/70 dark:bg-on-surface/70 backdrop-blur-md">
            <div class="flex justify-between items-center px-4 py-4 max-w-screen-2xl mx-auto">
                <div class="font-headline text-xl font-bold tracking-tight text-primary dark:text-[#dbe1ff]">
                    <a href="../index.php">
                        Curated Wanderer
                    </a>
                </div>
                <div class="hidden md:flex gap-8">
                    <div class="font-headline uppercase tracking-widest text-primary dark:text-[#dbe1ff] border-b-2 border-primary pb-1">
                        Destinations
                    </div>
                    <div class="font-headline uppercase tracking-widest text-[#44474e] dark:text-[#c4c6d0]">
                        Itinerary
                    </div>
                </div>
                <div class="flex items-center gap-6">
                    <!--Search Bar-->
                    <div class="relative group max-w-2xl mx-auto">
                    <!--Div that contains input and search icon-->
                    <div class="flex items-center bg-outline-variant/70 glass-effect p-1 rounded-full shadow-2xl transition-all duration-300 focus-within:bg-surface focus-within:ring-2 focus-within:ring-primary">
                    <div class="pl-2 flex items-center text-on-surface-variant">
                    <svg height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f">
                        <path d="M784-120 532-372q-30 24-69 38t-83 14q-109 0-184.5-75.5T120-580q0-109 75.5-184.5T380-840q109 0 184.5 75.5T640-580q0 44-14 83t-38 69l252 252-56 56ZM380-400q75 0 127.5-52.5T560-580q0-75-52.5-127.5T380-760q-75 0-127.5 52.5T200-580q0 75 52.5 127.5T380-400Z"/>
                    </svg>
                    </div>
                    <input type="text" id="searchInput" class="w-full bg-transparent border-0 outline-none focus:ring-0 px-2 py-1 text-on-surface placeholder:text-stone-500 font-medium" placeholder="Search province..."/>
                    </div>
                    </div>
                    <!--Search Suggestions Dropdown-->
                    <div id="suggestionsDropdown" class="absolute right-10 top-10 mt-2 bg-surface rounded-xl shadow-lg z-30 hidden">
                        <!--Populated dynamically by JavaScript-->
                    </div>
                    <div>
                        <a href="../pages/profile.php">
                        <img src="<?php echo getUserProfilePic(); ?>" class="w-10 h-10 rounded-full overflow-hidden"/>
                        </a>
                    </div>
                </div>
            </div>
        </nav>
        <main class="pt-24 pb-16 px-8 max-w-screen-2xl mx-auto">
            <header class="mb-8">
                <nav class="flex items-center gap-2 text-xs uppercase tracking-widest text-outline mb-4 font-headline">
                    <span>Philippines</span>
                    <svg class="w-10 h-10 sm:w-3 sm:h-3 lg:w-4 lg:h-4 text-gray-700" viewBox="0 0 24 24">
                        <path d="M9 6l6 6-6 6" fill="currentColor"/>
                    </svg>
                </nav>
                <h1 class="text-5xl md:text-6xl font-headline font-bold text-primary mb-4 leading-tight">
                    <span>Curated</span>
                    <span id="provinceName"></span>
                </h1>
                <p id="provinceDescription" class="text-on-surface-variant max-w-2xl text-lg leading-relaxed">
                </p>
            </header>
            <section id="topDestinationsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-8 mb-20">
                <!--Top destinations will be rendered here by JavaScript-->
            </section>
            <section class="mb-20">
                <div class="flex justify-between items-end mb-4">
                    <div>
                        <h2 class=" text-3xl font-headline font-bold text-primary mb-2">
                            Off the Beaten Path
                        </h2>
                        <p class="text-on-surface-variant">
                            Rare destinations for the intentional traveler.
                        </p>
                    </div>
                </div>
                <!--Carousel Container-->
                <div class="relative">
                    <!--Slides wrapper (overflow hidden)-->
                    <div class="overflow-hidden">
                        <div id="offBeatenCarouselTrack" class="flex transition-transform duration-500 ease-out">
                            <!--Off the beaten path destinations will be rendered here by JavaScript-->
                        </div>
                    </div>
                    <!--Navigation arrows-->
                    <button id="prevOffArrow" class="absolute left-0 top-1/2 -translate-y-1/2 w-12 h-12 p-2 z-10 shadow-lg rounded-full border border-outline-variant/20 flex items-center justify-center text-on-surface-variant bg-on-primary/80 hover:bg-on-primary transition-colors">
                        <svg class="w-6 h-6 text-gray-600 align-middle" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M17 10a1 1 0 01-1 1H6.414l3.293 3.293a1 1 0 01-1.414 1.414l-5-5a1 1 0 010-1.414l5-5a1 1 0 011.414 1.414L6.414 9H16a1 1 0 011 1z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <button id="nextOffArrow" class="absolute right-0 top-1/2 -translate-y-1/2 w-12 h-12 p-2 shadow-lg z-10 rounded-full border border-outline-variant/20 flex items-center justify-center text-on-surface-variant bg-on-primary/80 hover:bg-on-primary transition-colors">
                        <svg class="w-6 h-6 text-gray-600 align-middle" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h9.586l-3.293-3.293a1 1 0 111.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 11-1.414-1.414L13.586 11H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </section>
        </main>
        <footer class="w-full mt-20 bg-[#e3e3de] dark:bg-stone-900">
            <div class="flex flex-col md:flex-row justify-between items-center px-12 py-5 gap-8 max-w-screen-2xl mx-auto">
                <div class="flex flex-col gap-4">
                    <div class="hidden lg:flex font-footer font-bold text-stone-900 dark:text-stone-100 text-2xl tracking-tighter">
                        Curated Wanderer
                    </div>
                    <p class="font-footer text-sm tracking-widest text-stone-500 max-w-xs">
                        © 2026 Curated Wanderer. Online Travel Planner.
                    </p>
                </div>
                <div class="flex gap-12">
                    <div class="flex flex-col gap-2">
                        <span class="font-footer text-sm uppercase tracking-widest text-[#00327d] font-bold mb-2">
                            UPOU - CMSC207
                        </span>
                        <a class="font-footer text-sm tracking-widest text-stone-500 hover:text-stone-900 dark:hover:text-stone-100 transition-all duration-200 underline-offset-4 hover:underline" href="#">
                            About Me
                        </a>
                        <a class="font-footer text-sm tracking-widest text-stone-500 hover:text-stone-900 dark:hover:text-stone-100 transition-all duration-200 underline-offset-4 hover:underline" href="#">
                            Terms
                        </a>
                    </div>
                    <div class="flex flex-col gap-2">
                        <span class="font-footer text-sm uppercase tracking-widest text-[#00327d] font-bold mb-2">
                            Support
                        </span>
                        <a class="font-footer text-sm tracking-widest text-stone-500 hover:text-stone-900 dark:hover:text-stone-100 transition-all duration-200 underline-offset-4 hover:underline" href="#">
                            Privacy
                        </a>
                        <a class="font-footer text-sm tracking-widest text-stone-500 hover:text-stone-900 dark:hover:text-stone-100 transition-all duration-200 underline-offset-4 hover:underline" href="#">
                            Support
                        </a>
                    </div>
                </div>
            </div>
        </footer>
        <script src="../scripts/common.js"></script>
        <script src="../scripts/result.js"></script>
    </body>
</html>