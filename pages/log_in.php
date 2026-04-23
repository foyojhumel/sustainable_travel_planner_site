<?php session_start(); ?>
<!DOCTYPE html>

<html class="light" lang="en">
    <head>
        <meta charset="utf-8"/>
        <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
        <title>Login | Curated Wandere</title>
        <link href="../styles/output.css" rel="stylesheet"/>
    </head>
    <body class="bg-surface text-on-surface font-body selection:bg-primary-container selection:text-on-primary-container min-h-screen flex items-center justify-center p-6">
        <!--Subtle background aesthetic element-->
        <div class="fixed inset-0 overflow-hidden pointer-events-none opacity-20">
            <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-surface-container blur-3xl"></div>
            <div class="absolute bottom-0 right-0 w-[40rem] h-[40rem] rounded-full bg-surface-container-highest blur-3xl"></div>
        </div>
        <main class="relative z-10 w-full max-w-5xl grid grid-cols-1 md:grid-cols-12 bg-surface-container-low rounded-[2rem] overflow-hidden shadow-sm">
            <!--Left Column: Editorial Imagery-->
            <div class="hidden md:flex md:col-span-5 relative flex-col justify-between p-12 text-on-primary">
                <div class="absolute inset-0 z-0">
                    <img class="w-full h-full object-cover brightness-95" data-alt="A beautiful contrast of green lush hills at the bottom of bright blue sky sitting on top of Mt. Mayon." src="../images/albay/daraga/mayon_volcano_2.jpg"/>
                    <div class="absolute inset-0 bg-gradient-to-t from-primary/60 to-transparent"></div>
                </div>
                <div class="relative z-10">
                    <h1 class="font-headline font-bold text-3xl tracking-tighter text-surface">
                        Curated Wanderer
                    </h1>
                    <p class="font-label text-xs uppercase tracking-[0.2em] mt-2 opacity-80 text-surface">
                        Online Travel Planner
                    </p>
                </div>
                <div class="relative z-10">
                    <blockquote class="font-headline text-2xl font-light italic leading-relaxed text-surface">
                        "To travel is to discover that everyone is wrong about other countries."
                    </blockquote>
                    <p class="mt-4 font-label text-sm uppercase tracking-widest opacity-70">
                        — Aldous Huxley
                    </p>
                </div>
            </div>
            <!--Right Column: Login Interface-->
            <div class="md:col-span-7 bg-surface-container-low p-8 md:p-16 flex flex-col justify-center">
            <!--Mobile Brand Header (Hidden on MD)-->
            <div class="md:hidden mb-8 text-center">
                <h1 class="font-headline font-bold text-2xl tracking-tighter text-primary">
                    Curated Wanderer
                </h1>
            </div>
            <div class="max-w-md mx-auto w-full">
                <header class="mb-8">
                    <h2 class="font-headline text-4xl font-bold tracking-tight text-on-surface mb-3">
                        Ready for your next adventure?
                    </h2>
                    <p class="text-on-surface-variant text-lg">
                        Enter your account details to access your curated journeys.
                    </p>
                </header>
            <form action="../php/login.php" method="POST" class="space-y-6">
                <!--Display login success message-->
                <?php if (isset($_SESSION['registration_success'])): ?>
                    <div class="bg-green-100 text-green-700 p-3 rounded-xl mb-4">
                        <?php echo htmlspecialchars($_SESSION['login success']); ?>
                        <?php unset($_SESSION['registration_success']); ?>
                    </div>
                <?php endif; ?>
                <!--Display errors if wrong email or password provided-->
                <?php if (isset($_SESSION['login_errors'])): ?>
                    <div class="bg-red-100 text-red-700 p-3 rounded-xl mb-4">
                        <?php foreach ($_SESSION['login_errors'] as $error): ?>
                            <p><?php echo htmlspecialchars($error); ?></p>
                        <?php endforeach; ?>
                    </div>
                    <?php unset($_SESSION['login_errors']); ?>
                <?php endif; ?>
                <!--Email Field-->
                <div class="space-y-2">
                    <label class="font-label text-xs uppercase tracking-widest font-semibold text-on-surface-variant" for="email">
                        Email Address
                    </label>
                <div class="relative">
                    <input  id="email" name="email" placeholder="wanderer@example.com" type="email" class="w-full px-5 py-2 bg-surface-container-high border-none rounded-xl focus:ring-2 focus:ring-primary/20 focus:bg-surface-container-highest transition-all duration-300 outline-none text-on-surface placeholder:text-outline"/>
                </div>
                </div>
                <!--Password Field-->
                <div class="space-y-2">
                <div class="flex justify-between items-center">
                    <label class="font-label text-xs uppercase tracking-widest font-semibold text-on-surface-variant" for="password">
                        Password
                    </label>
                    <a class="font-label text-xs uppercase tracking-widest font-bold text-primary hover:text-primary-container transition-colors" href="#">
                        Forgot?
                    </a>
                </div>
                <div class="relative">
                    <input id="password" name="password" placeholder="••••••••" type="password" class="w-full px-5 py-2 bg-surface-container-high border-none rounded-xl focus:ring-2 focus:ring-primary/20 focus:bg-surface-container-highest transition-all duration-300 outline-none text-on-surface placeholder:text-outline"/>
                </div>
                </div>
                <!--CTA Button-->
                <div class="pt-4">
                    <button type="submit" class="w-full editorial-gradient text-on-primary font-headline font-semibold py-2 px-8 rounded-full shadow-lg shadow-primary/10 hover:shadow-primary/20 transform hover:-translate-y-0.5 transition-all duration-300 active:scale-95">
                        Login to Dashboard
                    </button>
                </div>
            </form>
            <footer class="mt-8 text-center">
                <p class="text-on-surface-variant font-label text-sm">
                    Don't have an account?
                <button class="text-secondary font-bold hover:underline underline-offset-4 ml-1" onclick="window.location.href='../pages/sign_up.html'">
                    Create an account
                </button>
                </p>
            </footer>
            </div>
            </div>
        </main>
        <div class="fixed bottom-1 w-full text-center pointer-events-none">
            <p class="font-label text-[10px] uppercase tracking-[0.3em] text-outline opacity-50">
                © 2026 Curated Wanderer. Online Travel Planner.
            </p>
        </div>
    </body>
</html>