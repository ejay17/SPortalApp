<!DOCTYPE html>
<html lang="en">

<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>SPortal Log in</title>
        <script src="https://cdn.tailwindcss.com"></script>

        <style>
                @font-face {
                        font-family: 'Nunito';
                        src: url('fonts/Nunito-Regular.ttf') format('truetype');
                }

                body {
                        font-family: 'Nunito';
                }
        </style>
</head>

<body>
        <div class="h-screen w-screen flex flex-col items-center justify-center bg-orange-50/30">
                <div class="w-[90%] flex flex-col items-center gap-20">
                        <div class="flex flex-col items-center justify-center">
                                <p class="text-6xl flex items-center font-nunito font-extrabold text-yellow-500"><img src="assets/images/S lang - SPortal Logo.svg" alt="" width="56">Portal</p>
                                <p class="text-sm text-yellow-700">Way to your Sports.</p>
                        </div>
                        <form action=""
                                class="w-full p-9 flex flex-col gap-y-4 rounded-xl ">

                                <div class="flex flex-col gap-y-2">
                                        <label class="text-yellow-700 font-semibold">Username</label>
                                        <input class="p-4 rounded-md outline-yellow-600 border border-yellow-100"
                                                type="text">
                                </div>

                                <div class="flex flex-col gap-y-2">
                                        <label class="text-yellow-700 font-semibold" for="">Password</label>
                                        <div class="relative flex flex-col">
                                                <input class="p-4 rounded-md outline-yellow-600 border border-yellow-100"
                                                        id="password" type="password">
                                                <img id="password-eye" src="assets/icons/eye-state.svg" alt="" width="32"
                                                        class="absolute right-4 top-1/2 -translate-y-1/2">
                                        </div>
                                </div>
                                <div class="flex justify-center">
                                        <a class="text-sm text-yellow-700" href="pages/signup.php">Don't have an account? <span class="text-yellow-600 underline">Sign up Here.</span></a>
                                </div>
                                <div class="">
                                        <button class="w-full flex items-center justify-center gap-x-2 py-4 bg-yellow-600 rounded-full text-lg text-white font-bold">Log In <img src="assets/icons/login.svg" alt="" width="20"></button>
                                </div>
                        </form>
                </div>
        </div>

        <script>
                const eye = document.getElementById("password-eye");
                const inputpass = document.getElementById("password");

                eye.addEventListener("click", () => {
                        if (inputpass.type === "password") {
                                inputpass.type = "text";
                                eye.setAttribute("src", "assets/icons/eye-clicked.svg");
                        } else {
                                inputpass.type = "password";
                                eye.setAttribute("src", "assets/icons/eye-state.svg");
                        }
                });
        </script>
</body>

</html>