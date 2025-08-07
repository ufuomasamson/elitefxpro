<!-- Language Selection Modal -->
<div id="languageModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900">
                <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mt-4">
                {{ __('messages.select_language') }}
            </h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ __('messages.language_detection') }}
                </p>
            </div>
            
            <!-- Language Options -->
            <div class="mt-4 space-y-2">
                <div class="grid grid-cols-1 gap-2">
                    <!-- Detected Language Option -->
                    <button id="switchToDetected" 
                            data-language="" 
                            class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition duration-200 flex items-center justify-center">
                        <span class="flag-icon mr-2"></span>
                        <span class="language-name"></span>
                    </button>
                    
                    <!-- Continue in English -->
                    <button id="continueEnglish" 
                            class="w-full px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md transition duration-200 flex items-center justify-center">
                        <span class="flag-icon flag-icon-us mr-2"></span>
                        {{ __('messages.continue_english') }}
                    </button>
                </div>
                
                <!-- Other Language Options -->
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ __('messages.language') }}:</p>
                    <div class="grid grid-cols-2 gap-1">
                        <button class="language-option px-2 py-1 text-sm bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded flex items-center" data-language="en">
                            <span class="flag-icon flag-icon-us mr-1"></span>
                            English
                        </button>
                        <button class="language-option px-2 py-1 text-sm bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded flex items-center" data-language="it">
                            <span class="flag-icon flag-icon-it mr-1"></span>
                            Italiano
                        </button>
                        <button class="language-option px-2 py-1 text-sm bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded flex items-center" data-language="fr">
                            <span class="flag-icon flag-icon-fr mr-1"></span>
                            Français
                        </button>
                        <button class="language-option px-2 py-1 text-sm bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded flex items-center" data-language="de">
                            <span class="flag-icon flag-icon-de mr-1"></span>
                            Deutsch
                        </button>
                        <button class="language-option px-2 py-1 text-sm bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded flex items-center" data-language="ru">
                            <span class="flag-icon flag-icon-ru mr-1"></span>
                            Русский
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Language Modal JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const languageModal = document.getElementById('languageModal');
    const switchToDetected = document.getElementById('switchToDetected');
    const continueEnglish = document.getElementById('continueEnglish');
    const languageOptions = document.querySelectorAll('.language-option');
    
    // Language names mapping
    const languageNames = {
        'en': 'English',
        'it': 'Italiano', 
        'fr': 'Français',
        'de': 'Deutsch',
        'ru': 'Русский'
    };
    
    // Flag icons mapping
    const flagIcons = {
        'en': 'flag-icon-us',
        'it': 'flag-icon-it',
        'fr': 'flag-icon-fr', 
        'de': 'flag-icon-de',
        'ru': 'flag-icon-ru'
    };
    
    // Check if user has already set a language preference
    if (!localStorage.getItem('language_preference_set') && !sessionStorage.getItem('language_modal_dismissed')) {
        // Detect browser language
        fetch('{{ route("language.detect") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.detected && data.detected !== 'en') {
                // Set up the detected language button
                switchToDetected.setAttribute('data-language', data.detected);
                switchToDetected.querySelector('.language-name').textContent = languageNames[data.detected];
                switchToDetected.querySelector('.flag-icon').className = 'flag-icon ' + flagIcons[data.detected] + ' mr-2';
                
                // Show the modal
                languageModal.classList.remove('hidden');
            }
        })
        .catch(error => {
            console.log('Language detection failed:', error);
        });
    }
    
    // Handle language switching
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
                localStorage.setItem('language_preference_set', 'true');
                languageModal.classList.add('hidden');
                // Reload page to apply new language
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Language switch failed:', error);
        });
    }
    
    // Event listeners
    switchToDetected.addEventListener('click', function() {
        const language = this.getAttribute('data-language');
        switchLanguage(language);
    });
    
    continueEnglish.addEventListener('click', function() {
        sessionStorage.setItem('language_modal_dismissed', 'true');
        languageModal.classList.add('hidden');
    });
    
    languageOptions.forEach(option => {
        option.addEventListener('click', function() {
            const language = this.getAttribute('data-language');
            switchLanguage(language);
        });
    });
    
    // Close modal when clicking outside
    languageModal.addEventListener('click', function(e) {
        if (e.target === languageModal) {
            sessionStorage.setItem('language_modal_dismissed', 'true');
            languageModal.classList.add('hidden');
        }
    });
});
</script>

