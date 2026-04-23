<?php session_start(); ?>
<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="utf-8"/>
        <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
        <title>Curated Wanderer | Create Your Own Journey</title>
        <link href="../styles/output.css" rel="stylesheet"/>
    </head>
    <body class="font-body text-on-surface antialiased">
        <!--Top Navigation Bar (Suppressed on focused transactional pages like registration)-->
        <!--As per "Shell Visibility & Relevance" rule: Transactional screens exclude the Shell-->
        <main class="min-h-screen flex items-center justify-center p-6 bg-surface">
            <!--Editorial Background Ornamentation-->
            <div class="fixed inset-0 left-0 w-ful h-full pointer-events-none overflow-hidden z-0">
                <div class="absolute -top-[10%] -left-[5%] w-[40%] h-[60%] bg-surface-container-low rounded-full blur-3xl opacity-60">
                </div>
                <div class="absolute bottom-[5%] right-[0%] w-[30%] h-[50%] bg-surface-container/20 rounded-full blur-3xl opcaity-40">
                </div>
            </div>
            <div class="relative z-10 w-full max-w-xl">
                <!--Brand Identity Focus-->
                <div class="text-center mb-10">
                    <h1 class="font-headline font-extrabold text-4xl text-primary tracking-tighter mb-2">
                        Curated Wanderer
                    </h1>
                    <p class="font-label text-sm uppercase tracking-widest text-on-surface-variant">
                        Start Your Editorial Journey
                    </p>
                </div>
                <!--Registration Container-->
                <div class="bg-on-primary rounded-[2rem] p-10 md:p-14 shadow-[0_20px_50px_rgba(26,28,25,0.06)] border border-outline-variant/10">
                    <header class="mb-10">
                        <h2 class="font-headline font-bold text-3xl text-on-surface tracking-tight leading-tight">
                            Create your account
                        </h2>
                        <p class="text-on-surface-variant mt-2">
                            Search for your next travel and retreat.
                        </p>
                    </header>
                    <form action="../php/signup.php" method="POST" class="space-y-6">
                        <!--Display registration errors, if there is any-->
                        <?php if (isset($_SESSION['signup_errors'])): ?>
                            <div class="bg-red-100 text-red-700 p-3 rounded-xl mb-4">
                                <?php foreach ($_SESSION['signup_errors'] as $error): ?>
                                    <p><?php echo htmlspecialchars($error); ?></p>
                                <?php endforeach; ?>
                            </div>
                            <?php unset($_SESSION['signup_errors']); ?>
                        <?php endif; ?>
                        <!--Display account creation success message-->
                        <?php if (isset($_SESSION['signup_success'])): ?>
                            <div class="bg-green-100 text-green-700 p-3 rounded-xl mb-4">
                                <?php echo htmlspecialchars($_SESSION['signup_success']); ?>
                            </div>
                            <?php unset($_SESSION['signup_success']); ?>
                        <?php endif; ?>
                        <!--Name Field-->
                        <div class="space-y-1.5">
                        <label class="font-label text-xs uppercase tracking-widest font-semibold text-on-surface-variant ml-1" for="full_name">
                            Name
                        </label>
                        <div class="relative">
                            <input id="full_name" name="name" placeholder="Juan Derer" type="text" class="w-full bg-surface-container-low border-0 rounded-xl px-5 py-2 text-on-surface placeholder:text-outline focus:ring-2 focus:ring-primary transition-all duration-300 outline-none border-none" required/>
                        </div>
                        </div>
                        <!--Email Field-->
                        <div class="space-y-1.5">
                        <label class="font-label text-xs uppercase tracking-widest font-semibold text-on-surface-variant ml-1" for="email_address">
                            Email
                        </label>
                        <div class="relative">
                            <input id="email_address" name="email" placeholder="juanderer@curated.com" type="email" class="w-full bg-surface-container-low border-0 rounded-xl px-5 py-2 text-on-surface placeholder:text-outline focus:ring-2 focus:ring-primary transition-all duration-300 outline-none border-none" required/>
                        </div>
                        </div>
                        <!--Password Grid-->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                        <label class="font-label text-xs uppercase tracking-widest font-semibold text-on-surface-variant ml-1" for="password">
                            Password
                        </label>
                        <input id="password" name="password" placeholder="••••••••" type="password" class="w-full bg-surface-container-low border-0 rounded-xl px-5 py-2 text-on-surface placeholder:text-outline focus:ring-2 focus:ring-primary transition-all duration-300 outline-none" required/>
                        </div>
                        <div class="space-y-1.5">
                        <label class="font-label text-xs uppercase tracking-widest font-semibold text-on-surface-variant ml-1" for="confirm_password">
                            Confirm Password
                        </label>
                        <input id="confirm_password" name="confirm_password" placeholder="••••••••" type="password" class="w-full bg-surface-container-low border-0 rounded-xl px-5 py-2 text-on-surface placeholder:text-outline focus:ring-2 focus:ring-primary transition-all duration-300 outline-none" required/>
                        </div>
                        </div>
                        <!--Terms Checkbox-->
                        <div class="flex items-start gap-3 py-2">
                        <input class="mt-1 rounded border-outline-variant text-primary focus:ring-primary bg-surface-container-low" id="terms" type="checkbox"/>
                        <label class="text-sm text-on-surface-variant leading-relaxed" for="terms">
                            I agree to the
                            <a class="text-primary hover:underline underline-offset-4" href="#">
                                Terms of Service
                            </a> and
                            <a class="text-primary hover:underline underline-offset-4" href="#">
                                Privacy Policy
                            </a>
                        </label>
                        </div>
                        <!--Primary Action-->
                        <div class="pt-1">
                        <button class="w-full bg-gradient-to-r from-primary to-primary-container text-on-primary font-headline font-bold py-2 rounded-xl shadow-lg hover:shadow-xl active:scale-95 transition-all duration-300" type="submit">
                            Create Account
                        </button>
                        </div>
                    </form>
                    <!--Footer Link-->
                    <div class="mt-8 pt-4 border-t border-surface-container-high text-center">
                    <p class="text-on-surface-variant">
                        Already have an account?
                        <button class="text-primary font-bold hover:underline underline-offset-4 ml-1 transition-all" onclick="window.location.href='../pages/log_in.php'">
                            Log In
                        </button>
                    </p>
                    </div>
                </div>
            </div>
        </main>
        <!--Global Footer (Suppressed on Transactional/Login Shells as per instructions)-->
    </body>
</html>