<!-- Language Switcher Dropdown -->
<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" 
            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
        <!-- Current Language Flag -->
        <span class="flag-icon flag-icon-{{ app()->getLocale() === 'en' ? 'us' : app()->getLocale() }} mr-2"></span>
        
        <!-- Current Language Name -->
        @switch(app()->getLocale())
            @case('en')
                English
                @break
            @case('it')
                Italiano
                @break
            @case('fr')
                Français
                @break
            @case('de')
                Deutsch
                @break
            @case('ru')
                Русский
                @break
            @default
                English
        @endswitch
        
        <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
    </button>

    <!-- Dropdown Menu -->
    <div x-show="open" 
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute right-0 z-50 mt-2 w-48 origin-top-right rounded-md bg-white dark:bg-gray-800 py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
        
        <!-- Language Options -->
        <a href="#" 
           onclick="switchLanguage('en')"
           class="language-option flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ app()->getLocale() === 'en' ? 'bg-gray-50 dark:bg-gray-700' : '' }}">
            <span class="flag-icon flag-icon-us mr-3"></span>
            English
            @if(app()->getLocale() === 'en')
                <svg class="ml-auto h-4 w-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
            @endif
        </a>
        
        <a href="#" 
           onclick="switchLanguage('it')"
           class="language-option flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ app()->getLocale() === 'it' ? 'bg-gray-50 dark:bg-gray-700' : '' }}">
            <span class="flag-icon flag-icon-it mr-3"></span>
            Italiano
            @if(app()->getLocale() === 'it')
                <svg class="ml-auto h-4 w-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
            @endif
        </a>
        
        <a href="#" 
           onclick="switchLanguage('fr')"
           class="language-option flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ app()->getLocale() === 'fr' ? 'bg-gray-50 dark:bg-gray-700' : '' }}">
            <span class="flag-icon flag-icon-fr mr-3"></span>
            Français
            @if(app()->getLocale() === 'fr')
                <svg class="ml-auto h-4 w-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
            @endif
        </a>
        
        <a href="#" 
           onclick="switchLanguage('de')"
           class="language-option flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ app()->getLocale() === 'de' ? 'bg-gray-50 dark:bg-gray-700' : '' }}">
            <span class="flag-icon flag-icon-de mr-3"></span>
            Deutsch
            @if(app()->getLocale() === 'de')
                <svg class="ml-auto h-4 w-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
            @endif
        </a>
        
        <a href="#" 
           onclick="switchLanguage('ru')"
           class="language-option flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ app()->getLocale() === 'ru' ? 'bg-gray-50 dark:bg-gray-700' : '' }}">
            <span class="flag-icon flag-icon-ru mr-3"></span>
            Русский
            @if(app()->getLocale() === 'ru')
                <svg class="ml-auto h-4 w-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
            @endif
        </a>
    </div>
</div>

<!-- Language Switcher JavaScript -->
<script>
function switchLanguage(language) {
    fetch('{{ route("language.switch") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ language: language })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            if (typeof showNotification === 'function') {
                showNotification('{{ __("messages.language_changed") }}', 'success');
            }
            
            // Reload page to apply new language
            setTimeout(() => {
                window.location.reload();
            }, 500);
        }
    })
    .catch(error => {
        console.error('Language switch failed:', error);
        if (typeof showNotification === 'function') {
            showNotification('{{ __("messages.operation_failed") }}', 'error');
        }
    });
}
</script>
