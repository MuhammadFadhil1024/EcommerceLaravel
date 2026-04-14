<div x-data="{ show: false, message: '' }" 
     x-on:notify.window="
        show = true; 
        message = $event.detail.message; 
        setTimeout(() => show = false, 3000)
     "
     x-show="show" 
     style="display: none;"
     x-transition.opacity.duration.300ms
     class="fixed bottom-5 right-5 z-50 flex items-center w-full max-w-xs p-4 space-x-3 text-zinc-700 bg-white border-l-4 border-green-500 rounded-lg shadow-xl dark:text-zinc-300 dark:bg-zinc-800" 
     role="alert">
    
    <div class="inline-flex items-center justify-center shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg dark:bg-green-500/20 dark:text-green-400">
        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
        </svg>
    </div>
    
    <div class="ml-3 text-sm font-medium" x-text="message"></div>
    
    <button @click="show = false" type="button" class="ml-auto -mx-1.5 -my-1.5 text-zinc-400 hover:text-zinc-900 rounded-lg p-1.5 hover:bg-zinc-100 inline-flex items-center justify-center h-8 w-8 dark:text-zinc-500 dark:hover:text-white dark:hover:bg-zinc-700 transition-colors" aria-label="Close">
        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
        </svg>
    </button>
</div>