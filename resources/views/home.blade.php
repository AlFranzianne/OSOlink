<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie-edge">
        <title>OSOlink | Home</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="flex items-center justify-center min-h-screen">
        @auth
        {{-- Display task management dashboard if authenticated --}}
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
        <div class="pt-16"></div> {{-- Push content below navbar --}}

        <div class="p-10 w-full">
            <div class="flex justify-between items-center mb-6 w-full">
                <h2 class="text-2xl font-bold text-red-700 text-left">Projects</h2>
                <button id="addTaskBtn"
                class="px-4 py-2 bg-red-600 text-white rounded shadow hover:bg-red-700 transition font-semibold">
                    + Add Task
                </button>
            </div>
            {{-- Populates table with tasks with update and delete buttons --}}
            <ul>
                <li>
                    <div class="flex flex-col md:flex-row items-center justify-between mb-4">
                        <span class="font-semibold text-red-700 w-1/2">Task Name</span>
                        <span class="text-gray-700 w-1/2">Description</span>
                    </div>
                @foreach($tasks as $task)
                    <li class="mb-2 p-4 bg-gray-100 rounded shadow flex items-center justify-between">
                        <div class="flex flex-col md:flex-row md:items-center gap-4 w-full">
                            <span class="font-semibold text-red-700 w-1/2">{{ $task->name }}</span>
                            <span class="text-gray-700 w-1/2">{{ $task->description }}</span>
                        </div>
                        <button onclick="openEditTaskModal({{ $task->id }}, '{{ addslashes($task->name) }}', '{{ addslashes($task->description) }}')"
                        class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition text-sm font-semibold">Edit</button>
                        <form action="/delete-task/{{ $task->id }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition text-sm font-semibold">Delete</button>
                        </form>
                    </li>
                @endforeach
            </ul>
            {{-- Add Task Modal --}}
            <div id="addTaskModal" class="fixed inset-0 bg-black bg-opacity-40 items-center justify-center z-50 hidden">
                <div class="bg-white rounded-xl shadow-xl p-8 w-full max-w-md">
                    <h3 class="text-xl font-bold mb-6 text-red-700">Add Task</h3>
                    <form action="/add-task" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-semibold mb-2 text-red-700">Name</label>
                            <input type="text" name="name" placeholder="My Task" required
                            class="w-full px-4 py-2 border-2 border-red-300 rounded focus:outline-none focus:ring-2 focus:ring-red-400">
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-semibold mb-2 text-red-700">Description</label>
                            <input type="text" name="description" placeholder="To Do" required
                            class="w-full px-4 py-2 border-2 border-red-300 rounded focus:outline-none focus:ring-2 focus:ring-red-400">
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" id="closeAddTaskModalBtn"
                            class="px-4 py-2 rounded bg-gray-200 text-red-700 font-semibold hover:bg-gray-300 transition">Cancel</button>
                            <button type="submit"
                            class="px-4 py-2 rounded bg-red-600 text-white font-semibold hover:bg-red-700 transition">Save</button>
                        </div>
                    </form>
                </div>
            </div>
            {{-- Edit Task Modal --}}
            <div id="editTaskModal" class="fixed inset-0 bg-black bg-opacity-40 items-center justify-center z-50 hidden">
                <div class="bg-white rounded-xl shadow-xl p-8 w-full max-w-md">
                    <h3 class="text-xl font-bold mb-6 text-red-700">Edit Task</h3>
                    <form id="editTaskForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label class="block text-sm font-semibold mb-2 text-red-700">Name</label>
                            <input type="text" name="name" id="editTaskName" required class="w-full px-4 py-2 border-2 border-red-300 rounded focus:outline-none focus:ring-2 focus:ring-red-400">
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-semibold mb-2 text-red-700">Description</label>
                            <input type="text" name="description" id="editTaskDescription" required class="w-full px-4 py-2 border-2 border-red-300 rounded focus:outline-none focus:ring-2 focus:ring-red-400">
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" id="closeEditTaskModalBtn"
                            class="px-4 py-2 rounded bg-gray-200 text-red-700 font-semibold hover:bg-gray-300 transition">Cancel</button>
                            <button type="submit"
                            class="px-4 py-2 rounded bg-red-600 text-white font-semibold hover:bg-red-700 transition">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        <script>
            // Add Task Modal Script
            const addTaskBtn = document.getElementById('addTaskBtn');
            const addTaskModal = document.getElementById('addTaskModal');
            const closeAddTaskModalBtn = document.getElementById('closeAddTaskModalBtn');
            addTaskBtn.onclick = () => addTaskModal.classList.remove('hidden');
            closeAddTaskModalBtn.onclick = () => addTaskModal.classList.add('hidden');

            // Edit Task Modal Script
            const editTaskModal = document.getElementById('editTaskModal');
            const closeEditTaskModalBtn = document.getElementById('closeEditTaskModalBtn');
            closeEditTaskModalBtn.onclick = () => editTaskModal.classList.add('hidden');

            // Open edit task modal with task data
            const editTaskForm = document.getElementById('editTaskForm');
            function openEditTaskModal(id, name, description) {
                editTaskForm.action = `/edit-task/${id}`;
                document.getElementById('editTaskName').value = name;
                document.getElementById('editTaskDescription').value = description;
                editTaskModal.classList.remove('hidden');
                editTaskModal.classList.add('flex');
            }
        </script>
        </div>
        @else
        {{-- Display login form if not authenticated --}}
        <div class="w-full max-w-sm p-8 rounded-2xl shadow-2xl flex flex-col items-center bg-gradient-to-b from-red-600 to-red-400 border-2 border-red-700">
            <h2 class="text-center text-3xl font-extrabold text-white mb-8 tracking-wide drop-shadow-lg">OSOlink Portal</h2>
            {{-- Login Form --}}
            <form action="/login" method="POST" class="space-y-6 w-full">
                @csrf
                <input name="username" type="text" placeholder="Username" required
                class="w-full px-5 py-3 border-2 border-white rounded-xl focus:outline-none focus:ring-2 focus:ring-white bg-white bg-opacity-80 text-red-700 font-semibold placeholder-red-400 shadow">
                <input name="password" type="password" placeholder="Password" required
                class="w-full px-5 py-3 border-2 border-white rounded-xl focus:outline-none focus:ring-2 focus:ring-white bg-white bg-opacity-80 text-red-700 font-semibold placeholder-red-400 shadow">
                <button type="submit"
                class="w-full py-3 px-5 bg-white bg-opacity-90 text-red-700 font-bold rounded-xl shadow-lg hover:bg-red-100 hover:text-red-900 transition">Login</button>
            </form>
            {{-- Registration Form --}}
            <form action="/register" method="POST" class="space-y-6 w-full">
                @csrf
                <input name="name" type="text" placeholder="Username" required
                class="w-full px-5 py-3 border-2 border-white rounded-xl focus:outline-none focus:ring-2 focus:ring-white bg-white bg-opacity-80 text-red-700 font-semibold placeholder-red-400 shadow">
                <input name="email" type="email" placeholder="Email" required
                class="w-full px-5 py-3 border-2 border-white rounded-xl focus:outline-none focus:ring-2 focus:ring-white bg-white bg-opacity-80 text-red-700 font-semibold placeholder-red-400 shadow">
                <input name="password" type="password" placeholder="Password" required
                class="w-full px-5 py-3 border-2 border-white rounded-xl focus:outline-none focus:ring-2 focus:ring-white bg-white bg-opacity-80 text-red-700 font-semibold placeholder-red-400 shadow">
                <button type="submit"
                class="w-full py-3 px-5 bg-red-700 text-white font-bold rounded-xl shadow-lg hover:bg-red-800 transition">Register</button>
            </form>
            <p class="mt-6 text-xs text-white text-opacity-70 text-center select-none">© {{ date('Y') }} OSOlink. All rights reserved.</p>
        </div>
        @endauth
    </body>
</html>