<!DOCTYPE html>
<html lang="en">

<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>SPortal</title>
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
        <script>
                tailwind.config = {
                        theme: {
                                extend: {
                                        fontFamily: {
                                                nunito: ['Nunito'],
                                        }
                                }
                        }
                }
        </script>
</head>

<body>
        <div class="h-screen w-screen flex flex-col items-center justify-center bg-orange-50/30">
                <div class="w-[90%] h-[50%] flex flex-col items-center gap-20">
                        <div class="flex flex-col items-center justify-center">
                                <p class="text-6xl flex items-center font-nunito font-extrabold text-yellow-500"><img src="assets/images/S lang - SPortal Logo.svg" alt="" width="56">Portal</p>
                                <p class="text-sm text-yellow-600">Way to your Sports.</p>
                        </div>
                        <form action=""
                                class="w-full p-9 flex flex-col gap-y-4 rounded-xl">

                                <div class="flex flex-col gap-y-2">
                                        <label class="text-yellow-700">E-mail</label>
                                        <input class="p-4 rounded-md outline-yellow-600 border border-yellow-100"
                                                type="text">
                                </div>

                                <div class="flex flex-col gap-y-2">
                                        <label class="text-yellow-700 " for="">Password</label>
                                        <input class="p-4 rounded-md outline-yellow-600 border border-yellow-100"
                                                type="password">
                                </div>
                                <div class="flex justify-center">
                                        <a class="text-sm" href="">Don't have an account? <span class="text-yellow-500 underline">Sign up Here.</span></a>
                                </div>
                                <div class="">
                                        <button class="w-full py-4 bg-yellow-500 rounded-full text-lg text-white">Log In</button>
                                </div>
                        </form>
                </div>
        </div>
</body>

</html>