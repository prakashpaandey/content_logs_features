@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => 'border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-lg shadow-sm px-3 py-2']) }}>
