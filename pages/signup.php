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
</head>

<body>
	<div class="h-screen w-screen flex flex-col items-center bg-orange-50/30">
		<div class="flex flex-col h-full w-full p-6 gap-y-4">
			<p class="text-2xl text-center font-bold text-yellow-700">Create an Account</p>
			<form action=""
				class="relative flex flex-col gap-y-4 p-4 rounded-xl h-[90%]">

				<div class="sticky top-0 flex w-full border border-yellow-200 rounded-xl bg-yellow-50 p-1">
					<button id="playerformbtn" type="button" class="w-[50%] py-3 bg-yellow-600 rounded-lg text-yellow-50 font-bold"> Player</button>
					<button id="coachformbtn" type="button" class="w-[50%] py-3 text-yellow-600 font-bold">Coach</button>
				</div>
				<!-- Player Form -->
				<div id="playerform" class="flex flex-col gap-y-4 overflow-y-auto">
					<div class="flex flex-col gap-y-1">
						<label class="text-yellow-700 font-semibold" for="">Given Name</label>
						<input name="givenname" type="text" placeholder="Juan"
							class="p-4 rounded-md outline-yellow-600 border border-yellow-200">
					</div>

					<div class="flex flex-col gap-y-1">
						<label class="text-yellow-700 font-semibold" for="">Middle Name</label>
						<input name="middlename" type="text" placeholder="Santos"
							class="p-4 rounded-md outline-yellow-600 border border-yellow-200">
					</div>

					<div class="flex flex-col gap-y-1">
						<label class="text-yellow-700 font-semibold" for="">Last Name</label>
						<input name="lastname" type="text" placeholder="Dela Cruz"
							class="p-4 rounded-md outline-yellow-600 border border-yellow-200">
					</div>

					<div class="w-full flex gap-x-4">
						<div class="w-[50%] flex flex-col gap-y-1">
							<label class="text-yellow-700 font-semibold" for="">Contact Number</label>
							<input name="contactnumber" type="text"
								class="p-4 rounded-md outline-yellow-600 border border-yellow-200">
						</div>
						<div class="w-[50%] flex flex-col gap-y-1">
							<label class="text-yellow-700 font-semibold" for="">Sex</label>
							<select name="yearlvl" id=""
								class="w-full p-4 rounded-md outline-yellow-600 border border-yellow-200 bg-white">
								<option value="" hidden selected></option>
								<option value="">Male</option>
								<option value="">Female</option>
							</select>
						</div>
					</div>

					<div class="flex flex-col gap-y-1">
						<label class="text-yellow-700 font-semibold" for="">Social Media Link <span class="text-xs text-yellow-700/60">(Optional)</span></label>
						<input name="socialmedialink" type="text"
							class="p-4 rounded-md outline-yellow-600 border border-yellow-200">
					</div>

					<div class="w-full flex gap-x-4">
						<div class="w-[50%] flex flex-col gap-y-1">
							<label class="text-yellow-700 font-semibold" for="">Suffix</label>
							<input name="suffix" type="text" placeholder="ex. Jr."
								class="p-4 rounded-md outline-yellow-600 border border-yellow-200">
						</div>
						<div class="w-[50%] flex flex-col gap-y-1">
							<label class="text-yellow-700 font-semibold" for="">Date of Birth</label>
							<input name="dob" type="date"
								class="w-full p-4 rounded-md outline-yellow-600 border border-yellow-200 bg-white">
						</div>
					</div>

					<div class="w-full flex gap-x-4">
						<div class="w-[50%] flex flex-col gap-y-1">
							<label class="text-yellow-700 font-semibold" for="">Student ID</label>
							<input name="studentid" type="text"
								class="p-4 rounded-md outline-yellow-600 border border-yellow-200">
						</div>
						<div class="w-[50%] flex flex-col gap-y-1">
							<label class="text-yellow-700 font-semibold" for="">Year Level</label>
							<select name="yearlvl" id=""
								class="w-full p-4 rounded-md outline-yellow-600 border border-yellow-200 bg-white">
								<option value="" hidden selected></option>
								<option value="">1st Year</option>
								<option value="">2nd Year</option>
								<option value="">3rd Year</option>
								<option value="">4th Year</option>
							</select>
						</div>
					</div>

					<div class="flex flex-col gap-y-1">
						<label class="text-yellow-700 font-semibold" for="">Institute/Campus</label>
						<select name="inscam" id=""
							class="w-full p-4 rounded-md outline-yellow-600 border border-yellow-200 bg-white">
							<option value=""></option>
						</select>
					</div>

					<div class="flex flex-col gap-y-4">
						<label class="text-yellow-700 font-semibold" for="">Your Sport/s <span class="text-sm text-yellow-700/50">(Choose your desire sport/s)</span></label>

						<div class="grid grid-cols-3 gap-y-1">
							<div class="col-span-1 flex items-center gap-x-3">
								<input type="checkbox" name="sports[]" value="Basketball" id=""
									class="border border-yellow-500 accent-yellow-500">
								<label class="text-lg text-yellow-700" for="">Basketball</label>
							</div>

							<div class="col-span-1 flex items-center gap-x-3">
								<input type="checkbox" name="sports[]" value="Volleyball" id=""
									class="border border-yellow-500 accent-yellow-500">
								<label class="text-lg text-yellow-700" for="">Volleyball</label>
							</div>

							<div class="col-span-1 flex items-center gap-x-3">
								<input type="checkbox" name="sports[]" value="Arnis" id=""
									class="border border-yellow-500 accent-yellow-500 ">
								<label class="text-lg text-yellow-700" for="">Arnis</label>
							</div>

							<div class="col-span-1 flex items-center gap-x-3">
								<input type="checkbox" name="sports[]" value="Athlethics" id=""
									class="border border-yellow-500 accent-yellow-500 ">
								<label class="text-lg text-yellow-700" for="">Athletics</label>
							</div>

							<div class="col-span-1 flex items-center gap-x-3">
								<input type="checkbox" name="sports[]" value="Table Tennis" id=""
									class="border border-yellow-500 accent-yellow-500 ">
								<label class="text-lg text-yellow-700" for="">Table Tennis</label>
							</div>

							<div class="col-span-1 flex items-center gap-x-3">
								<input type="checkbox" name="sports[]" value="Taekwondo" id=""
									class="border border-yellow-500 accent-yellow-500 ">
								<label class="text-lg text-yellow-700" for="">Taekwondo</label>
							</div>
						</div>
					</div>

					<div class="flex flex-col gap-y-1">
						<label class="text-yellow-700 font-semibold" for="">Username</label>
						<input type="text" name="username"
							class="p-4 rounded-md outline-yellow-600 border border-yellow-200">
					</div>

					<div class="flex flex-col gap-y-1">
						<label class="text-yellow-700 font-semibold" for="">Password</label>
						<div class="relative flex flex-col">
							<input type="password" name="password"
								class="p-4 rounded-md outline-yellow-600 border border-yellow-200">
							<img id="password-eye" src="../assets/icons/eye-state.svg" alt="" width="32"
								class="absolute right-4 top-1/2 -translate-y-1/2">
						</div>
					</div>
				</div>
				<!-- Coach Form -->
				<div id="coachform" class="hidden flex-col gap-y-4 overflow-y-auto">
					<div class="flex flex-col gap-y-1">
						<label class="text-yellow-700 font-semibold" for="">Given Name</label>
						<input type="text" placeholder="Juan"
							class="p-4 rounded-md outline-yellow-600 border border-yellow-200">
					</div>

					<div class="flex flex-col gap-y-1">
						<label class="text-yellow-700 font-semibold" for="">Middle Name</label>
						<input type="text" placeholder="Santos"
							class="p-4 rounded-md outline-yellow-600 border border-yellow-200">
					</div>

					<div class="flex flex-col gap-y-1">
						<label class="text-yellow-700 font-semibold" for="">Last Name</label>
						<input type="text" placeholder="Dela Cruz"
							class="p-4 rounded-md outline-yellow-600 border border-yellow-200">
					</div>

					<div class="flex flex-col gap-y-1">
						<label class="text-yellow-700 font-semibold" for="">Sex</label>
						<select name="yearlvl" id=""
							class="w-full p-4 rounded-md outline-yellow-600 border border-yellow-200 bg-white">
							<option value="" hidden selected></option>
							<option value="">Male</option>
							<option value="">Female</option>
						</select>
					</div>

					<div class="w-full flex gap-x-4">
						<div class="w-[50%] flex flex-col gap-y-1">
							<label class="text-yellow-700 font-semibold" for="">Suffix</label>
							<input type="text" placeholder="ex. Jr."
								class="p-4 rounded-md outline-yellow-600 border border-yellow-200">
						</div>
						<div class="w-[50%] flex flex-col gap-y-1">
							<label class="text-yellow-700 font-semibold" for="">Date of Birth</label>
							<input type="date"
								class="w-full p-4 rounded-md outline-yellow-600 border border-yellow-200 bg-white">
						</div>
					</div>

					<div class="flex flex-col gap-y-1">
						<label class="text-yellow-700 font-semibold" for="">Institute/Campus</label>
						<select name="" id=""
							class="w-full p-4 rounded-md outline-yellow-600 border border-yellow-200 bg-white">
							<option value=""></option>
						</select>
					</div>

					<div class="flex flex-col gap-y-4">
						<label class="text-yellow-700 font-semibold" for="">Your Sport</span></label>

						<div class="grid grid-cols-3 gap-y-1">
							<div class="col-span-1 flex items-center gap-x-3">
								<input type="checkbox" name="basketball" id=""
									class="border border-yellow-500 accent-yellow-500">
								<label class="text-lg text-yellow-700" for="">Basketball</label>
							</div>

							<div class="col-span-1 flex items-center gap-x-3">
								<input type="checkbox" name="volleyball" id=""
									class="border border-yellow-500 accent-yellow-500">
								<label class="text-lg text-yellow-700" for="">Volleyball</label>
							</div>

							<div class="col-span-1 flex items-center gap-x-3">
								<input type="checkbox" name="arnis" id=""
									class="border border-yellow-500 accent-yellow-500 ">
								<label class="text-lg text-yellow-700" for="">Arnis</label>
							</div>

							<div class="col-span-1 flex items-center gap-x-3">
								<input type="checkbox" name="athletics" id=""
									class="border border-yellow-500 accent-yellow-500 ">
								<label class="text-lg text-yellow-700" for="">Athletics</label>
							</div>

							<div class="col-span-1 flex items-center gap-x-3">
								<input type="checkbox" name="table tennis" id=""
									class="border border-yellow-500 accent-yellow-500 ">
								<label class="text-lg text-yellow-700" for="">Table Tennis</label>
							</div>

							<div class="col-span-1 flex items-center gap-x-3">
								<input type="checkbox" name="taekwondo" id=""
									class="border border-yellow-500 accent-yellow-500 ">
								<label class="text-lg text-yellow-700" for="">Taekwondo</label>
							</div>
						</div>
					</div>

					<div class="flex flex-col gap-y-1">
						<label class="text-yellow-700 font-semibold" for="">Username</label>
						<input type="text"
							class="p-4 rounded-md outline-yellow-600 border border-yellow-200">
					</div>

					<div class="flex flex-col gap-y-1">
						<label class="text-yellow-700 font-semibold" for="">Password</label>
						<div>
							<div class="relative flex flex-col">
								<input type="password" name="password"
									class="p-4 rounded-md outline-yellow-600 border border-yellow-200">
								<img id="password-eye" src="../assets/icons/eye-state.svg" alt="" width="32"
									class="absolute right-4 top-1/2 -translate-y-1/2">
							</div>
						</div>
					</div>
				</div>

				<div class="flex flex-col gap-y-4 items-center mt-4">
					<a class="text-sm text-yellow-700" href="../index.php">Already have an account? <span class="underline text-yellow-500">Log in here</span></a>
					<button id="submitbtn" class="w-full py-4 bg-yellow-600 rounded-full text-lg text-white font-bold">Sign up</button>
				</div>
			</form>
		</div>
	</div>

	<script>
		const formplayerbtn = document.getElementById("playerformbtn");
		const formcoachbtn = document.getElementById("coachformbtn");

		const playerform = document.getElementById("playerform");
		const coachform = document.getElementById("coachform");

		const submitbtn = document.getElementById("submitbtn");

		// Show Player Form
		formplayerbtn.addEventListener("click", () => {
			playerform.classList.remove("hidden");
			playerform.classList.add("flex");

			coachform.classList.remove("flex");
			coachform.classList.add("hidden");

			// Active button style
			formplayerbtn.classList.add(
				"bg-yellow-600",
				"text-yellow-50",
				"rounded-lg"
			);

			formplayerbtn.classList.remove("text-yellow-600");

			// Inactive button style
			formcoachbtn.classList.remove(
				"bg-yellow-600",
				"text-yellow-50",
				"rounded-lg"
			);

			formcoachbtn.classList.add("text-yellow-600");

			submitbtn.setAttribute("name", "signup_player");
		});

		// Show Coach Form
		formcoachbtn.addEventListener("click", () => {
			coachform.classList.remove("hidden");
			coachform.classList.add("flex");

			playerform.classList.remove("flex");
			playerform.classList.add("hidden");

			// Active button style
			formcoachbtn.classList.add(
				"bg-yellow-600",
				"text-yellow-50",
				"rounded-lg"
			);

			formcoachbtn.classList.remove("text-yellow-600");

			// Inactive button style
			formplayerbtn.classList.remove(
				"bg-yellow-600",
				"text-yellow-50",
				"rounded-lg"
			);

			formplayerbtn.classList.add("text-yellow-600");

			submitbtn.setAttribute("name", "signup_coach");
		});

		const eye = document.getElementById("password-eye");
		const password = document.getElementById("password");

		eye.addEventListener("click", () => {
			if (password.type === "password") {
				password.type = "text";
				eye.setAttribute("src", "../assets/icons/eye-clicked.svg");
			} else {
				password.type = "password";
				eye.setAttribute("src", "../assets/icons/eye-state.svg");
			}
		});
	</script>
</body>

</html>