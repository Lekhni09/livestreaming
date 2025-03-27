<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Meeting</title>
    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen relative">
    <!-- Navbar -->
    <nav style="position: absolute; top: 16px; right: 16px;">
        <li style="position: relative; list-style: none;">
            <a id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false"
                style="background-color: white; padding: 8px 16px; border-radius: 8px; box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2); display: inline-block; text-decoration: none; color: black;">
                {{ Auth::user()->name }}
            </a>

            <div style="position: absolute; right: 0; margin-top: 8px; width: 180px; background-color: white; border: 1px solid #ddd; border-radius: 8px; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); z-index: 50; display: none;"
                id="dropdownMenu">
                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    style="display: block; padding: 10px 16px; color: #333; text-decoration: none; cursor: pointer; border-radius: 6px;">
                    {{ __('Logout') }}
                </a>
            </div>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </li>
    </nav>

    <!-- Meeting Box -->
    <div class="bg-white p-6 rounded-lg shadow-lg w-96">
        <h2 class="text-xl font-semibold text-center text-gray-700 mb-4">Meeting Room</h2>

        <!-- Meeting Link Input -->
        <input type="text" id="linkUrl"
            class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
            placeholder="Enter or generate a meeting link">

        <!-- Buttons -->
        <div class="flex flex-col sm:flex-row justify-between mt-4 space-y-2 sm:space-y-0 sm:space-x-4">
            @if (Auth::user())
                <a href="{{ url('createMeeting') }}"
                    class="w-full sm:w-1/2 text-center bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                    Create Meeting
                </a>
            @endif

            <button id="join-btn2" onclick="joinUserMeeting()"
                class="w-full sm:w-1/2 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                Join Meeting
            </button>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        function joinUserMeeting() {
            var link = $('#linkUrl').val();
            if (link.trim() == "" || link.length < 1) {
                alert("Please enter a meeting link!");
            } else {
                window.location.href = link;
            }
        }
    </script>

    <script>
        document.getElementById('navbarDropdown').addEventListener('click', function(event) {
            event.preventDefault();
            var dropdown = document.getElementById('dropdownMenu');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        });

        document.addEventListener('click', function(event) {
            var dropdown = document.getElementById('dropdownMenu');
            if (!event.target.closest('#navbarDropdown')) {
                dropdown.style.display = 'none';
            }
        });
    </script>
</body>


</html>
