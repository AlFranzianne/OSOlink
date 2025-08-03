<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>OSOlink | Home</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center min-h-screen">
    @auth
    {{-- Dashboard --}}
    <nav class="fixed top-0 left-0 w-full bg-white shadow z-50 h-16 flex items-center px-8">
        <div class="flex-1 flex items-center">
            <span class="text-2xl font-bold text-red-700 tracking-wide">OSOlink</span>
        </div>
        <div class="flex items-center space-x-4">
            <span class="text-gray-700 font-medium whitespace-nowrap">Welcome, {{ auth()->user()->name }}!</span>
            <form action="/logout" method="POST">
                @csrf
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition font-semibold">
                    Logout
                </button>
            </form>
        </div>
    </nav>
    <div class="pt-20 px-10 w-full max-w-5xl">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-red-700">Projects</h2>
            <button id="addTaskBtn" class="px-4 py-2 bg-red-600 text-white rounded shadow hover:bg-red-700 transition font-semibold">
                + Add Task
            </button>
        </div>

        {{-- Task List --}}
        <ul class="space-y-3">
            <li class="bg-gray-200 px-4 py-3 rounded flex justify-between font-semibold text-red-700">
                <span class="w-1/2">Task Name</span>
                <span class="w-1/2 text-gray-700">Description</span>
            </li>
            @foreach($tasks as $task)
            <li class="bg-white shadow px-4 py-3 rounded flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex flex-col md:flex-row md:items-center gap-4 w-full">
                    <span class="w-full md:w-1/2 font-semibold text-red-700">{{ $task->name }}</span>
                    <span class="w-full md:w-1/2 text-gray-700">{{ $task->description }}</span>
                </div>
                <div class="flex gap-2">
                    <button onclick="openEditTaskModal({{ $task->id }}, '{{ addslashes($task->name) }}', '{{ addslashes($task->description) }}')" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition text-sm font-semibold">Edit</button>
                    <form action="/delete-task/{{ $task->id }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition text-sm font-semibold">Delete</button>
                    </form>
                </div>
            </li>
            @endforeach
        </ul>

        {{-- Add Task Modal --}}
        <div id="addTaskModal" class="fixed inset-0 bg-black bg-opacity-40 z-50 hidden">
            <div class="bg-white rounded-xl shadow-xl p-8 w-full max-w-md">
                <h3 class="text-xl font-bold mb-6 text-red-700">Add Task</h3>
                <form action="/add-task" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-2 text-red-700">Name</label>
                        <input type="text" name="name" required placeholder="My Task"
                            class="w-full px-4 py-2 border-2 border-red-300 rounded focus:outline-none focus:ring-2 focus:ring-red-400">
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-semibold mb-2 text-red-700">Description</label>
                        <input type="text" name="description" required placeholder="To Do"
                            class="w-full px-4 py-2 border-2 border-red-300 rounded focus:outline-none focus:ring-2 focus:ring-red-400">
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" id="closeAddTaskModalBtn"
                            class="px-4 py-2 bg-gray-200 text-red-700 font-semibold rounded hover:bg-gray-300 transition">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white font-semibold rounded hover:bg-red-700 transition">Save</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Edit Task Modal --}}
        <div id="editTaskModal" class="fixed inset-0 bg-black bg-opacity-40 z-50 hidden">
            <div class="bg-white rounded-xl shadow-xl p-8 w-full max-w-md">
                <h3 class="text-xl font-bold mb-6 text-red-700">Edit Task</h3>
                <form id="editTaskForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-2 text-red-700">Name</label>
                        <input type="text" name="name" id="editTaskName" required
                            class="w-full px-4 py-2 border-2 border-red-300 rounded focus:outline-none focus:ring-2 focus:ring-red-400">
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-semibold mb-2 text-red-700">Description</label>
                        <input type="text" name="description" id="editTaskDescription" required
                            class="w-full px-4 py-2 border-2 border-red-300 rounded focus:outline-none focus:ring-2 focus:ring-red-400">
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" id="closeEditTaskModalBtn"
                            class="px-4 py-2 bg-gray-200 text-red-700 font-semibold rounded hover:bg-gray-300 transition">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white font-semibold rounded hover:bg-red-700 transition">Update</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Scripts --}}
        <script>
            const addTaskBtn = document.getElementById('addTaskBtn');
            const addTaskModal = document.getElementById('addTaskModal');
            const closeAddTaskModalBtn = document.getElementById('closeAddTaskModalBtn');
            addTaskBtn.onclick = () => {
                addTaskModal.classList.remove('hidden');
                addTaskModal.classList.add('flex', 'items-center', 'justify-center');
            };
            closeAddTaskModalBtn.onclick = () => {
                addTaskModal.classList.add('hidden');
                addTaskModal.classList.remove('flex', 'items-center', 'justify-center');
            };

            const editTaskModal = document.getElementById('editTaskModal');
            const closeEditTaskModalBtn = document.getElementById('closeEditTaskModalBtn');
            const editTaskForm = document.getElementById('editTaskForm');
            closeEditTaskModalBtn.onclick = () => {
                editTaskModal.classList.add('hidden');
                editTaskModal.classList.remove('flex', 'items-center', 'justify-center');
            };

            function openEditTaskModal(id, name, description) {
                editTaskForm.action = `/edit-task/${id}`;
                document.getElementById('editTaskName').value = name;
                document.getElementById('editTaskDescription').value = description;
                editTaskModal.classList.remove('hidden');
                editTaskModal.classList.add('flex', 'items-center', 'justify-center');
            }
        </script>
    </div>
    @else
    {{-- Login/Register --}}
    <div class="w-full max-w-sm p-8 rounded-2xl shadow-2xl flex flex-col items-center bg-gradient-to-b from-red-600 to-red-400 border-2 border-red-700">
        <h2 class="text-center text-3xl font-extrabold text-white mb-8 tracking-wide drop-shadow-lg">OSOlink Portal</h2>
        
        {{-- Login Form --}}
        <form action="/login" method="POST" class="space-y-6 w-full">
            @csrf
            <input name="username" type="text" placeholder="Username" required
                class="w-full px-5 py-3 border-2 border-white rounded-xl bg-white bg-opacity-80 text-red-700 font-semibold placeholder-red-400 shadow focus:outline-none focus:ring-2 focus:ring-white">
            <input name="password" type="password" placeholder="Password" required
                class="w-full px-5 py-3 border-2 border-white rounded-xl bg-white bg-opacity-80 text-red-700 font-semibold placeholder-red-400 shadow focus:outline-none focus:ring-2 focus:ring-white">
            <button type="submit"
                class="w-full py-3 px-5 bg-white bg-opacity-90 text-red-700 font-bold rounded-xl shadow-lg hover:bg-red-100 hover:text-red-900 transition">Login</button>
        </form>

        {{-- Register Form --}}
        <form action="/register" method="POST" class="space-y-6 w-full mt-6">
            @csrf
            <input name="name" type="text" placeholder="Username" required
                class="w-full px-5 py-3 border-2 border-white rounded-xl bg-white bg-opacity-80 text-red-700 font-semibold placeholder-red-400 shadow focus:outline-none focus:ring-2 focus:ring-white">
            <input name="email" type="email" placeholder="Email" required
                class="w-full px-5 py-3 border-2 border-white rounded-xl bg-white bg-opacity-80 text-red-700 font-semibold placeholder-red-400 shadow focus:outline-none focus:ring-2 focus:ring-white">
            <input name="password" type="password" placeholder="Password" required
                class="w-full px-5 py-3 border-2 border-white rounded-xl bg-white bg-opacity-80 text-red-700 font-semibold placeholder-red-400 shadow focus:outline-none focus:ring-2 focus:ring-white">
            <button type="submit"
                class="w-full py-3 px-5 bg-red-700 text-white font-bold rounded-xl shadow-lg hover:bg-red-800 transition">Register</button>
        </form>
        <p class="mt-6 text-xs text-white text-opacity-70 text-center select-none">© {{ date('Y') }} OSOlink. All rights reserved.</p>
    </div>
    @endauth
</body>
</html>