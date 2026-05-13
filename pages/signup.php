<!DOCTYPE html>
<html lang="en">

<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>SPortal Sign up</title>
        <script src="https://cdn.tailwindcss.com"></script>

        <style>
                @font-face {
                        font-family: 'Nunito';
                        src: url('../fonts/Nunito-Regular.ttf') format('truetype');
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
        <div class="h-screen w-screen flex flex-col items-center bg-orange-50/30">
                <div class="flex flex-col h-full w-full p-6">
                        <form action=""
                                class="flex flex-col gap-y-3">
                                <p class="text-2xl text-center font-bold text-yellow-600">Sign Up</p>
                                <div class="flex w-full border border-yellow-100 rounded-2xl bg-yellow-50 p-1">
                                        <button class="w-[50%] py-3 bg-yellow-700 rounded-xl text-white font-semibold">I am Player</button>
                                        <button class="w-[50%] py-3 ">I am Coach</button>
                                </div>

                                
                        </form>
                </div>
        </div>
</body>

</html>