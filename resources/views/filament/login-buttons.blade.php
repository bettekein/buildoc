@if (app()->isLocal())
    <div class="mt-4 p-4 border rounded-lg bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700">
        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Development Login</h3>
        <div class="grid grid-cols-2 gap-2">
            <button type="button" onclick="fillLogin('admin@buildoc.test', 'password')"
                class="px-3 py-2 text-xs font-medium text-white bg-red-600 rounded hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                Super Admin
            </button>
            <button type="button" onclick="fillLogin('user@buildoc.test', 'password')"
                class="px-3 py-2 text-xs font-medium text-white bg-blue-600 rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Tenant User
            </button>
        </div>
    </div>

    <script>
        function fillLogin(email, password) {
            const emailInput = document.querySelector('input[type="email"]');
            const passwordInput = document.querySelector('input[type="password"]');
            
            if (emailInput) {
                emailInput.value = email;
                emailInput.dispatchEvent(new Event('input', { bubbles: true }));
            }
            
            if (passwordInput) {
                passwordInput.value = password;
                passwordInput.dispatchEvent(new Event('input', { bubbles: true }));
            }
        }
    </script>
@endif