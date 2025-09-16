<x-app-layout>
    <!-- CSS to center activate/deactivate button -->
    <style>
        td, th {
            text-align: center !important;
            vertical-align: middle;
        }
    </style>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div>
                    @include('adminpanel.partials.create')
                </div>
            </div>
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div>
                    @include('adminpanel.partials.manage')
                </div>
            </div>
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div>
                    @include('adminpanel.partials.audit')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>