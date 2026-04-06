<x-layouts::app :title="__('candidate.notifications')">
   <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
      <div class="relative h-full flex-1 overflow-y-scroll rounded-xl border border-neutral-200 dark:border-neutral-700">
         <div class="max-w-7xl mx-6 my-6 mx-auto px-6">
            <flux:heading class="text-center my-3" size="xl">{{ __('candidate.notifications') }}</flux:heading>
            notifications
         </div>
      </div>
   </div>
</x-layouts::app>