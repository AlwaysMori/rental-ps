<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body class="bg-gray-900 text-white fade-in min-h-screen">
    <div class="container mx-auto p-8">
        <!-- Top Navbar -->
        <nav class="navbar rounded-none fixed-navbar mb-8 flex items-center justify-between">
            <div class="flex items-center gap-4">
            <a href="#calender" class="active hidden">Calender</a>
                <a href="#manage-rooms" >Manage Rooms</a>
                <a href="#manage-consoles">Manage PS Consoles</a>
            </div>
            <div class="flex items-center gap-4 rounded-none">
                <span class="text-gray-300">Hello, {{ auth()->user()->username }}!</span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-none photo-card hover:bg-red-700 transition duration-300">
                        Logout
                    </button>
                </form>
            </div>
        </nav>

        <!-- Main Content -->
        

        <!-- Calendar and Clock Section -->
        <div id="calender" class="bg-gray-800 shadow-lg rounded-none photo-card p-6 mt-16 fade-in" style="animation-delay: 0.4s">
            <div class="text-left mb-6">
                <h2 id="greeting" class="text-2xl font-bold text-blue-400"></h2>
            </div>
            <div class="flex flex-col md:flex-row justify-between rounded-none border-2 border-black-600 photo-card items-center gap-6">
                <!-- Calendar Side -->
                <div class="w-full md:w-1/2">
                    <div class="bg-gray-900 p-4 rounded-lg shadow-md">
                        <h3 class="text-xl font-bold text-center mb-4 text-blue-400" id="monthYear"></h3>
                        <div class="grid grid-cols-7 gap-1 text-center text-gray-400">
                            <div class="font-bold">Sun</div>
                            <div class="font-bold">Mon</div>
                            <div class="font-bold">Tue</div>
                            <div class="font-bold">Wed</div>
                            <div class="font-bold">Thu</div>
                            <div class="font-bold">Fri</div>
                            <div class="font-bold">Sat</div>
                        </div>
                        <div id="calendar" class="grid grid-cols-7 gap-1 mt-2"></div>
                    </div>
                </div>
                <!-- Digital Clock Side -->
                <div class="w-full md:w-1/2 text-center">
                    <div class="bg-gray-900 p-4 rounded-lg shadow-md">
                        <h3 class="text-lg font-bold text-blue-400 mb-4">Current Time</h3>
                        <p id="clock" class="text-4xl font-bold text-white mb-2"></p>
                        <p id="date" class="text-xl text-gray-400"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Manage Rooms Section -->
        <div id="manage-rooms" class="mt-12">
            <h2 class="text-2xl font-bold text-blue-400 mb-4">Manage Rooms</h2>
            <div class="mb-6">
                <form action="/rooms" method="POST" class="flex flex-wrap gap-4">
                    @csrf
                    <input type="text" name="name" placeholder="Room Name" class="flex-1 px-4 py-2 rounded-none photo-card bg-gray-800 text-white focus:ring-2 focus:ring-blue-500" required>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-none photo-card hover:bg-green-700 transition duration-300">
                        Add Room
                    </button>
                </form>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @if(isset($rooms) && $rooms->isNotEmpty())
                @foreach($rooms as $room)
                <div class="photo-card border-2 border-blue-800 p-4 bg-gray-800 text-white rounded-none">
                    <h3 class="text-xl font-bold">{{ $room->name }} 🏠</h3>
                    <p class="text-gray-400">Number of PS Consoles: 🎮 {{ $room->psConsoles->count() }}</p>
                    <div class="mt-4 flex justify-between gap-2">
                        <button type="button" class="px-4 py-2 bg-red-600 text-white rounded-none photo-card hover:bg-red-700 transition duration-300 delete-room-btn" data-room-id="{{ $room->id }}">
                            Delete ❌
                        </button>
                        <button type="button" class="px-4 py-2 bg-blue-600 text-white rounded-none photo-card hover:bg-blue-700 transition duration-300 edit-room-btn" data-room-id="{{ $room->id }}" data-room-name="{{ $room->name }}">
                            Edit ✏️
                        </button>
                    </div>
                </div>
                @endforeach
                @else
                    <p class="text-gray-400">No rooms available.</p>
                @endif
            </div>
        </div>

        <!-- Delete Room Modal -->
        <div id="deleteRoomModal" class="fixed inset-0 bg-black bg-opacity-50 fade-in flex items-center justify-center hidden">
            <div class="bg-gray-800 p-6 rounded-lg shadow-lg w-96">
                <h2 class="text-xl font-bold text-blue-400 mb-4">Confirm Deletion</h2>
                <form id="deleteRoomForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="room_id" id="deleteRoomId">
                    <input type="password" name="password" placeholder="Enter your password" class="w-full px-4 py-2 rounded-none photo-card bg-gray-700 text-white focus:ring-2 focus:ring-red-500 mb-4" required>
                    <div class="flex justify-end gap-4">
                        <button type="button" id="cancelDeleteRoom" class="px-4 py-2 bg-gray-600 text-white rounded-none photo-card hover:bg-gray-700 transition duration-300">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-none photo-card hover:bg-red-700 transition duration-300">
                            Confirm
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Room Modal -->
        <div id="editRoomModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center fade-in fade-out justify-center hidden">
            <div class="bg-gray-800 p-6 rounded-lg shadow-lg w-96">
                <h2 class="text-xl font-bold text-blue-400 mb-4">Edit Room</h2>
                <form id="editRoomForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="room_id" id="editRoomId">
                    <input type="text" name="name" id="editRoomName" placeholder="Room Name" class="w-full px-4 py-2 rounded-none photo-card bg-gray-700 text-white focus:ring-2 focus:ring-blue-500 mb-4" required>
                    <div class="flex justify-end gap-4">
                        <button type="button" id="cancelEditRoom" class="px-4 py-2 bg-gray-600 text-white rounded-none photo-card hover:bg-gray-700 transition duration-300">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-none photo-card hover:bg-blue-700 transition duration-300">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Manage PS Consoles Section -->
        <div id="manage-consoles" class="mt-12">
            <h2 class="text-2xl font-bold text-blue-400 mb-4">Manage PS Consoles</h2>
            <div class="mb-6">
                <form action="/ps" method="POST" class="flex flex-wrap gap-4">
                    @csrf
                    <select name="room_id" class="flex-1 px-4 py-2 rounded-none photo-card bg-gray-800 text-white focus:ring-2 focus:ring-blue-500" required>
                        <option value="" disabled selected>Select Room</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}">{{ $room->name }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="name" placeholder="PS Console Name" class="flex-1 px-4 py-2 rounded-none photo-card bg-gray-800 text-white focus:ring-2 focus:ring-blue-500" required>
                    <select name="status" class="flex-1 px-4 py-2 rounded-none photo-card bg-gray-800 text-white focus:ring-2 focus:ring-blue-500" required>
                        <option value="active">Active</option>
                        <option value="damaged">Damaged</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-none photo-card hover:bg-green-700 transition duration-300">
                        Add PS Console
                    </button>
                </form>
            </div>
            <div class="space-y-8">
                @foreach($rooms as $room)
                    <div>
                        <h3 class="text-xl font-bold text-blue-300">{{ $room->name }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-4">
                            @forelse($room->psConsoles as $ps)
                                <div class="photo-card border-2 border-blue-800 p-4 bg-gray-800 text-white rounded-none">
                                    <h3 class="text-xl font-bold">{{ $ps->name }} ({{ ucfirst($ps->status) }}) 🎮</h3>
                                    <p class="text-gray-400">Room: {{ $room->name }} 🏠</p>
                                    <div class="mt-4">
                                        <p class="text-gray-400 text-lg font-semibold">Remaining Time: 
                                            <span id="timer-{{ $ps->id }}" class="text-3xl fade-in font-bold text-yellow-400"></span>
                                        </p>
                                    </div>
                                    <div class="mt-4 fade-in flex justify-between">
                                        <button type="button" id="set-timer-btn-{{ $ps->id }}" class="px-4 fade-in py-2 bg-blue-600 text-white rounded-none photo-card hover:bg-blue-700 transition duration-300 set-timer-btn" data-ps-id="{{ $ps->id }}">
                                            Set Timer ⏱️
                                        </button>
                                        <button type="button" id="edit-timer-btn-{{ $ps->id }}" class="px-4 py-2 fade-in bg-green-600 text-white rounded-none photo-card hover:bg-green-700 transition duration-300 edit-timer-btn hidden" data-ps-id="{{ $ps->id }}">
                                            Edit Timer ✏️
                                        </button>
                                        <button type="button" id="delete-timer-btn-{{ $ps->id }}" class="px-4 fade-in py-2 bg-red-600 text-white rounded-none photo-card hover:bg-red-700 transition duration-300 delete-timer-btn hidden" data-ps-id="{{ $ps->id }}">
                                            Delete Timer ❌
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-400">No PS consoles available in this room.</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Timer Modal -->
    <div id="timerModal" class="fixed inset-0 bg-black fade-in bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-gray-800 p-6 rounded-lg shadow-lg w-96">
            <h2 class="text-xl font-bold text-blue-400 mb-4">Set Timer</h2>
            <form id="timerForm">
                <div class="flex gap-4 mb-4">
                    <input type="number" id="hours" placeholder="Hours" class="w-1/3 px-4 py-2 fade-in rounded-none photo-card bg-gray-700 text-white focus:ring-2 focus:ring-blue-500" min="0">
                    <input type="number" id="minutes" placeholder="Minutes" class="w-1/3 px-4 py-2 fade-in rounded-none photo-card bg-gray-700 text-white focus:ring-2 focus:ring-blue-500" min="0" max="59">
                    <input type="number" id="seconds" placeholder="Seconds" class="w-1/3 px-4 py-2 fade-in rounded-none photo-card bg-gray-700 text-white focus:ring-2 focus:ring-blue-500" min="0" max="59">
                </div>
                <input type="hidden" id="currentPsId">
                <div class="flex justify-end gap-4">
                    <button type="button" id="cancelTimer" class="px-4 py-2 bg-gray-600 text-white rounded-none photo-card hover:bg-gray-700 transition duration-300">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-none photo-card hover:bg-blue-700 transition duration-300">
                        Confirm
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Timer Modal -->
    <div id="deleteTimerModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center fade-in justify-center hidden">
        <div class="bg-gray-800 p-6 rounded-lg shadow-lg w-96">
            <h2 class="text-xl font-bold text-blue-400 mb-4">Confirm Timer Deletion</h2>
            <form id="deleteTimerForm" method="POST">
                @csrf
                @method('DELETE')
                <input type="hidden" name="ps_id" id="deleteTimerPsId">
                <input type="password" name="password" placeholder="Enter your password" class="w-full px-4 py-2 rounded-none photo-card bg-gray-700 text-white focus:ring-2 focus:ring-red-500 mb-4" required>
                <div class="flex justify-end gap-4">
                    <button type="button" id="cancelDeleteTimer" class="px-4 py-2 bg-gray-600 text-white rounded-none photo-card hover:bg-gray-700 transition duration-300">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-none photo-card hover:bg-red-700 transition duration-300">
                        Confirm
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const monthNamesID = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        const dayNamesLongID = [
            'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'
        ];

        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        function updateTime() {
            const now = new Date();
            const hours = now.getHours();
            const time = now.toLocaleTimeString('id-ID', { hour12: false });
            const date = `${dayNamesLongID[now.getDay()]}, ${now.getDate()} ${monthNamesID[now.getMonth()]} ${now.getFullYear()}`;
            document.getElementById('clock').textContent = time;
            document.getElementById('date').textContent = date;

            // Update greeting based on time
            let greeting = '';
            const username = capitalizeFirstLetter("{{ auth()->user()->username }}");
            if (hours >= 4 && hours < 12) {
                greeting = `Selamat Pagi, ${username}! 😁☀️`;
            } else if (hours >= 12 && hours < 15) {
                greeting = `Selamat Siang, ${username}! 😎🌤️`;
            } else if (hours >= 15 && hours < 18) {
                greeting = `Selamat Sore, ${username}! 🥳🌅`;
            } else if (hours >= 18 || hours < 4) {
                greeting = `Selamat Malam, ${username}! 😌🌙`;
            }
            document.getElementById('greeting').textContent = greeting;
        }

        function generateCalendar() {
            const now = new Date();
            const month = now.getMonth();
            const year = now.getFullYear();
            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            document.getElementById('monthYear').textContent = `${monthNamesID[month]} ${year}`;
            const calendar = document.getElementById('calendar');
            calendar.innerHTML = '';

            for (let i = 0; i < firstDay; i++) {
                calendar.innerHTML += '<div class="p-2"></div>';
            }

            for (let day = 1; day <= daysInMonth; day++) {
                const isToday = day === now.getDate() && month === now.getMonth() && year === now.getFullYear();
                calendar.innerHTML += `<div class="p-2 text-center ${isToday ? 'bg-blue-500 text-white rounded-full' : 'text-gray-300'}">${day}</div>`;
            }
        }

        // Store active timer intervals
        const activeTimers = {};

        function startTimer(duration, displayId, psId) {
            // Clear any existing timer for this PS Console
            if (activeTimers[psId]) {
                clearInterval(activeTimers[psId]);
            }

            let timer = duration, hours, minutes, seconds;

            activeTimers[psId] = setInterval(() => {
                hours = Math.floor(timer / 3600);
                minutes = Math.floor((timer % 3600) / 60);
                seconds = timer % 60;

                document.getElementById(displayId).textContent =
                    `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                document.getElementById(displayId).style.fontFamily = "'Courier New', Courier, monospace";
                document.getElementById(displayId).style.fontSize = "2rem";
                document.getElementById(displayId).style.color = "#FACC15"; // Yellow color
                document.getElementById(displayId).style.letterSpacing = "0.15rem";

                if (--timer < 0) {
                    clearInterval(activeTimers[psId]);
                    delete activeTimers[psId];
                    document.getElementById(displayId).textContent = "Time's up!";
                    document.getElementById(`set-timer-btn-${psId}`).classList.remove('hidden');
                    document.getElementById(`edit-timer-btn-${psId}`).classList.add('hidden');
                    document.getElementById(`delete-timer-btn-${psId}`).classList.add('hidden');

                    // Send a request to reset the timer in the database
                    fetch(`/ps/${psId}/timer`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({ timer: null }),
                    }).catch(error => console.error('Error resetting timer:', error));
                } else {
                    // Update the database every second
                    fetch(`/ps/${psId}/timer`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({ timer }),
                    }).catch(error => console.error('Error updating timer:', error));
                }
            }, 1000);

            // Show the "Delete Timer" button
            document.getElementById(`delete-timer-btn-${psId}`).classList.remove('hidden');
        }

        function fetchAndStartTimer(psId, displayId) {
            fetch(`/ps/${psId}/timer`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.timer > 0) {
                    startTimer(data.timer, displayId, psId); // Resume the timer
                    document.getElementById(`set-timer-btn-${psId}`).classList.add('hidden');
                    document.getElementById(`edit-timer-btn-${psId}`).classList.remove('hidden');
                }
            })
            .catch(error => console.error('Error fetching timer:', error));
        }

        document.addEventListener('DOMContentLoaded', () => {
            updateTime();
            generateCalendar();
            setInterval(updateTime, 1000);

            // Smooth scroll for navbar links
            document.querySelectorAll('.navbar a').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();

                    // Remove 'active' class from all links
                    document.querySelectorAll('.navbar a').forEach(link => link.classList.remove('active'));

                    // Add 'active' class to the clicked link
                    this.classList.add('active');

                    // Smooth scroll to the target section
                    const targetId = this.getAttribute('href').substring(1);
                    const targetElement = document.getElementById(targetId);
                    if (targetElement) {
                        window.scrollTo({
                            top: targetElement.offsetTop - document.querySelector('.navbar').offsetHeight, // Adjust for navbar height
                            behavior: 'smooth'
                        });
                    }
                });
            });

            // Edit Room Modal functionality
            const editRoomModal = document.getElementById('editRoomModal');
            const editRoomForm = document.getElementById('editRoomForm');
            const editRoomId = document.getElementById('editRoomId');
            const editRoomName = document.getElementById('editRoomName');
            const cancelEditRoom = document.getElementById('cancelEditRoom');

            document.querySelectorAll('.edit-room-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const roomId = button.getAttribute('data-room-id');
                    const roomName = button.getAttribute('data-room-name');

                    editRoomId.value = roomId;
                    editRoomName.value = roomName;
                    editRoomForm.action = `/rooms/${roomId}`;
                    editRoomModal.classList.remove('hidden');
                    editRoomModal.classList.add('fade-in');
                });
            });

            cancelEditRoom.addEventListener('click', () => {
                editRoomModal.classList.remove('fade-in');
                editRoomModal.classList.add('fade-out');
                setTimeout(() => {
                    editRoomModal.classList.add('hidden');
                    editRoomModal.classList.remove('fade-out');
                }, 500); // Match the CSS transition duration
            });

            // Delete Room Modal functionality
            const deleteRoomModal = document.getElementById('deleteRoomModal');
            const deleteRoomForm = document.getElementById('deleteRoomForm');
            const deleteRoomId = document.getElementById('deleteRoomId');
            const cancelDeleteRoom = document.getElementById('cancelDeleteRoom');

            document.querySelectorAll('.delete-room-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const roomId = button.getAttribute('data-room-id');
                    deleteRoomId.value = roomId;
                    deleteRoomForm.action = `/rooms/${roomId}`;
                    deleteRoomModal.classList.remove('hidden');
                });
            });

            cancelDeleteRoom.addEventListener('click', () => {
                deleteRoomModal.classList.add('hidden');
            });

            deleteRoomForm.addEventListener('submit', (e) => {
                if (!deleteRoomForm.password.value) {
                    e.preventDefault();
                    alert('Please enter your password to confirm deletion.');
                }
            });

            // Timer Modal functionality
            const timerModal = document.getElementById('timerModal');
            const timerForm = document.getElementById('timerForm');
            const cancelTimer = document.getElementById('cancelTimer');
            const hoursInput = document.getElementById('hours');
            const minutesInput = document.getElementById('minutes');
            const secondsInput = document.getElementById('seconds');
            const currentPsIdInput = document.getElementById('currentPsId');

            // Attach event listeners to "Set Timer" and "Edit Timer" buttons
            document.querySelectorAll('.set-timer-btn, .edit-timer-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const psId = button.getAttribute('data-ps-id');
                    currentPsIdInput.value = psId; // Set the current PS Console ID
                    timerModal.classList.remove('hidden'); // Show the modal
                });
            });

            // Cancel button functionality
            cancelTimer.addEventListener('click', () => {
                timerModal.classList.add('hidden'); // Hide the modal
                hoursInput.value = ''; // Reset input fields
                minutesInput.value = '';
                secondsInput.value = '';
            });

            // Timer form submission
            timerForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const psId = currentPsIdInput.value;
                const hours = parseInt(hoursInput.value) || 0;
                const minutes = parseInt(minutesInput.value) || 0;
                const seconds = parseInt(secondsInput.value) || 0;
                const totalSeconds = (hours * 3600) + (minutes * 60) + seconds;

                if (totalSeconds > 0) {
                    // Send timer to backend
                    fetch(`/ps/${psId}/timer`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({ timer: totalSeconds }),
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Failed to update timer.');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            startTimer(totalSeconds, `timer-${psId}`, psId); // Start the timer
                            document.getElementById(`set-timer-btn-${psId}`).classList.add('hidden'); // Hide "Set Timer" button
                            document.getElementById(`edit-timer-btn-${psId}`).classList.remove('hidden'); // Show "Edit Timer" button
                            timerModal.classList.add('hidden'); // Hide the modal
                            hoursInput.value = ''; // Reset input fields
                            minutesInput.value = '';
                            secondsInput.value = '';
                        } else {
                            alert(data.message || 'Failed to update timer.');
                        }
                    })
                    .catch(error => alert(error.message));
                } else {
                    alert("Please enter a valid time.");
                }
            });

            // Attach event listeners to "Delete Timer" buttons
            const deleteTimerModal = document.getElementById('deleteTimerModal');
            const deleteTimerForm = document.getElementById('deleteTimerForm');
            const deleteTimerPsId = document.getElementById('deleteTimerPsId');
            const cancelDeleteTimer = document.getElementById('cancelDeleteTimer');

            document.querySelectorAll('.delete-timer-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const psId = button.getAttribute('data-ps-id');
                    deleteTimerPsId.value = psId; // Set the PS Console ID
                    deleteTimerModal.classList.remove('hidden'); // Show the modal
                });
            });

            // Cancel button functionality
            cancelDeleteTimer.addEventListener('click', () => {
                deleteTimerModal.classList.add('hidden'); // Hide the modal
            });

            // Delete Timer form submission
            deleteTimerForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const psId = deleteTimerPsId.value;
                const password = deleteTimerForm.password.value;

                // Send a request to delete the timer
                fetch(`/ps/${psId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ password }), // Include the password in the request
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Invalid password or failed to delete timer.');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Clear the timer
                        if (activeTimers[psId]) {
                            clearInterval(activeTimers[psId]);
                            delete activeTimers[psId];
                        }

                        // Reset the timer display
                        document.getElementById(`timer-${psId}`).textContent = "00:00:00";

                        // Hide "Edit Timer" and "Delete Timer" buttons, show "Set Timer" button
                        document.getElementById(`set-timer-btn-${psId}`).classList.remove('hidden');
                        document.getElementById(`edit-timer-btn-${psId}`).classList.add('hidden');
                        document.getElementById(`delete-timer-btn-${psId}`).classList.add('hidden');

                        // Hide the modal
                        deleteTimerModal.classList.add('hidden');
                        deleteTimerForm.reset(); // Reset the form fields
                    } else {
                        alert(data.message || 'Failed to delete timer.');
                    }
                })
                .catch(error => alert(error.message));
            });

            @foreach($rooms as $room)
                @foreach($room->psConsoles as $ps)
                    fetchAndStartTimer({{ $ps->id }}, `timer-{{ $ps->id }}`);
                @endforeach
            @endforeach
        });
    </script>
</body>
</html>