<!-- Flag Icons CSS (add this to your main layout if not already included) -->
<style>
.flag-icon {
    width: 1.33em;
    height: 1em;
    background-size: contain;
    background-position: 50%;
    background-repeat: no-repeat;
    display: inline-block;
}

.flag-icon-us {
    background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjUwIiB2aWV3Qm94PSIwIDAgMTAwIDUwIiBmaWxsPSJub25lIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgo8cmVjdCB3aWR0aD0iMTAwIiBoZWlnaHQ9IjUwIiBmaWxsPSIjQjIyMjM0Ii8+CjxyZWN0IHdpZHRoPSIxMDAiIGhlaWdodD0iMy44NDYxNSIgZmlsbD0id2hpdGUiLz4KPHJlY3QgeT0iNy42OTIzMSIgd2lkdGg9IjEwMCIgaGVpZ2h0PSIzLjg0NjE1IiBmaWxsPSJ3aGl0ZSIvPgo8L3N2Zz4K');
}

.flag-icon-it {
    background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjUwIiB2aWV3Qm94PSIwIDAgMTAwIDUwIiBmaWxsPSJub25lIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgo8cmVjdCB3aWR0aD0iMzMuMzMiIGhlaWdodD0iNTAiIGZpbGw9IiMwMDk2NDYiLz4KPHJlY3QgeD0iMzMuMzMiIHdpZHRoPSIzMy4zNCIgaGVpZ2h0PSI1MCIgZmlsbD0id2hpdGUiLz4KPHJlY3QgeD0iNjYuNjciIHdpZHRoPSIzMy4zMyIgaGVpZ2h0PSI1MCIgZmlsbD0iI0NFMkIzNyIvPgo8L3N2Zz4K');
}

.flag-icon-fr {
    background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjUwIiB2aWV3Qm94PSIwIDAgMTAwIDUwIiBmaWxsPSJub25lIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgo8cmVjdCB3aWR0aD0iMzMuMzMiIGhlaWdodD0iNTAiIGZpbGw9IiMwMDJBOEYiLz4KPHJlY3QgeD0iMzMuMzMiIHdpZHRoPSIzMy4zNCIgaGVpZ2h0PSI1MCIgZmlsbD0id2hpdGUiLz4KPHJlY3QgeD0iNjYuNjciIHdpZHRoPSIzMy4zMyIgaGVpZ2h0PSI1MCIgZmlsbD0iI0VGMzMzOSIvPgo8L3N2Zz4K');
}

.flag-icon-de {
    background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjUwIiB2aWV3Qm94PSIwIDAgMTAwIDUwIiBmaWxsPSJub25lIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgo8cmVjdCB3aWR0aD0iMTAwIiBoZWlnaHQ9IjE2LjY3IiBmaWxsPSIjMDAwMDAwIi8+CjxyZWN0IHk9IjE2LjY3IiB3aWR0aD0iMTAwIiBoZWlnaHQ9IjE2LjY2IiBmaWxsPSIjRkYwMDAwIi8+CjxyZWN0IHk9IjMzLjMzIiB3aWR0aD0iMTAwIiBoZWlnaHQ9IjE2LjY3IiBmaWxsPSIjRkZDQzAwIi8+Cjwvc3ZnPgo=');
}

.flag-icon-ru {
    background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjUwIiB2aWV3Qm94PSIwIDAgMTAwIDUwIiBmaWxsPSJub25lIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgo8cmVjdCB3aWR0aD0iMTAwIiBoZWlnaHQ9IjE2LjY3IiBmaWxsPSJ3aGl0ZSIvPgo8cmVjdCB5PSIxNi42NyIgd2lkdGg9IjEwMCIgaGVpZ2h0PSIxNi42NiIgZmlsbD0iIzAwNTJCNCIvPgo8cmVjdCB5PSIzMy4zMyIgd2lkdGg9IjEwMCIgaGVpZ2h0PSIxNi42NyIgZmlsbD0iI0Q1MkIzMSIvPgo8L3N2Zz4K');
}
</style>
