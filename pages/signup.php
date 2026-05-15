<?php
session_start();
?>
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
			<?php
			if (isset($_SESSION["error"])) {
				echo '<div class="w-full p-3 bg-red-100 border border-red-300 text-red-700 rounded-lg text-sm">' . htmlspecialchars($_SESSION["error"]) . '</div>';
				unset($_SESSION["error"]);
			}
			if (isset($_SESSION["success"])) {
				echo '<div class="w-full p-3 bg-green-100 border border-green-300 text-green-700 rounded-lg text-sm">' . htmlspecialchars($_SESSION["success"]) . '</div>';
				unset($_SESSION["success"]);
			}
			?>

			<div class="sticky top-0 flex w-full border border-yellow-200 rounded-xl bg-yellow-50 p-1">
				<button id="playerformbtn" type="button" class="flex gap-2 items-center justify-center w-[50%] py-3 bg-yellow-600 rounded-lg text-yellow-50 font-bold"><img src="../assets/icons/player.svg" alt="" width="24"> Player</button>
				<button id="coachformbtn" type="button" class="flex gap-2 items-center justify-center w-[50%] py-3 text-yellow-600 font-bold"><img src="../assets/icons/coach.svg" alt="" width="24"> Coach</button>
			</div>

			<form action="../assets/backend/functions/signup.php" method="POST" id="playerform"
				class="flex relative flex-col rounded-xl h-[85%]">
				<!-- Player Form -->
				<div class="flex flex-col gap-y-4 overflow-y-auto">
					<div class="flex flex-col gap-y-1">
						<label class="text-yellow-700 font-semibold" for="">Given Name</label>
						<input name="givenname_player" type="text" placeholder="Juan" required
							class="p-4 rounded-md outline-yellow-600 border border-yellow-200">
					</div>

					<div class="flex flex-col gap-y-1">
						<label class="text-yellow-700 font-semibold" for="">Middle Name</label>
						<input name="middlename_player" type="text" placeholder="Santos"
							class="p-4 rounded-md outline-yellow-600 border border-yellow-200">
					</div>

					<div class="flex flex-col gap-y-1">
						<label class="text-yellow-700 font-semibold" for="">Last Name</label>
						<input name="lastname_player" type="text" placeholder="Dela Cruz" required
							class="p-4 rounded-md outline-yellow-600 border border-yellow-200">
					</div>

					<div class="w-full flex gap-x-4">
						<div class="w-[50%] flex flex-col gap-y-1">
							<label class="text-yellow-700 font-semibold" for="">Contact Number</label>
							<input name="contactnumber" type="text" required
								class="p-4 rounded-md outline-yellow-600 border border-yellow-200">
						</div>
						<div class="w-[50%] flex flex-col gap-y-1">
							<label class="text-yellow-700 font-semibold" for="">Sex</label>
							<select name="sex_player" id="" required
								class="w-full p-4 rounded-md outline-yellow-600 border border-yellow-200 bg-white">
								<option value="" hidden selected></option>
								<option value="Male">Male</option>
								<option value="Female">Female</option>
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
							<input name="suffix_player" type="text" placeholder="ex. Jr."
								class="p-4 rounded-md outline-yellow-600 border border-yellow-200">
						</div>
						<div class="w-[50%] flex flex-col gap-y-1">
							<label class="text-yellow-700 font-semibold" for="">Date of Birth</label>
							<input name="dob_player" type="date" required
								class="w-full p-4 rounded-md outline-yellow-600 border border-yellow-200 bg-white">
						</div>
					</div>

					<div class="w-full flex gap-x-4">
						<div class="w-[50%] flex flex-col gap-y-1">
							<label class="text-yellow-700 font-semibold" for="">Student ID</label>
							<input name="studentid" type="text" required
								class="p-4 rounded-md outline-yellow-600 border border-yellow-200">
						</div>
						<div class="w-[50%] flex flex-col gap-y-1">
							<label class="text-yellow-700 font-semibold" for="">Year Level</label>
							<select name="yearlvl" id="" required
								class="w-full p-4 rounded-md outline-yellow-600 border border-yellow-200 bg-white">
								<option value="" hidden selected></option>
								<option value="1st Year">1st Year</option>
								<option value="2nd Year">2nd Year</option>
								<option value="3rd Year">3rd Year</option>
								<option value="4th Year">4th Year</option>
							</select>
						</div>
					</div>

					<div class="flex flex-col gap-y-1">
						<label class="text-yellow-700 font-semibold" for="">Institute/Campus</label>
						<select name="inscam_player" id="" required
							class="w-full p-4 rounded-md outline-yellow-600 border border-yellow-200 bg-white">
							<option value="" hidden></option>
							<option value="Balagtas Technical Vocational College">Balagtas Technical Vocational College</option>
							<option value="College Of Agriculture">College Of Agriculture</option>
							<option value="College Of Education">College Of Education</option>
							<option value="College Of Engineering And Technology">College Of Engineering And Technology</option>
							<option value="College Of Management">College Of Management</option>
							<option value="Fortunato F. Halili National Agricultural School">Fortunato F. Halili National Agricultural School</option>
							<option value="Institute Of Arts And Sciences">Institute Of Arts And Sciences</option>
							<option value="Insitute Of Computer Studies">Insitute Of Computer Studies</option>
							<option value="Institute Of Veterinary Medicine">Institute Of Veterinary Medicine</option>
						</select>
					</div>

					<div class="flex flex-col gap-y-4">
						<label class="text-yellow-700 font-semibold" for="">Your Sport/s <span class="text-sm text-yellow-700/50">(Choose your sport/s)</span></label>

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
								<input type="checkbox" name="sports[]" value="Athletics" id=""
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
						<input type="text" name="username_player" required
							class="p-4 rounded-md outline-yellow-600 border border-yellow-200">
					</div>

					<div class="flex flex-col gap-y-1">
						<label class="text-yellow-700 font-semibold" for="">Password</label>
						<div class="relative flex flex-col">
							<input id="password-player" type="password" name="password_player" required
								class="p-4 rounded-md outline-yellow-600 border border-yellow-200">
							<img id="password-eye-player" src="../assets/icons/eye-state.svg" alt="" width="32"
								class="absolute right-4 top-1/2 -translate-y-1/2">
						</div>

					</div>
				</div>
				<div class="flex flex-col gap-y-4 items-center mt-4">
					<a class="text-sm text-yellow-700" href="../index.php">Already have an account? <span class="underline text-yellow-600">Log in here</span></a>
					<button type="submit" id="submitplayerbtn" name="signup_player" class="flex items-center justify-center gap-x-2 w-full py-4 bg-yellow-600 rounded-full text-lg text-white font-bold">Sign up <img src="../assets/icons/login.svg" alt="" width="20"></button>
				</div>
			</form>

			<form action="../assets/backend/functions/signup.php" method="POST" id="coachform"
				class="hidden relative flex-col gap-y-4 rounded-xl h-[85%]">
				<!-- Coach Form -->
				<div class="flex flex-col gap-y-4 overflow-y-auto">
					<div class="flex flex-col gap-y-1">
						<label class="text-yellow-700 font-semibold" for="">Given Name</label>
						<input type="text" placeholder="Juan" name="givenname_coach" required
							class="p-4 rounded-md outline-yellow-600 border border-yellow-200">
					</div>

					<div class="flex flex-col gap-y-1">
						<label class="text-yellow-700 font-semibold" for="">Middle Name</label>
						<input type="text" placeholder="Santos" name="middlename_coach"
							class="p-4 rounded-md outline-yellow-600 border border-yellow-200">
					</div>

					<div class="flex flex-col gap-y-1">
						<label class="text-yellow-700 font-semibold" for="">Last Name</label>
						<input type="text" placeholder="Dela Cruz" name="lastname_coach" required
							class="p-4 rounded-md outline-yellow-600 border border-yellow-200">
					</div>

					<div class="flex flex-col gap-y-1">
						<label class="text-yellow-700 font-semibold" for="">Sex</label>
						<select name="sex_coach" id="" required
							class="w-full p-4 rounded-md outline-yellow-600 border border-yellow-200 bg-white">
							<option value="" hidden selected></option>
							<option value="Male">Male</option>
							<option value="Female">Female</option>
						</select>
					</div>

					<div class="w-full flex gap-x-4">
						<div class="w-[50%] flex flex-col gap-y-1">
							<label class="text-yellow-700 font-semibold" for="">Suffix</label>
							<input type="text" placeholder="ex. Jr." name="suffix_coach"
								class="p-4 rounded-md outline-yellow-600 border border-yellow-200">
						</div>
						<div class="w-[50%] flex flex-col gap-y-1">
							<label class="text-yellow-700 font-semibold" for="">Date of Birth</label>
							<input type="date" name="dob_coach" required
								class="w-full p-4 rounded-md outline-yellow-600 border border-yellow-200 bg-white">
						</div>
					</div>

					<div class="flex flex-col gap-y-1">
						<label class="text-yellow-700 font-semibold" for="">Institute/Campus</label>
						<select name="inscam_coach" id="" required
							class="w-full p-4 rounded-md outline-yellow-600 border border-yellow-200 bg-white">
							<option value="" hidden></option>
							<option value="Balagtas Technical Vocational College">Balagtas Technical Vocational College</option>
							<option value="College Of Agriculture">College Of Agriculture</option>
							<option value="College Of Education">College Of Education</option>
							<option value="College Of Engineering And Technology">College Of Engineering And Technology</option>
							<option value="College Of Management">College Of Management</option>
							<option value="Fortunato F. Halili National Agricultural School">Fortunato F. Halili National Agricultural School</option>
							<option value="Institute Of Arts And Sciences">Institute Of Arts And Sciences</option>
							<option value="Insitute Of Computer Studies">Insitute Of Computer Studies</option>
							<option value="Institute Of Veterinary Medicine">Institute Of Veterinary Medicine</option>
						</select>
					</div>

					<div class="flex flex-col gap-y-4">
						<label class="text-yellow-700 font-semibold" for="">Your Sport</span></label>

						<div class="grid grid-cols-3 gap-y-1">
							<div class="flex items-center gap-x-3">
								<input type="radio" name="sport" value="Basketball" class="accent-yellow-500">
								<label class="text-lg text-yellow-700">Basketball</label>
							</div>

							<div class="flex items-center gap-x-3">
								<input type="radio" name="sport" value="Volleyball" class="accent-yellow-500">
								<label class="text-lg text-yellow-700">Volleyball</label>
							</div>

							<div class="flex items-center gap-x-3">
								<input type="radio" name="sport" value="Arnis" class="accent-yellow-500">
								<label class="text-lg text-yellow-700">Arnis</label>
							</div>

							<div class="flex items-center gap-x-3">
								<input type="radio" name="sport" value="Athletics" class="accent-yellow-500">
								<label class="text-lg text-yellow-700">Athletics</label>
							</div>

							<div class="flex items-center gap-x-3">
								<input type="radio" name="sport" value="Table Tennis" class="accent-yellow-500">
								<label class="text-lg text-yellow-700">Table Tennis</label>
							</div>

							<div class="flex items-center gap-x-3">
								<input type="radio" name="sport" value="Taekwondo" class="accent-yellow-500">
								<label class="text-lg text-yellow-700">Taekwondo</label>
							</div>
						</div>
					</div>

					<div class="flex flex-col gap-y-1">
						<label class="text-yellow-700 font-semibold" for="">Username</label>
						<input type="text" name="username_coach" required
							class="p-4 rounded-md outline-yellow-600 border border-yellow-200">
					</div>

					<div class="flex flex-col gap-y-1">
						<label class="text-yellow-700 font-semibold" for="">Password</label>
						<div class="relative flex flex-col">
							<input id="password-coach" type="password" name="password_coach" required
								class="p-4 rounded-md outline-yellow-600 border border-yellow-200">
							<img id="password-eye-coach" src="../assets/icons/eye-state.svg" alt="" width="32"
								class="absolute right-4 top-1/2 -translate-y-1/2">
						</div>
					</div>
				</div>

				<div class="flex flex-col gap-y-4 items-center mt-4">
					<a class="text-sm text-yellow-700" href="../index.php">Already have an account? <span class="underline text-yellow-600">Log in here</span></a>
					<button type="submit" id="submitcoachbtn" name="signup_coach" class="flex items-center justify-center gap-x-2 w-full py-4 bg-yellow-600 rounded-full text-lg text-white font-bold">Sign up <img src="../assets/icons/login.svg" alt="" width="20"></button>
				</div>
			</form>
		</div>
	</div>

	<script>
		const formplayerbtn = document.getElementById("playerformbtn");
		const formcoachbtn = document.getElementById("coachformbtn");

		const subplayerbtn = document.getElementById("submitplayerbtn");
		const subcoachbtn = document.getElementById("submitcoachbtn");

		const playerform = document.getElementById("playerform");
		const coachform = document.getElementById("coachform");



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

			subplayerbtn.classList.remove("hidden");
			subcoachbtn.classList.add("hidden");
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

			subcoachbtn.classList.remove("hidden");
			subplayerbtn.classList.add("hidden");
		});
		// Password
		const eyecoach = document.getElementById("password-eye-coach");
		const eyeplayer = document.getElementById("password-eye-player");
		const passwordcoach = document.getElementById("password-coach");
		const passwordplayer = document.getElementById("password-player");

		eyecoach.addEventListener("click", () => {
			if (passwordcoach.type === "password") {
				passwordcoach.type = "text";
				eyecoach.setAttribute("src", "../assets/icons/eye-clicked.svg");
			} else {
				passwordcoach.type = "password";
				eyecoach.setAttribute("src", "../assets/icons/eye-state.svg");
			}
		});

		eyeplayer.addEventListener("click", () => {
			if (passwordplayer.type === "password") {
				passwordplayer.type = "text";
				eyeplayer.setAttribute("src", "../assets/icons/eye-clicked.svg");
			} else {
				passwordplayer.type = "password";
				eyeplayer.setAttribute("src", "../assets/icons/eye-state.svg");
			}
		});
	</script>
</body>

</html>