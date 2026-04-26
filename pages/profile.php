<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: log_in.php");
    exit();
}

require_once '../php/dbConnect.php';
require_once __DIR__ . '/../config.php';

$user_id = $_SESSION['user_id'];

// Fetch user data
$stmt = $pdo->prepare("SELECT name, motto, profile_picture FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // Thise should never happen, but will be handled gracefully if ever
    session_destroy();
    header("Location: log_in.php");
    exit();
}

$name = htmlspecialchars($user['name']);
$motto = htmlspecialchars($user['motto'] ?? '');
$profilePic = $user['profile_picture'] ?? '';
$profilePicUrl = !empty($profilePic) ? '../' . $profilePic : '../images/profiles/default-avatar.jpg';
?>
<!DOCTYPE html>

<html class="light" lang="en">
    <head>
        <meta charset="utf-8"/>
        <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
        <title>Curated Wanderer | Profile</title>
        <link href="../styles/output.css" rel="stylesheet"/>
    </head>
    <body class="bg-surface text-on-surface font-body">
        <!--Navigation Bar-->
        <header class="fixed top-0 w-full z-50 bg-surface/70 dark:bg-slate-950/70 backdrop-blur-xl">
            <nav class="flex justify-between items-center px-4 py-4 max-w-screen-2xl mx-auto">
                <div class="font-headline text-xl font-bold tracking-tight text-primary dark:text-[#dbe1ff]">
                    <a href="../index.html">
                        Curated Wanderer
                    </a>
                </div>
                <div class="hidden md:flex gap-8">
                    <a class="font-headline tracking-widest uppercase text-[#44474e] dark:text-[#c4c6d0] hover:text-primary transition-colors duration-300" href="../pages/result.html">
                        Destinations
                    </a>
                    <a class="font-headline uppercase tracking-widest text-[#44474e] dark:text-[#dbe1ff] hover:text-primary transition-colors duration-300" href="../pages/itinerary.html">
                        Itinerary
                    </a>
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
                        <img id="profileImage" src="<?php echo $profilePicUrl; ?>" alt="Profile Picture" class="w-10 h-10 rounded-full overflow-hidden"/>
                    </div>
                </div>
            </nav>
        </header>
        <main class="pt-28 pb-20 max-w-screen-2xl mx-auto px-8 lg:px-12">
            <!--Profile Header Section-->
            <section class="flex flex-col lg:flex-row gap-12 items-start mb-20">
                <!--Avatar Area-->
                <div class="profile-picture-container relative group shrink-0">
                    <div class="w-48 h-64 rounded-xl overflow-hidden bg-surface-container-high shadow-2xl shadow-primary/5">
                        <img id="profileImage" src="<?php echo $profilePicUrl; ?>" alt="Profile Picture" class="w-full h-full object-cover"/>
                    </div>
                    <button id="editPhotoBtn" class="absolute -bottom-4 -right-4 bg-primary text-on-primary p-4 rounded-xl shadow-lg hover:scale-105 transition-transform duration-200 flex items-center gap-2 group">
                        <!--Edit Icon-->
                        <svg class="text-base" height="18px" viewBox="0 -960 960 960" width="18px" fill="#ffffff">
                            <path d="M200-200h57l391-391-57-57-391 391v57Zm-80 80v-170l528-527q12-11 26.5-17t30.5-6q16 0 31 6t26 18l55 56q12 11 17.5 26t5.5 30q0 16-5.5 30.5T817-647L290-120H120Zm640-584-56-56 56 56Zm-141 85-28-29 57 57-29-28Z"/>
                        </svg>
                        <span class="text-xs font-bold uppercase tracking-widest font-label">
                            Edit Photo
                        </span>
                    </button>
                    <input type="file" id="profilePhotoInput" accept="image/jpeg,image/png,image/jpg" style="display: none;">
                </div>
                <!--Identity Area-->
                <div class="flex-grow pt-4">
                    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
                        <h1 id="userName" class="text-5xl font-headline font-bold text-primary tracking-tight mb-2">
                            <!--Display username-->
                            <?php echo $name; ?>
                        </h1>
                    </div>
                    <div class="relative group shrink-0">
                        <p id="userMotto" class="max-w-2xl text-on-surface-variant leading-relaxed text-lg font-light italic">
                            <!--Display motto-->
                            <?php echo $motto ?: 'Click edit to add your motto...'; ?>
                        </p>
                        <button id="editMottoBtn" class="absolute -bottom-4 -right-4 bg-primary text-on-primary p-4 rounded-xl shadow-lg hover:scale-105 transition-transform duration-200 flex items-center gap-2 group">
                        <!--Edit Icon-->
                        <svg class="text-base" height="18px" viewBox="0 -960 960 960" width="18px" fill="#ffffff">
                            <path d="M200-200h57l391-391-57-57-391 391v57Zm-80 80v-170l528-527q12-11 26.5-17t30.5-6q16 0 31 6t26 18l55 56q12 11 17.5 26t5.5 30q0 16-5.5 30.5T817-647L290-120H120Zm640-584-56-56 56 56Zm-141 85-28-29 57 57-29-28Z"/>
                        </svg>
                        </button>
                        <div id="editMottoForm">
                            <textarea id="newMotto" rows="3" class="text-on-surface w-full p-3 rounded-md focus-within:bg-surface focus-within:ring-primary">
                                <?php echo $motto; ?>
                            </textarea>
                            <div class="mt-3 flex gap-10">
                                <button id="saveMottoBtn" class="px-6 py-2.5 bg-gradient-to-r from-primary to-primary-container text-on-primary rounded-xl text-sm font-bold scale-95 active:opacity-80 transition-transform shadow-lg shadow-primary/10">
                                    Save
                                </button>
                                <button id="cancelMottoBtn" class="px-4 py-1 text-sm font-semibold text-stone-600 hover:text-[#00327d] transition-all duration-300">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--Main Content Area: Saved Itineraries-->
            <section>
                <div class="mb-12">
                    <h2 class="text-3xl font-headline font-bold text-primary tracking-tight">
                        Saved Itineraries
                    </h2>
                </div>
                <!--Bento-Style Grid-->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                    <!--Trip Card 1-->
                    <div class="group flex flex-col">
                        <div class="relative overflow-hidden rounded-xl aspect-[5/4] md:aspect-[7/8] mb-4 shadow-sm group-hover:shadow-xl transition-all duration-500">
                            <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" src="../images/palawan/el_nido/big_lagoon.jpg"/>
                            <div class="absolute bottom-0 left-0 right-0 p-8 bg-gradient-to-t from-primary/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <button class="w-full bg-white text-primary py-4 rounded-xl font-headline font-bold text-sm uppercase tracking-widest">
                                View Itinerary
                            </button>
                            </div>
                        </div>
                        <div>
                            <span class="text-[10px] font-label uppercase tracking-[0.2em] text-secondary font-bold">
                                Palawan • 3 Days
                            </span>
                            <h3 class="text-2xl font-headline font-bold text-primary mt-2">
                                El Nido
                            </h3>
                        </div>
                    </div>
                    <!--Trip Card 2 (Offset/Asymmetric feel)-->
                    <div class="group flex flex-col lg:mt-12">
                        <div class="relative overflow-hidden rounded-xl aspect-[5/4] md:aspect-[7/8] mb-4 shadow-sm group-hover:shadow-xl transition-all duration-500">
                            <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" src="../images/rizal/binangonan/mount_tagapo.jpg"/>
                            <div class="absolute bottom-0 left-0 right-0 p-8 bg-gradient-to-t from-primary/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <button class="w-full bg-white text-primary py-4 rounded-xl font-headline font-bold text-sm uppercase tracking-widest">
                                View Itinerary
                            </button>
                            </div>
                        </div>
                        <div>
                            <span class="text-[10px] font-label uppercase tracking-[0.2em] text-secondary font-bold">
                                Rizal • 1 Days
                            </span>
                            <h3 class="text-2xl font-headline font-bold text-primary mt-2">
                                Binangonan
                            </h3>
                        </div>
                    </div>
                    <!--Trip Card 3-->
                    <div class="group flex flex-col">
                        <div class="relative overflow-hidden rounded-xl aspect-[5/4] md:aspect-[7/8] mb-4 shadow-sm group-hover:shadow-xl transition-all duration-500">
                            <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" src="../images/leyte/palompon/kalanggaman_island.jpg"/>
                            <div class="absolute bottom-0 left-0 right-0 p-8 bg-gradient-to-t from-primary/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <button class="w-full bg-white text-primary py-4 rounded-xl font-headline font-bold text-sm uppercase tracking-widest">
                                View Itinerary
                            </button>
                            </div>
                        </div>
                        <div>
                            <span class="text-[10px] font-label uppercase tracking-[0.2em] text-secondary font-bold">
                                Leyte • 2 Days
                            </span>
                            <h3 class="text-2xl font-headline font-bold text-primary mt-2">
                                Kalanggaman Island
                            </h3>
                        </div>
                    </div>
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
        <script src="../scripts/profile.js"></script>
    </body>
</html>