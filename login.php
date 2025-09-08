<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>HR1-Merchandising | Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-800 flex items-center justify-center min-h-screen p-4">
  <div class="bg-white shadow-2xl rounded-xl w-full max-w-md p-8">
    <!-- Brand -->
    <h1 class="text-3xl font-bold text-center mb-6 text-slate-700">HR1-Merchandising</h1>

    <!-- Login Form -->
    <form action="auth.php" method="POST" class="space-y-5">
      <div>
        <label class="block text-slate-600 font-medium mb-1">Email</label>
        <input type="email" name="email" required
          class="w-full p-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-500">
      </div>
      <div>
        <label class="block text-slate-600 font-medium mb-1">Password</label>
        <input type="password" name="password" required
          class="w-full p-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-slate-500">
      </div>
      <button type="submit"
        class="w-full bg-slate-700 text-white p-3 rounded-lg hover:bg-slate-900 transition">
        Login
      </button>
    </form>

    <!-- Register Link -->
    <p class="text-center text-slate-600 mt-6">
      Don't have an account? 
      <a href="register.php" class="text-slate-700 font-semibold hover:underline">Register</a>
    </p>
  </div>
</body>
</html>
